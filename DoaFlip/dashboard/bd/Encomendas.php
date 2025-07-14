<?php
require_once "Connection.php";
require_once "EncomendasProdutos.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/paprique/DoaFlip/vendor/autoload.php'; // Caminho para o autoload do PHPMailer

class Encomendas extends Connection
{
    protected $conn;

    public function __construct()
    {
        $this->conn = $this->connect();
    }

    public function create(array $dados, float $total): int|false
    {
        try {
            $this->conn->beginTransaction();

            // 1. Criar encomenda
            $sql = "INSERT INTO encomendas 
                (id_status_encomendas, id_utilizador, valor_total, metodo_pagamento, id_metodo_pagamento, observacoes) 
                VALUES (1, :id_utilizador, :valor_total, :metodo_pagamento, :id_metodo_pagamento, :observacoes)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_utilizador', $dados['id_utilizador'], PDO::PARAM_INT);
            $stmt->bindValue(':valor_total', $total, PDO::PARAM_STR);
            $stmt->bindValue(':metodo_pagamento', $dados['metodo_pagamento'], PDO::PARAM_STR);
            $stmt->bindValue(':id_metodo_pagamento', $this->getMetodoPagamentoId($dados['metodo_pagamento']), PDO::PARAM_INT);
            $stmt->bindValue(':observacoes', $dados['observacoes'] ?? null, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao criar encomenda: " . implode(", ", $stmt->errorInfo()));
            }

            $id_encomenda = $this->conn->lastInsertId();

            // 2. Inserir produtos da encomenda
            if (isset($dados['produtos']) && is_array($dados['produtos'])) {
                $encomendasProdutos = new EncomendasProdutos();
                foreach ($dados['produtos'] as $produto) {
                    $produtoData = [
                        'id_encomenda' => $id_encomenda,
                        'id_produto' => $produto['id_produto'],
                        'quantidade' => $produto['quantidade'],
                        'preco_unitario' => $produto['preco']
                    ];
                    if (!$encomendasProdutos->create($produtoData)) {
                        throw new Exception("Erro ao adicionar produto à encomenda");
                    }
                }
            }

            // 3. Atualizar usuário
            $sql = "UPDATE utilizador SET 
            morada = COALESCE(:morada, morada),
            codigo_postal = COALESCE(:codigo_postal, codigo_postal),
            telefone = COALESCE(:telefone, telefone)
            WHERE id_utilizador = :id_utilizador";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':morada', $dados['morada'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':codigo_postal', $dados['codigo_postal'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':telefone', $dados['telefone'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':id_utilizador', $dados['id_utilizador'], PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();

            // Enviar email APÓS commit para garantir que todos os dados estão no banco
            $this->sendConfirmationEmail($dados['id_utilizador'], $id_encomenda, $total, $dados['metodo_pagamento']);

            return $id_encomenda;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    private function sendConfirmationEmail(int $id_utilizador, int $id_encomenda, float $total, string $metodo_pagamento): bool
    {
        $mailConfig = require 'mail_config.php';

        // Obter dados do utilizador
        $utilizador = new Utilizador();
        $utilizador->setId($id_utilizador);
        $userData = $utilizador->view();

        // Obter produtos da encomenda
        $encomendasProdutos = new EncomendasProdutos();
        $produtos = $encomendasProdutos->getByEncomenda($id_encomenda);

        // Criar lista de produtos em HTML
        $produtosHtml = '';
        foreach ($produtos as $produto) {
            $produtosHtml .= "
            <tr>
                <td>{$produto['nome_produto']}</td>
                <td>{$produto['quantidade']}</td>
                <td>€ " . number_format($produto['preco_unitario'], 2, ',', '.') . "</td>
                <td>€ " . number_format($produto['quantidade'] * $produto['preco_unitario'], 2, ',', '.') . "</td>
            </tr>
        ";
        }


        // Corpo do email
        $emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #4f46e5;
            color: #fff;
            padding: 25px;
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 25px;
        }
        .content p {
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 14px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
            padding: 12px;
        }
        td {
            padding: 12px;
            border-top: 1px solid #e0e0e0;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f9fafb;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #888;
        }
        @media (max-width: 600px) {
            .content, .header, .footer {
                padding: 15px;
            }
            table th, table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Pedido Realizado com Sucesso</h2>
        </div>
        <div class='content'>
            <p>Olá {$userData['nome_completo']},</p>
            <p>Recebemos a sua encomenda com sucesso. Aqui estão os detalhes:</p>
            
            <h3>Detalhes da Encomenda</h3>
            <table>
                <tr>
                   
                </tr>
                <tr>
                    <th>Data</th>
                    <td>" . date('d/m/Y H:i') . "</td>
                </tr>
                <tr>
                   <th>Método de Pagamento</th>
            <td>{$metodo_pagamento}</td>
                </tr>
            </table>
            
            <h3>Produtos</h3>
            <table>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
                {$produtosHtml}
                <tr class='total-row'>
                    <td colspan='3' style='text-align:right;'>Total da Encomenda:</td>
                    <td>€ " . number_format($total, 2, ',', '.') . "</td>
                </tr>
            </table>
            
            <p>Obrigado por comprar conosco! 🛒</p>
            <p>— A equipa DoaFlip</p>
        </div>
        <div class='footer'>
            <p>Se tiver alguma dúvida, é só responder a este email.</p>
        </div>
    </div>
</body>
</html>
";

        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = $mailConfig['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailConfig['smtp']['username'];
            $mail->Password = $mailConfig['smtp']['password'];
            $mail->SMTPSecure = $mailConfig['smtp']['encryption'];
            $mail->Port = $mailConfig['smtp']['port'];

            if ($mailConfig['debug'] > 0) {
                $mail->SMTPDebug = $mailConfig['debug'];
                $mail->Debugoutput = function ($str, $level) {
                    error_log("PHPMailer: $str");
                };
            }

            // Remetente e destinatário
            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($userData['email'], $userData['nome_completo']);

            // Conteúdo do email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmação de Encomenda - DoaFlip';
            $mail->Body = $emailBody;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $emailBody));

            if (!$mail->send()) {
                throw new Exception("Erro ao enviar email: " . $mail->ErrorInfo);
            }

            error_log("Email enviado com sucesso para: " . $userData['email']);
            return true;
        } catch (Exception $e) {
            error_log("ERRO AO ENVIAR EMAIL: " . $e->getMessage());
            return false;
        }
    }

    private function getMetodoPagamentoId(string $metodo): int
    {
        $sql = "SELECT id_metodo_pagamento FROM metodo_pagamento WHERE metodo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$metodo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id_metodo_pagamento'] ?? 1; // Default to MBWay if not found
    }

    public function getByUser(int $id_utilizador): array
    {
        $sql = "SELECT e.*, s.status 
                FROM encomendas e
                JOIN status_encomendas s ON e.id_status_encomendas = s.id_status_encomendas
                WHERE e.id_utilizador = ?
                ORDER BY e.data_encomenda DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_utilizador]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

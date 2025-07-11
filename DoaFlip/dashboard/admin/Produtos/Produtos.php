<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */
ob_start(); // Inicia o buffer de saída para capturar mensagens de erro e redirecionamentos
require_once './bd/Connection.php';
class Produtos extends Connection
{

    /**
     * Conexão com o banco de dados.
     * @var object
     */
    public object $conn;

    /**
     * Dados do formulário para criação e edição de um novo categoria.
     * @var array
     */
    public array $formData;

    /**
     * ID do categoria para operações específicas (visualização e edição).
     * @var int
     */
    public int $id;

    /**
     * Define os dados do formulário para criação de um novo categoria.
     * 
     * @param array $formData Dados do formulário contendo informações do categoria.
     * @return void
     */
    public function setFormData(array $formData): void
    {
        // Atribui os dados do formulário à propriedade formData.
        $this->formData = $formData;
    }

    /**
     * Define o ID do categoria para operações que necessitam de um identificador específico.
     * 
     * @param int $id Identificador único do categoria.
     * @return void
     */
    public function setId(int $id_produto): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_produto;
    }

    /** 
     * Lista os categorias cadastrados no banco de dados.
     * 
     * @return array Retorna um array contendo os dados dos categorias.
     */
    public function list(): array
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados dos categorias, limitando o resultado a 40 registros.
        $sql = "SELECT P.*, I.link_imagem FROM produtos P LEFT JOIN imagens I ON I.id_produto = P.id_produto ORDER BY P.id_produto DESC;";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }


    public function create(): bool
    {
        $this->conn = $this->connect();

        try {
            // Debug: Verificar conexão
            error_log("Tentando conectar ao banco de dados");

            // Iniciar transação
            $this->conn->beginTransaction();
            error_log("Transação iniciada");

            // 1. Verificar ou criar marca
            $marca = trim($this->formData['marca']);
            $stmt = $this->conn->prepare("SELECT id_marca FROM marca WHERE nome_marca = ?");
            $stmt->execute([$marca]);
            $marca_existente = $stmt->fetch();

            if ($marca_existente) {
                $id_marca = $marca_existente['id_marca'];
            } else {
                $stmt = $this->conn->prepare("INSERT INTO marca (nome_marca) VALUES (?)");
                $stmt->execute([$marca]);
                $id_marca = $this->conn->lastInsertId();
            }

            // 1. Inserir imagem se existir
            $id_imagem = null;
            if (!empty($this->formData['link_imagem'])) {
                $sql = "INSERT INTO imagens (link_imagem) VALUES (?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$this->formData['link_imagem']]);
                $id_imagem = $this->conn->lastInsertId();
                error_log("Imagem inserida com ID: " . $id_imagem);
            }

            // 2. Inserir produto
            $sql = "INSERT INTO produtos (
            id_categoria, id_subcategoria, id_imagem, 
            id_marca, nome_produto, descricao, preco
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                $this->formData['id_categoria'],
                $this->formData['id_subcategoria'],
                $id_imagem,
                $id_marca,
                $this->formData['nome_produto'],
                $this->formData['descricao'],
                $this->formData['preco']
            ]);

            if (!$success) {
                throw new Exception("Erro ao inserir produto: " . implode(", ", $stmt->errorInfo()));
            }

            $id_produto = $this->conn->lastInsertId();
            error_log("Produto inserido com ID: " . $id_produto);

            // 3. Atualizar imagem com ID do produto
            if ($id_imagem) {
                $sql = "UPDATE imagens SET id_produto = ? WHERE id_imagem = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id_produto, $id_imagem]);
                error_log("Imagem atualizada com ID do produto");
            }

            // Commit
            $this->conn->commit();
            error_log("Transação concluída com sucesso");
            return true;
        } catch (Exception $e) {
            error_log("ERRO: " . $e->getMessage());
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
                error_log("Transação revertida");
            }
            return false;
        }
    }

    public function listMarcas(): array
    {
        $this->conn = $this->connect();
        $sql = "SELECT DISTINCT M.id_marca, M.nome_marca 
            FROM marca M
            JOIN produtos P ON P.id_marca = M.id_marca
            ORDER BY M.nome_marca";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lista produtos por marca
     * @param int $id_marca
     * @return array
     */
    public function listPorMarca(int $id_marca): array
    {
        $this->conn = $this->connect();
        $sql = "SELECT P.*, I.link_imagem 
            FROM produtos P
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            WHERE P.id_marca = ?
            ORDER BY P.nome_produto";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_marca]);
        return $stmt->fetchAll();
    }

    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um categoria específico.
        $sql = "SELECT * FROM produtos
                WHERE id_produto = :id_produto";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_produto', $this->id);

        // Executa a consulta SQL.
        $resultUser->execute();

        // Retorna os dados do categoria ou false se não encontrado.
        return $resultUser->fetch();
    }

    /**
     * Edita as informações de um categoria existente.
     * 
     * @return bool Retorna true se o categoria for atualizado com sucesso, false caso contrário.
     */
    public function edit(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        try {
            // Iniciar transação
            $this->conn->beginTransaction();

            // 1. Verificar ou criar marca
            $marca = trim($this->formData['marca']);
            $stmt = $this->conn->prepare("SELECT id_marca FROM marca WHERE nome_marca = ?");
            $stmt->execute([$marca]);
            $marca_existente = $stmt->fetch();

            if ($marca_existente) {
                $id_marca = $marca_existente['id_marca'];
            } else {
                $stmt = $this->conn->prepare("INSERT INTO marca (nome_marca) VALUES (?)");
                $stmt->execute([$marca]);
                $id_marca = $this->conn->lastInsertId();
            }

            // Consulta SQL para atualizar os dados do produto
            $sql = "UPDATE produtos SET 
            nome_produto = :nome_produto,
            descricao = :descricao,
            preco = :preco,
            id_categoria = :id_categoria,
            id_subcategoria = :id_subcategoria,
            id_marca = :id_marca,
            id_imagem = :id_imagem
            WHERE id_produto = :id_produto";

            // Prepara a consulta SQL.
            $editProduto = $this->conn->prepare($sql);

            // Associa os valores das propriedades ao SQL.
            $editProduto->bindValue(':nome_produto', $this->formData['nome_produto']);
            $editProduto->bindValue(':descricao', $this->formData['descricao']);
            $editProduto->bindValue(':preco', $this->formData['preco']);
            $editProduto->bindValue(':id_categoria', $this->formData['id_categoria']);
            $editProduto->bindValue(':id_subcategoria', $this->formData['id_subcategoria']);
            $editProduto->bindValue(':id_marca', $id_marca); // Usando o id_marca obtido acima
            $editProduto->bindValue(':id_imagem', $this->formData['id_imagem'] ?? null);
            $editProduto->bindValue(':id_produto', $this->formData['id_produto']);

            // Executa a consulta SQL.
            $editProduto->execute();

            // Commit da transação
            $this->conn->commit();

            return $editProduto->rowCount() > 0;
        } catch (Exception $e) {
            // Rollback em caso de erro
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Erro ao editar produto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui um categoria do banco de dados.
     * 
     * @return bool Retorna true se o categoria for excluído com sucesso, false caso contrário.
     */
    public function delete(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para excluir um status de encomenda específico baseado no seu ID.
        $sql = "DELETE FROM produtos WHERE id_produto = :id_produto LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_produto', $this->id);

        // Executa a consulta SQL.
        return $deleteUser->execute();
    }

    public function getById($id)
    {
        $this->conn = $this->connect();
        $sql = "SELECT P.*, I.link_imagem, M.nome_marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            WHERE P.id_produto = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

<?php
require_once "Connection.php";

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

            // 2. Atualizar usuário apenas se os dados forem diferentes
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
            return $id_encomenda;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
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
<?php
require_once "Connection.php";

class MetodosPagamento extends Connection
{
    protected $conn;

    public function __construct()
    {
        $this->conn = $this->connect();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM metodo_pagamento";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM metodo_pagamento WHERE id_metodo_pagamento = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
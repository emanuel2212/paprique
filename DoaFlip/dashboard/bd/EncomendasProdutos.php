<?php
require_once "Connection.php";

class EncomendasProdutos extends Connection
{
    protected $conn;

    public function __construct()
    {
        $this->conn = $this->connect();
    }

    public function create(array $dados): bool
    {
        $sql = "INSERT INTO encomendas_produtos 
                (id_encomenda, id_produto, quantidade, preco_unitario) 
                VALUES (:id_encomenda, :id_produto, :quantidade, :preco_unitario)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    public function getByEncomenda(int $id_encomenda): array
    {
        $sql = "SELECT ep.*, p.nome_produto, i.link_imagem 
                FROM encomendas_produtos ep
                JOIN produtos p ON ep.id_produto = p.id_produto
                LEFT JOIN imagens i ON p.id_imagem = i.id_imagem
                WHERE ep.id_encomenda = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_encomenda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
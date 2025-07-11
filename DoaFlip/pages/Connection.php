<?php
class Connection {
    private $host = "localhost";
    private $dbname = "doaflip";
    private $user = "root";
    private $pass = "";
    protected $conn; // Alterado para protected para permitir acesso pelas classes filhas

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname", 
                $this->user, 
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $this->conn;
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    // Método para verificar se um produto existe
    public function produtoExiste($id_produto) {
        try {
            $sql = "SELECT COUNT(*) FROM produtos WHERE id_produto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_produto]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar produto: " . $e->getMessage());
            return false;
        }
    }
}
?>
<?php
// dashboard/bd/Connection.php
if (!class_exists('Connection')) {
    class Connection
    {
        public function connect()
        {
            try {
                $pdo = new PDO(
                    "mysql:host=localhost;dbname=doaflip",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]
                );
                return $pdo;
            } catch (PDOException $e) {
                die("Erro ao conectar: " . $e->getMessage());
            }
        }
    }
}
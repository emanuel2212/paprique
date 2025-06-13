<?php
session_start();
require_once './Connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os campos foram enviados
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['erro_login'] = "Por favor, preencha todos os campos!";
        header('Location: ../login.php');
        exit();
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Create a child class to instantiate the abstract Connection
    class DatabaseConnection extends Connection {}
    $dbConnection = new DatabaseConnection();
    $pdo = $dbConnection->connect();

    try {
        // Prepara a consulta usando apenas o username primeiro
        $query = "SELECT id_utilizador, username, password, id_tipo_utilizador 
                 FROM utilizador 
                 WHERE username = :username";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica a senha (usando SHA1 conforme seu código original)
            if ($password === $user['password']) {
                // Autenticação bem-sucedida
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user['id_utilizador'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_tipo'] = $user['id_tipo_utilizador'];


                if ($user['id_tipo_utilizador'] == 1) { // Assumindo que 1 é admin
                    header('Location: ../index.php'); // Página do administrador
                } else {
                    header('Location: ../index.php'); // Página inicial para usuários normais
                }
                exit();
            }
        }

        // Se chegou aqui, a autenticação falhou
        $_SESSION['erro_login'] = "Utilizador ou senha inválidos!";
        header('Location: ../../login.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['erro_login'] = "Erro ao tentar fazer login. Por favor, tente novamente.";
        error_log("Login error: " . $e->getMessage());
        header('Location: ../../login.php');
        exit();
    }
} else {
    header('Location: ../../login.php');
    exit();
}

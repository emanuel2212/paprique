<?php
session_start();
require_once 'Connection.php';

// Se já estiver logado, redirecione para a página apropriada
if (isset($_SESSION['user'])) {
    if ($_SESSION['user_tipo'] == 1) {
        header('Location: ../index.php');
    } else {
        header('Location: ../../index.php');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os campos foram enviados
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['erro_login'] = "Por favor, preencha todos os campos!";
        header('Location: ../../index.php?page=login');
        exit();
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Create a child class to instantiate the abstract Connection
    class DatabaseConnection extends Connection {}
    $dbConnection = new DatabaseConnection();
    $pdo = $dbConnection->connect();

    try {
        // Prepara a consulta
        $query = "SELECT id_utilizador, username, password, id_tipo_utilizador 
                 FROM utilizador 
                 WHERE username = :username";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica todos os formatos possíveis
            if ($password === $user['password'] ||                      // Texto puro
                sha1($password) === $user['password'] ||               // SHA1
                password_verify($password, $user['password'])) {       // Bcrypt
                
                // Autenticação bem-sucedida
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user['id_utilizador'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_tipo'] = $user['id_tipo_utilizador'];

                // Se a senha estava em formato antigo, atualiza para Bcrypt
                if ($password === $user['password'] || sha1($password) === $user['password']) {
                    $novoHash = password_hash($password, PASSWORD_DEFAULT);
                    // Atualiza no banco de dados
                    $updateQuery = "UPDATE utilizador SET password = :novoHash WHERE id_utilizador = :id";
                    $updateStmt = $pdo->prepare($updateQuery);
                    $updateStmt->bindParam(':novoHash', $novoHash);
                    $updateStmt->bindParam(':id', $user['id_utilizador']);
                    $updateStmt->execute();
                }

                if ($user['id_tipo_utilizador'] == 1) { // Admin
                    header('Location: ../index.php');
                } else {
                    header('Location: ../../index.php');
                }
                exit();
            }
        }

        // Se chegou aqui, a autenticação falhou
        $_SESSION['erro_login'] = "Utilizador ou senha inválidos!";
        header('Location: ../../index.php?page=login');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = "Erro ao tentar fazer login. Por favor, tente novamente.";
        error_log("Login error: " . $e->getMessage());
        header('Location: ../../index.php?page=login');
        exit();
    }
} else {
    // Se acessado diretamente sem POST, redirecione para login
    header('Location: ../../index.php?page=login');
    exit();
}
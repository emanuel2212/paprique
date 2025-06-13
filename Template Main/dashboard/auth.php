<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php'); // Ajuste para sua página de login
    exit();
}

// Verifica se é admin (assumindo que tipo 1 é admin)
if ($_SESSION['id_tipo_utilizador'] != 1) {
    header('Location: ../'); // Redireciona não-admins para a página inicial
    exit();
}
?>
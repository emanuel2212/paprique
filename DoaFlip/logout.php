<?php
session_start();

// Destroi a sessão
unset($_SESSION['user']);
session_destroy();

// Define uma mensagem de sucesso
$_SESSION['logout_message'] = "Logout feito com sucesso";

// Redireciona de volta para a página inicial
header('Location: index.php');
exit();
?>
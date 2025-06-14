<?php
session_start();
require_once './Utilizador.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = 'ID inválido!';
    header('Location: viewUtilizador.php');
    exit;
}

$utilizador = new Utilizador();
$utilizador->setId((int)$_GET['id']);

if ($utilizador->delete()) {
    $_SESSION['msg'] = 'Utilizador excluído com sucesso!';
} else {
    $_SESSION['msg'] = 'Erro ao excluir categoria!';
}

header('Location: viewUtilizador.php');
exit;
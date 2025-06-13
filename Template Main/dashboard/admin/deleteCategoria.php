<?php
session_start();
require_once './Categoria.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = 'ID inválido!';
    header('Location: viewCategoria.php');
    exit;
}

$categoria = new Categorias();
$categoria->setId((int)$_GET['id']);

if ($categoria->delete()) {
    $_SESSION['msg'] = 'Categoria excluída com sucesso!';
} else {
    $_SESSION['msg'] = 'Erro ao excluir categoria!';
}

header('Location: viewCategoria.php');
exit;
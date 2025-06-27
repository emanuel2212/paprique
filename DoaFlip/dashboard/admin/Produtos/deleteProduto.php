<?php
require_once 'Produtos.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = 'ID inválido!';
    header('Location: ?page=viewProduto');
    exit;
}

$produto = new Produtos();
$produto->setId((int)$_GET['id']);

if ($produto->delete()) {
    $_SESSION['msg'] = 'Produto excluída com sucesso!';
} else {
    $_SESSION['msg'] = 'Erro ao excluir Produto!';
}

header('Location: ?page=viewProduto');
exit;
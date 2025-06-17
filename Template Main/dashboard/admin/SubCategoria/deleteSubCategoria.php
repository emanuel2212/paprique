<?php
require_once 'SubCategoria.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = 'ID inválido!';
    header('Location: ?page=viewSubCategoria');
    exit;
}

$subCategoria = new SubCategorias();
$subCategoria->setId((int)$_GET['id']);

if ($subCategoria->delete()) {
    $_SESSION['msg'] = 'Subcategoria excluída com sucesso!';
} else {
    $_SESSION['msg'] = 'Erro ao excluir subcategoria!';
}

header('Location: ?page=viewSubCategoria');
exit;
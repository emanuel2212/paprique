<?php
session_start();
require_once './StatusEncomendas.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['msg'] = 'ID inválido!';
    header('Location: viewStatusEncomenda.php');
    exit;
}

$statusEncomenda = new StatusEncomendas();
$statusEncomenda->setId((int)$_GET['id']);

if ($statusEncomenda->delete()) {
    $_SESSION['msg'] = 'Status de encomenda excluído com sucesso!';
} else {
    $_SESSION['msg'] = 'Erro ao excluir status de encomenda!';
}

header('Location: viewStatusEncomenda.php');
exit;
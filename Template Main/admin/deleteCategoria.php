<?php

/**
 * Inicia a sessão para armazenar e acessar variáveis de sessão.
 */
session_start();

/**
 * Ativa o buffer de saída para permitir redirecionamentos após o envio de cabeçalhos.
 */
ob_start();

/**
 * Obtém e sanitiza o ID do Categoria da URL.
 * 
 * @var int|null $id ID do Categoria ou null se não fornecido.
 */

$id_categoria = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($id_categoria)) {

    // Importa a classe Users que realiza a consulta ao Categoria.
    require './Categorias.php';

    // Instancia a classe Users e define o ID do Categoria para a operação de exclusão.
    $deleteCategoria = new Categorias();
    $deleteCategoria->setId($id_categoria);

    // Executa a operação de exclusão do Categoria.
    $valueUser = $deleteCategoria->delete();

    // Verifica se o Categoria foi apagado com sucesso.
    if ($valueUser) {

        // Armazena uma mensagem de sucesso na sessão se o Categoria for apagado.
        $_SESSION['msg'] = "<p style='color: #086;'>Categoria apagado com sucesso!</p>";

        // Redireciona para a página de listagem de Categorias.
        header("Location: viewCategoria.php");
    } else {

        // Armazena uma mensagem de erro na sessão se o Categoria não for apagado.
        $_SESSION['msg'] = "<p style='color: #f00;'>Categoria não apagado!</p>";

        // Redireciona para a página de listagem de Categorias.
        header("Location: viewCategoria.php");
    }
} else {

    // Armazena uma mensagem de erro na sessão se o ID do Categoria não for fornecido.
    $_SESSION['msg'] = "<p style='color: #f00;'>Categoria não encontrado!</p>";

    // Redireciona para a página de listagem de Categorias.
    header("Location: viewCategoria.php");
}

<?php
session_start();
ob_start();

$id_subcategoria = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

require './SubCategoria.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar SubCategoria</title>
    <style>
        .header-gradient {
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .btn-submit {
            transition: all 0.3s;
            letter-spacing: 1px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-edit me-2"></i>Editar SubCategoria</h1>
            <a href="viewSubCategoria.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container mt-5">

        <?php

        // Filtra os dados do formulário enviados via POST.
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verifica se o formulário foi submetido.
        if (!empty($formData['EditSubCategoria'])) {

            // Cria uma nova instância da classe SubCategorias.
            $updateSubCategoria = new SubCategorias();

            // Define os dados do formulário na instância da classe SubCategorias.
            $updateSubCategoria->setFormData($formData);

            // Tenta editar o subcategoria no banco de dados.
            $value = $updateSubCategoria->edit();

            // Verifica se o subcategoria foi editado com sucesso.
            if ($value) {
                // Define uma mensagem de sucesso na sessão e redireciona para a página de visualização.
                $_SESSION['msg'] = "<p style='color: #086;'>subcategoria editada com sucesso!</p>";
                // Redireciona para a página de visualização do subcategoria.
                header("Location: viewSubCategoria.php?id_subcategoria=$id_subcategoria");
            } else {
                // Exibe uma mensagem de erro se a edição falhar.
                echo "<p style='color: #f00;'>subcategoria não editada!</p>";
            }
        }

        // Verifica se o ID do subcategoria foi fornecido.
        if (!empty($id_subcategoria)) {

            // Instancia a classe SubCategorias e define o ID do subcategoria a ser visualizado.
            $viewSubCategoria = new SubCategorias();
            $viewSubCategoria->setId($id_subcategoria);

            // Executa o método view() para obter os detalhes do subcategoria.
            $valueSubCategoria = $viewSubCategoria->view();

            // Verifica se o subcategoria foi encontrado e exibe os detalhes.
            if (isset($valueSubCategoria['id_subcategoria'])) {

                // Extrai as chaves do array associativo para variáveis individuais.
                extract($valueSubCategoria);
            } else {

                // Armazena uma mensagem de erro na sessão se o categoria for encontrado.
                $_SESSION['msg'] = "<p style='color: #086;'>Sub Categoria encontrada!</p>";

                // Redireciona para a página de listagem de subcategorias.
                header("Location: viewSubCategoria.php");

                return;
            }
        }
        ?>

        <!-- Formulário para edição de um subcategoria existente -->
        <form method="POST" action="" class="row g-3">

        <div class="col-md-12">
    
         <input type="hidden" name="id_subcategoria" value="<?php echo $valueSubCategoria['id_subcategoria']; ?>">

            <div class="col-md-6">
                    <label for="nome_subcategoria" class="form-label">Nome do subcategoria</label>
                    <input type="text" id="nome_subcategoria" name="nome_subcategoria" class="form-control" value="<?php echo $valueSubCategoria['nome_subcategoria']; ?>" required>
            </div>

            <input type="submit" name="EditSubCategoria" class="btn btn-secondary btn-lg w-100 btn-submit" value="Editar">
        </form>
    </div>
</body>

</html>

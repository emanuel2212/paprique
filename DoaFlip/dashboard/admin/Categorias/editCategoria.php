<?php
require_once 'Categorias.php';
$id_categoria = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar Categoria</title>
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
            <h1 class="m-0"><i class="fas fa-edit me-2"></i>Editar Categoria</h1>
            <a href="?page=viewCategoria" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container mt-5">

        <?php

        // Filtra os dados do formulário enviados via POST.
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verifica se o formulário foi submetido.
        if (!empty($formData['EditCategoria'])) {

            // Cria uma nova instância da classe Categorias.
            $updateCategoria = new Categorias();

            // Define os dados do formulário na instância da classe Categorias.
            $updateCategoria->setFormData($formData);

            // Tenta editar o categoria no banco de dados.
            $value = $updateCategoria->edit();

            // Verifica se o categoria foi editado com sucesso.
            if ($value) {
                // Define uma mensagem de sucesso na sessão e redireciona para a página de visualização.
                $_SESSION['msg'] = "<p style='color: #086;'>categoria editada com sucesso!</p>";
                // Redireciona para a página de visualização do categoria.
                header("Location: ?page=viewCategoria");
            } else {
                // Exibe uma mensagem de erro se a edição falhar.
                echo "<p style='color: #f00;'>categoria não editada!</p>";
            }
        }

        // Verifica se o ID do categoria foi fornecido.
        if (!empty($id_categoria)) {

            // Instancia a classe Categorias e define o ID do categoria a ser visualizado.
            $viewCategoria = new Categorias();
            $viewCategoria->setId($id_categoria);

            // Executa o método view() para obter os detalhes do categoria.
            $valueCategoria = $viewCategoria->view();

            // Verifica se o categoria foi encontrado e exibe os detalhes.
            if (isset($valueCategoria['id_categoria'])) {

                // Extrai as chaves do array associativo para variáveis individuais.
                extract($valueCategoria);
            } else {

                // Armazena uma mensagem de erro na sessão se o categoria for encontrado.
                $_SESSION['msg'] = "<p style='color: #086;'>categoria encontrada!</p>";

                // Redireciona para a página de listagem de categorias.
                header("Location: viewCategoria.php");

                return;
            }
        }
        ?>

        <!-- Formulário para edição de um categoria existente -->
        <form method="POST" action="" class="row g-3">

            <input type="hidden" name="id_categoria" value="<?php echo $valueCategoria['id_categoria']; ?>">

            <div class="col-md-6">
                    <label for="nome_categoria" class="form-label">Nome do categoria</label>
                    <input type="text" id="nome_categoria" name="nome_categoria" class="form-control" value="<?php echo $valueCategoria['nome_categoria']; ?>" required>
                </div>

            <input type="submit" name="EditCategoria" class="btn btn-secondary btn-lg w-100 btn-submit" value="Editar">
        </form>
    </div>
</body>

</html>

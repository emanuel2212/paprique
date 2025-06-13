<?php
session_start();
ob_start();

$id_status_encomendas = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

require './StatusEncomendas.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar Status de Encomenda</title>
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
            <h1 class="m-0"><i class="fas fa-edit me-2"></i>Editar Status de Encomenda</h1>
            <a href="viewStatusEncomenda.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container mt-5">

        <?php

        // Filtra os dados do formulário enviados via POST.
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verifica se o formulário foi submetido.
        if (!empty($formData['EditStatusEncomenda'])) {

            // Cria uma nova instância da classe StatusEncomendas.
            $updateStatusEncomenda = new StatusEncomendas();

            // Define os dados do formulário na instância da classe StatusEncomendas.
            $updateStatusEncomenda->setFormData($formData);

            // Tenta editar o status de encomenda no banco de dados.
            $value = $updateStatusEncomenda->edit();

            // Verifica se o status de encomenda foi editado com sucesso.
            if ($value) {
                // Define uma mensagem de sucesso na sessão e redireciona para a página de visualização.
                $_SESSION['msg'] = "<p style='color: #086;'>status de encomenda editado com sucesso!</p>";
                // Redireciona para a página de visualização do status de encomenda.
                header("Location: viewStatusEncomenda.php?id_status_encomendas=$id_status_encomendas");
            } else {
                // Exibe uma mensagem de erro se a edição falhar.
                echo "<p style='color: #f00;'>status de encomenda não editado!</p>";
            }
        }

        // Verifica se o ID do status de encomenda foi fornecido.
        if (!empty($id_status_encomendas)) {

            // Instancia a classe StatusEncomendas e define o ID do status de encomenda a ser visualizado.
            $viewStatusEncomenda = new StatusEncomendas();
            $viewStatusEncomenda->setId($id_status_encomendas);

            // Executa o método view() para obter os detalhes do status de encomenda.
            $valueStatusEncomenda = $viewStatusEncomenda->view();

            // Verifica se o status de encomenda foi encontrado e exibe os detalhes.
            if (isset($valueStatusEncomenda['id_status_encomendas'])) {

                // Extrai as chaves do array associativo para variáveis individuais.
                extract($valueStatusEncomenda);
            } else {

                // Armazena uma mensagem de erro na sessão se o status de encomenda for encontrado.
                $_SESSION['msg'] = "<p style='color: #086;'>status de encomenda encontrado!</p>";

                // Redireciona para a página de listagem de status de encomenda.
                header("Location: viewStatusEncomenda.php");

                return;
            }
        }
        ?>

        <!-- Formulário para edição de um status de encomenda existente -->
        <form method="POST" action="" class="row g-3">

            <input type="hidden" name="id_status_encomendas" value="<?php echo $valueStatusEncomenda['id_status_encomendas']; ?>">

            <div class="col-md-6">
                    <label for="status" class="form-label">Nome do Status de Encomenda</label>
                    <input type="text" id="status" name="status" class="form-control" value="<?php echo $valueStatusEncomenda['status']; ?>" required>
                </div>

            <input type="submit" name="EditStatusEncomenda" class="btn btn-secondary btn-lg w-100 btn-submit" value="Editar">
        </form>
    </div>
</body>

</html>

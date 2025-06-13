<?php
session_start();
ob_start();

$id_utilizador = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

require './Utilizador.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar Utilizador</title>
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
            <h1 class="m-0"><i class="fas fa-edit me-2"></i>Editar Utilizador</h1>
            <a href="viewUtilizador.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container mt-5">

        <?php

        // Filtra os dados do formulário enviados via POST.
        $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        // Verifica se o formulário foi submetido.
        if (!empty($formData['EditUtilizador'])) {

            // Cria uma nova instância da classe Utilizador.
            $updateUtilizador = new Utilizador();

            // Define os dados do formulário na instância da classe Utilizador.
            $updateUtilizador->setFormData($formData);

            // Tenta editar o utilizador no banco de dados.
            $value = $updateUtilizador->edit();

            // Verifica se o utilizador foi editado com sucesso.
            if ($value) {
                // Define uma mensagem de sucesso na sessão e redireciona para a página de visualização.
                $_SESSION['msg'] = "<p style='color: #086;'>utilizador editado com sucesso!</p>";
                // Redireciona para a página de visualização do utilizador.
                header("Location: viewUtilizador.php?id_utilizador=$id_utilizador");
            } else {
                // Exibe uma mensagem de erro se a edição falhar.
                echo "<p style='color: #f00;'>utilizador não editado!</p>";
            }
        }

        // Verifica se o ID do utilizador foi fornecido.
        if (!empty($id_utilizador)) {

            // Instancia a classe Utilizador e define o ID do utilizador a ser visualizado.
            $viewUtilizador = new Utilizador();
            $viewUtilizador->setId($id_utilizador);

            // Executa o método view() para obter os detalhes do utilizador.
            $valueUtilizador = $viewUtilizador->view();

            // Verifica se o utilizador foi encontrado e exibe os detalhes.
            if (isset($valueUtilizador['id_utilizador'])) {

                // Extrai as chaves do array associativo para variáveis individuais.
                extract($valueUtilizador);
            } else {

                // Armazena uma mensagem de erro na sessão se o utilizador for encontrado.
                $_SESSION['msg'] = "<p style='color: #086;'>utilizador encontrado!</p>";

                // Redireciona para a página de listagem de utilizadores.
                header("Location: viewUtilizador.php");

                return;
            }
        }
        ?>

        <!-- Formulário para edição de um utilizador existente -->
        <form method="POST" action="" class="row g-3">

            <input type="hidden" name="id_utilizador" value="<?php echo $valueUtilizador['id_utilizador']; ?>">



            <div class="col-md-6">
                    <label for="id_tipo_utilizador" class="form-label">Tipo de utilizador</label>
                    <input type="text" id="id_tipo_utilizador" name="id_tipo_utilizador" class="form-control" value="<?php echo $valueUtilizador['id_tipo_utilizador']; ?>" required>
            </div>

            <div class="col-md-6">
                    <label for="username" class="form-label">Nome do utilizador</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $valueUtilizador['username']; ?>" required>
            </div>
            
            <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $valueUtilizador['email']; ?>" required>
            </div>
            <div class="col-md-6">
                    <label for="morada" class="form-label">Morada</label>
                    <input type="text" id="morada" name="morada" class="form-control" value="<?php echo $valueUtilizador['morada']; ?>" required>
            </div>
              <div class="col-md-6">
                    <label for="morada" class="form-label">Telefone</label>
                    <input type="text" id="morada" name="morada" class="form-control" value="<?php echo $valueUtilizador['morada']; ?>" required>
            </div>
              <div class="col-md-6">
                    <label for="codigo_postal" class="form-label">codigo Postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" value="<?php echo $valueUtilizador['codigo_postal']; ?>" required>
            </div>
            <div class="col-md-6">
                    <label for="nif" class="form-label">NIF</label>
                    <input type="text" id="nif" name="nif" class="form-control" value="<?php echo $valueUtilizador['nif']; ?>" required>
            </div>
            

            <input type="submit" name="EditUtilizador" class="btn btn-secondary btn-lg w-100 btn-submit" value="Editar">
        </form>
    </div>
</body>

</html>

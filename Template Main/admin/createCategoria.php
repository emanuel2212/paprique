<?php
session_start();
ob_start();

require 'Categorias.php';

// Processamento do formulário movido para o topo para evitar headers já enviados
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($formData['AddUser'])) {
    $criarCategoria = new Categorias();
    $criarCategoria->setFormData($formData);
    $value = $criarCategoria->create();

    if ($value) {
        $_SESSION['msg'] = '<div class="alert alert-success">Categoria cadastrada com sucesso!</div>';
        header("Location: viewCategoria.php");
        exit(); // Adicionado exit() após header redirect
    } else {
        $errorMessage = '<div class="alert alert-danger">Erro ao cadastrar categoria!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Cadastrar Categoria</title>
    <style>
        .header-gradient {
            background: linear-gradient(135deg, #28a745, #218838);
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
            <h1 class="m-0"><i class="fas fa-tags me-2"></i>Cadastrar Categoria</h1>
            <a href="viewCategoria.php" class="btn btn-outline-light">
                <i class="fas fa-home me-1"></i> Ínicio
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="form-container">
            <?php
            // Exibe mensagem de erro se existir
            if (!empty($errorMessage)) {
                echo $errorMessage;
            }
            ?>

            <form method="POST" action="" class="row g-3">
                <div class="col-md-12">
                    <label for="nome_categoria" class="form-label">
                        <i class="fas fa-tag me-1"></i> Nome da Categoria
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        <input type="text" name="nome_categoria" class="form-control" 
                               placeholder="Digite o nome da categoria" required
                               value="<?= isset($formData['nome_categoria']) ? htmlspecialchars($formData['nome_categoria']) : '' ?>">
                    </div>  
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" href="viewCategoria.php" name="AddUser" class="btn btn-success btn-lg w-100 btn-submit"
                        class="fas fa-save me-2" value="Cadastrar Categoria">
                            <i class="fas fa-save me-2"></i> Cadastrar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
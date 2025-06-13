<?php
session_start();
ob_start();

require 'Imagens.php';

// Processamento do formulário movido para o topo para evitar headers já enviados
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($formData['AddImagem'])) {
    $criarImagem = new Imagens();
    $criarImagem->setFormData($formData);
    $value = $criarImagem->create();

    if ($value) {
        $_SESSION['msg'] = '<div class="alert alert-success">Imagem cadastrada com sucesso!</div>';
        header("Location: viewImagem.php");
        exit(); // Adicionado exit() após header redirect
    } else {
        $errorMessage = '<div class="alert alert-danger">Erro ao cadastrar imagem!</div>';
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
    <title>Cadastrar Imagem</title>
    <style>
        .header-gradient {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .btn-submit {
            transition: all 0.3s;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-tags me-2"></i>Cadastrar Imagem</h1>
            <a href="viewImagem.php" class="btn btn-outline-light">
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
                <!-- <<div class="col-md-12">
                        <label for="imagem" class="form-label">
                            <i class="fas fa-image me-1"></i> Imagem
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-upload"></i></span>
                            <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*" required>
                        </div>
                </div> -->
                <div class="col-md-12">
                    <label for="titulo" class="form-label">
                        <i class="fas fa-tag me-1"></i> Título
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        <input type="text" name="titulo" class="form-control"
                            placeholder="Digite o título da imagem" required
                            value="<?= isset($formData['titulo']) ? htmlspecialchars($formData['titulo']) : '' ?>">
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="link_imagem" class="form-label">
                        <i class="fas fa-tag me-1"></i> Link da Imagem
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        <input type="text" name="link_imagem" class="form-control"
                            placeholder="Digite o link da imagem" required
                            value="<?= isset($formData['link_imagem']) ? htmlspecialchars($formData['link_imagem']) : '' ?>">
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="descricao" class="form-label">
                        <i class="fas fa-tag me-1"></i> Descrição
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        <input type="text" name="descricao" class="form-control"
                            placeholder="Digite a Descrição" required
                            value="<?= isset($formData['descricao']) ? htmlspecialchars($formData['descricao']) : '' ?>">
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" href="viewImagem.php" name="AddImagem" class="btn btn-success btn-lg w-100 btn-submit"
                        class="fas fa-save me-2" value="Cadastrar Imagem">
                        <i class="fas fa-save me-2"></i> Cadastrar Imagem
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
<?php
session_start();
ob_start();

require 'SubCategoria.php';
require 'Categorias.php';

// Obter todas as categorias existentes
$categoria = new Categorias(); // Assumindo que você tem essa classe
$categorias = $categoria->listAll(); // Método que retorna todas as categorias

// Processamento do formulário
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($formData['addSubCategoria'])) {
    $criarSubCategoria = new SubCategorias();
    $criarSubCategoria->setFormData($formData);
    $value = $criarSubCategoria->create();

    if ($value) {
        $_SESSION['msg'] = '<div class="alert alert-success">SubCategoria cadastrada com sucesso!</div>';
        header("Location: viewSubCategoria.php");
        exit();
    } else {
        $errorMessage = '<div class="alert alert-danger">Erro ao cadastrar subcategoria! Verifique se a categoria selecionada existe.</div>';
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
    <title>Cadastrar SubCategoria</title>
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
        .select2-container--bootstrap5 .select2-selection {
            height: calc(3.5rem + 2px);
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-tags me-2"></i>Cadastrar SubCategoria</h1>
            <a href="viewSubCategoria.php" class="btn btn-outline-light">
                <i class="fas fa-home me-1"></i> Ínicio
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="form-container">
            <?php
            if (!empty($errorMessage)) {
                echo $errorMessage;
            }
            ?> 

            <form method="POST" action="" class="row g-3">
    <!-- Campo para selecionar a categoria - CORRIGIDO -->
    <div class="col-md-12">
        <label for="select_categoria" class="form-label">
            <i class="fas fa-list-alt me-1"></i> Selecione a Categoria
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-folder-open"></i></span>
            <select id="select_categoria" name="id_categoria" class="form-select" required>
                <option value="">Selecione uma categoria...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id_categoria']) ?>" 
                        <?= (isset($formData['id_categoria']) && $formData['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nome_categoria']) ?> (ID: <?= htmlspecialchars($cat['id_categoria']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Campo para o nome da subcategoria - CORRIGIDO -->
    <div class="col-md-12">
        <label for="input_subcategoria" class="form-label">
            <i class="fas fa-tag me-1"></i> Nome da SubCategoria
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
            <input type="text" id="input_subcategoria" name="nome_subcategoria" class="form-control" 
                   placeholder="Digite o nome da subcategoria" required
                   value="<?= isset($formData['nome_subcategoria']) ? htmlspecialchars($formData['nome_subcategoria']) : '' ?>">
        </div>
    </div>

    <div class="col-12 mt-4">
        <button type="submit" href="viewSubCategoria.php" name="addSubCategoria" class="btn btn-success btn-lg w-100 btn-submit">
            <i class="fas fa-save me-2"></i> Cadastrar SubCategoria
        </button>
    </div>
</form>
        </div>
    </div>

</body>

</html>
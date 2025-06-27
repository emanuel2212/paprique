<?php

require 'Utilizador.php';

// Processamento do formulário movido para o topo para evitar headers já enviados
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($formData['AddUtilizador'])) {
    $criarUtilizador = new Utilizador();
    $criarUtilizador->setFormData($formData);
    $value = $criarUtilizador->create();

    if ($value) {
        $_SESSION['msg'] = '<div class="alert alert-success">Utilizador cadastrado com sucesso!</div>';
        header("Location: viewUtilizador.php");
        exit();
    } else {
        $errorMessage = '<div class="alert alert-danger">Erro ao cadastrar utilizador!</div>';
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
    <title>Cadastrar Utilizador</title>
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
            <h1 class="m-0"><i class="fas fa-user me-2"></i>Cadastrar Utilizador</h1>
            <a href="viewUtilizador.php" class="btn btn-outline-light">
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
                <!-- Tipo de Utilizador -->
                <div class="col-md-6">
                    <label for="id_tipo_utilizador" class="form-label">
                        <i class="fas fa-user-tag me-1"></i> Tipo de Utilizador
                    </label>
                    <select name="id_tipo_utilizador" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="1" <?= (isset($formData['id_tipo_utilizador']) && $formData['id_tipo_utilizador'] == '1') ? 'selected' : '' ?>>Administrador</option>
                        <option value="2" <?= (isset($formData['id_tipo_utilizador']) && $formData['id_tipo_utilizador'] == '2') ? 'selected' : '' ?>>Funcionario</option>
                        <option value="3" <?= (isset($formData['id_tipo_utilizador']) && $formData['id_tipo_utilizador'] == '3') ? 'selected' : '' ?>>Cliente</option>
                    </select>
                </div>

                <!-- Nome de Utilizador -->
                <div class="col-md-6">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-1"></i> Nome de Utilizador
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Digite o nome de utilizador" required
                            value="<?= isset($formData['username']) ? htmlspecialchars($formData['username']) : '' ?>">
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i> Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Digite o email" required
                            value="<?= isset($formData['email']) ? htmlspecialchars($formData['email']) : '' ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Digite a password" required>
                    </div>
                </div>

                <!-- Morada -->
                <div class="col-12">
                    <label for="morada" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i> Morada
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-home"></i></span>
                        <input type="text" name="morada" class="form-control" placeholder="Digite a morada" required
                            value="<?= isset($formData['morada']) ? htmlspecialchars($formData['morada']) : '' ?>">
                    </div>
                </div>

                <!-- Telefone -->
                <div class="col-md-6">
                    <label for="telefone" class="form-label">
                        <i class="fas fa-phone me-1"></i> Telefone
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                        <input type="tel" name="telefone" class="form-control" placeholder="Digite o telefone" required
                            value="<?= isset($formData['telefone']) ? htmlspecialchars($formData['telefone']) : '' ?>">
                    </div>
                </div>

                <!-- Código Postal -->
                <div class="col-md-6">
                    <label for="codigo_postal" class="form-label">
                        <i class="fas fa-mail-bulk me-1"></i> Código Postal
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" name="codigo_postal" class="form-control" placeholder="Digite o código postal" required
                            value="<?= isset($formData['codigo_postal']) ? htmlspecialchars($formData['codigo_postal']) : '' ?>">
                    </div>
                </div>

                <!-- NIF -->
                <div class="col-md-6">
                    <label for="nif" class="form-label">
                        <i class="fas fa-id-card me-1"></i> NIF
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                        <input type="text" name="nif" class="form-control" placeholder="Digite o NIF" required
                            value="<?= isset($formData['nif']) ? htmlspecialchars($formData['nif']) : '' ?>">
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" href="viewUtilizador.php" name="AddUtilizador"  class="btn btn-success btn-lg w-100 btn-submit"
                    class="fas fa-save me-2" value="Cadastrar Utilizador">
                        <i class="fas fa-save me-2"></i> Cadastrar Utilizador
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
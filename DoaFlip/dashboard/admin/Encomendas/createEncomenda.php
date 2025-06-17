    <?php
    session_start();
    ob_start();

    require 'Encomendas.php';

    // Processamento do formulário movido para o topo para evitar headers já enviados
    $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($formData['AddEncomenda'])) {
        $criarEncomenda = new Encomendas();
        $criarEncomenda->setFormData($formData);
        $value = $criarEncomenda->create();

        if ($value) {
            $_SESSION['msg'] = '<div class="alert alert-success">Encomenda cadastrada com sucesso!</div>';
            header("Location: viewEncomenda.php");
            exit();
        } else {
            $errorMessage = '<div class="alert alert-danger">Erro ao cadastrar encomenda!</div>';
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
        <title>Cadastrar Encomenda</title>
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
                <h1 class="m-0"><i class="fas fa-user me-2"></i>Cadastrar Encomenda</h1>
                <a href="viewEncomenda.php" class="btn btn-outline-light">
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
                        <label for="id_status_encomendas" class="form-label">
                            <i class="fas fa-user-tag me-1"></i> Status da Encomenda
                        </label>
                        <select name="id_status_encomendas" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="1" <?= (isset($formData['id_status_encomendas']) && $formData['id_status_encomendas'] == '1') ? 'selected' : '' ?>>Em processo</option>
                            <option value="2" <?= (isset($formData['id_status_encomendas']) && $formData['id_status_encomendas'] == '2') ? 'selected' : '' ?>>Confirmado</option>
                            <option value="3" <?= (isset($formData['id_status_encomendas']) && $formData['id_status_encomendas'] == '3') ? 'selected' : '' ?>>Enviado</option>
                            <option value="4" <?= (isset($formData['id_status_encomendas']) && $formData['id_status_encomendas'] == '4') ? 'selected' : '' ?>>Recebido</option>
                        </select>
                    </div>

                    <!-- Nome de Utilizador -->
                    <div class="col-md-6">
                        <label for="id_utilizador" class="form-label">
                            <i class="fas fa-user me-1"></i> ID do Utilizador
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="id_utilizador" class="form-control" placeholder="Digite o ID de utilizador" required
                                value="<?= isset($formData['id_utilizador']) ? htmlspecialchars($formData['id_utilizador']) : '' ?>">
                        </div>
                    </div>

                    <!-- Valor Total -->
                    <div class="col-md-6">
                        <label for="valor_total" class="form-label">
                            <i class="fas fa-dollar-sign "></i> Valor Total
                        </label>
                        <div class="input-group">
                            <input type="number" name="valor_total" class="form-control" placeholder="Digite o valor total" required
                                value="<?= isset($formData['valor_total']) ? htmlspecialchars($formData['valor_total']) : '' ?>">
                        </div>
                    </div>

                    <!-- Data Encomenda -->
                    <div class="col-md-6">
                        <label for="data_encomenda" class="form-label">
                            <i class="fas fa-lock me-1"></i> Data Encomenda
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="date" name="data_encomenda" class="form-control" placeholder="Digite a data_encomenda" required>
                        </div>
                    </div>


                    <div class="col-12 mt-4">
                        <button type="submit" href="viewEncomenda.php" name="AddEncomenda" class="btn btn-success btn-lg w-100 btn-submit"
                            class="fas fa-save me-2" value="Cadastrar Utilizador">
                            <i class="fas fa-save me-2"></i> Cadastrar Encomenda
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </body>

    </html>
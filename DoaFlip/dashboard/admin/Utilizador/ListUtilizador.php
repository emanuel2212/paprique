<?php
require 'Utilizador.php';

// Verificar se foi passado um ID na URL
$id_utilizador = $_GET['id'] ?? null;

if (!$id_utilizador) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Nenhum utilizador especificado!</div>';
    header("Location: ?page=viewUtilizador");
    exit();
}

// Obter os dados do utilizador
$utilizador = new Utilizador();
$detalhes = $utilizador->getById($id_utilizador);

if (!$detalhes) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Utilizador não encontrado!</div>';
    header("Location: ?page=viewUtilizador");
    exit();
}

// Função para exibir valores (trata campos nulos)
function displayValue($value) {
    if (is_null($value) || $value === '') {
        return '<span class="text-muted fst-italic">Não informado</span>';
    }
    return htmlspecialchars($value);
}

// Verificar mensagens de sessão
$mensagem = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header-gradient {
            background: linear-gradient(135deg, #343a40, #212529);
        }
        .detail-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            min-height: 38px;
        }
        .not-provided {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-user-circle me-2"></i>Detalhes do Utilizador</h1>
            <div>
                <a href="?page=viewUtilizador" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="?page=editUtilizador&id=<?= $id_utilizador ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="?page=deleteUtilizador&id=<?= $id_utilizador ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('Tem certeza que deseja excluir este utilizador?')">
                    <i class="fas fa-trash-alt me-1"></i> Excluir
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <?php if ($mensagem): ?>
            <div class="alert alert-dismissible fade show <?= strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card detail-card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-info-circle me-1"></i> Informações Básicas
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">ID do Utilizador:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['id_utilizador']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Nome de Utilizador:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['username']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Nome Completo:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['nome_completo']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Email:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['email']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Tipo de Utilizador:</div>
                    <div class="col-md-9 detail-value">
                        <?php 
                        if (isset($detalhes['id_tipo_utilizador'])) {
                            switch($detalhes['id_tipo_utilizador']) {
                                case 1: echo 'Administrador'; break;
                                case 2: echo 'Funcionário'; break;
                                case 3: echo 'Cliente'; break;
                                default: echo 'Indefinido';
                            }
                        } else {
                            echo displayValue(null);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-address-card me-1"></i> Informações de Contacto
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Morada:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['morada']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Telefone:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['telefone']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Código Postal:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['codigo_postal']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">NIF:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['nif']) ?></div>
                </div>
            </div>
        </div>

        <div class="card detail-card">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-calendar-alt me-1"></i> Informações do Sistema
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Data de Criação:</div>
                    <div class="col-md-9 detail-value">
                        <?= isset($detalhes['data_criado']) ? date('d/m/Y H:i', strtotime($detalhes['data_criado'])) : displayValue(null) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 detail-label">Última Atualização:</div>
                    <div class="col-md-9 detail-value">
                        <?= isset($detalhes['data_atualizado']) ? date('d/m/Y H:i', strtotime($detalhes['data_atualizado'])) : displayValue(null) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require 'Encomendas.php';

// Verificar se foi passado um ID na URL
$id_encomenda = $_GET['id'] ?? null;

if (!$id_encomenda) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Nenhuma encomenda especificada!</div>';
    header("Location: ?page=viewEncomenda");
    exit();
}

// Obter os dados da encomenda
$encomenda = new Encomendas();
$detalhes = $encomenda->getById($id_encomenda);

if (!$detalhes) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Encomenda não encontrada!</div>';
    header("Location: ?page=viewEncomenda");
    exit();
}

// Função para exibir valores (trata campos nulos)
function displayValue($value)
{
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
    <title>Detalhes da Encomenda</title>
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
            <h1 class="m-0"><i class="fas fa-user-circle me-2"></i>Detalhes da Encomenda</h1>
            <div>
                <a href="?page=viewEncomenda" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="?page=editEncomenda&id=<?= $id_encomenda ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="?page=deleteEncomenda&id=<?= $id_encomenda ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Tem certeza que deseja excluir esta encomenda?')">
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
                    <div class="col-md-3 detail-label">ID da Encomenda:</div>
                    <div class="col-md-9 detail-value"><?= displayValue($detalhes['id_encomenda']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Status da Encomenda:</div>
                    <div class="col-md-9 detail-value">
                        <?php
                        if (isset($detalhes['id_status_encomendas'])) {
                            switch ($detalhes['id_status_encomendas']) {
                                case 1:
                                    echo 'Pendente';
                                    break;
                                case 2:
                                    echo 'Em Processamento';
                                    break;
                                case 3:
                                    echo 'Concluída';
                                    break;
                                case 4:
                                    echo 'Recebida';
                                    break;
                                case 6:
                                    echo 'Cancelada';
                                    break;
                                    
                                default:
                                    echo 'Indefinido';
                            }
                        } else {
                            echo displayValue(null);
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Utilizador:</div>
                    <div class="col-md-9 detail-value">
                        <?php
                        if (isset($detalhes['id_utilizador'])) {
                            echo displayValue($detalhes['id_utilizador']);
                        } else {
                            echo displayValue(null);
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Valor Total:</div>
                    <div class="col-md-9 detail-value">
                        <?= isset($detalhes['valor_total']) ? 'R$ ' . number_format($detalhes['valor_total'], 2, ',', '.') : displayValue(null) ?>
                    </div>
            </div>
        </div>

        <div class="card detail-card">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-calendar-alt me-1"></i>Informações do Sistema
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 detail-label">Data de Criação:</div>
                    <div class="col-md-9 detail-value">
                        <?= isset($detalhes['data_encomenda']) ? date('d/m/Y H:i', strtotime($detalhes['data_encomenda'])) : displayValue(null) ?>
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
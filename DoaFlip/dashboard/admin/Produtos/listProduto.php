<?php
require 'Produtos.php';

// Verificar se foi passado um ID na URL
$id_produto = $_GET['id'] ?? null;

if (!$id_produto) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Nenhum produto especificado!</div>';
    header("Location: ?page=viewProduto");
    exit();
}

// Obter os dados do produto
$produto = new Produtos();
$detalhes = $produto->getById($id_produto);

if (!$detalhes) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Produto não encontrado!</div>';
    header("Location: ?page=viewProduto");
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
    <title>Detalhes do Produto</title>
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
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }
        .image-container {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-box me-2"></i>Detalhes do Produto</h1>
            <div>
                <a href="?page=viewProduto" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="?page=editProduto&id=<?= $id_produto ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="?page=deleteProduto&id=<?= $id_produto ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Tem certeza que deseja excluir este produto?')">
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

        <div class="row">
            <div class="col-md-4">
                <div class="card detail-card mb-4">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-image me-1"></i> Imagem do Produto
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($detalhes['link_imagem'])): ?>
                            <img src="./images/"<?= htmlspecialchars($detalhes['link_imagem']) ?> alt="Imagem do Produto" class="product-image img-fluid">
                        <?php else: ?>
                            <div class="text-muted py-4">
                                <i class="fas fa-image fa-4x mb-3"></i>
                                <p>Nenhuma imagem disponível</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card detail-card mb-4">
                    <div class="card-header bg-dark text-white">
                        <i class="fas fa-info-circle me-1"></i> Informações do Produto
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 detail-label">ID:</div>
                            <div class="col-md-9 detail-value"><?= displayValue($detalhes['id_produto']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 detail-label">Nome:</div>
                            <div class="col-md-9 detail-value"><?= displayValue($detalhes['nome_produto']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 detail-label">Marca:</div>
                            <div class="col-md-9 detail-value"><?= displayValue($detalhes['marca']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 detail-label">Categoria:</div>
                            <div class="col-md-9 detail-value">
                                <?php
                                if (isset($detalhes['id_categoria'])) {
                                    switch ($detalhes['id_categoria']) {
                                        case 18:
                                            echo 'Peça';
                                            break;
                                        case 19:
                                            echo 'Proteção';
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
                            <div class="col-md-3 detail-label">Subcategoria:</div>
                            <div class="col-md-9 detail-value">
                                 <?php
                                if (isset($detalhes['id_subcategoria'])) {
                                    switch ($detalhes['id_subcategoria']) {
                                        case 9:
                                            echo 'Truck';
                                            break;
                                        case 10:
                                            echo 'Rodas';
                                            break;
                                        case 15:
                                            echo 'Rolamentos';
                                            break;
                                        case 16:
                                            echo 'Amortecedores';
                                            break;
                                        case 17:
                                            echo 'Porcas e Parafusos';
                                            break;
                                        case 18:
                                            echo 'Cera de Skate';
                                            break;
                                        case 19:
                                            echo 'Chave de Skate';
                                            break;
                                        case 24:
                                            echo 'Tabua';
                                            break;
                                        case 25:
                                            echo 'Lixa';
                                            break;
                                        case 13:
                                            echo 'Capacete';
                                            break;
                                        case 14:
                                            echo 'Joelheira';
                                            break;
                                        case 22:
                                            echo 'Protetor de Punho';
                                            break;
                                        case 23:
                                            echo 'Cotoveleira';
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
                            <div class="col-md-3 detail-label">Preço:</div>
                            <div class="col-md-9 detail-value">
                                <?= isset($detalhes['preco']) ? '€ ' . number_format($detalhes['preco'], 2, ',', '.') : displayValue(null) ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 detail-label">Descrição:</div>
                            <div class="col-md-9 detail-value">
                                <?= !empty($detalhes['descricao']) ? nl2br(htmlspecialchars($detalhes['descricao'])) : displayValue(null) ?>
                            </div>
                        </div>
                    </div>
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
                        <?= isset($detalhes['data_criacao']) ? date('d/m/Y H:i', strtotime($detalhes['data_criacao'])) : displayValue(null) ?>
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
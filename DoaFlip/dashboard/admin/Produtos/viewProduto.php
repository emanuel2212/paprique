<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Produto</title>
    <style>
        .card-header {
            background: linear-gradient(135deg, #343a40, #212529);
        }
        .btn-action {
            width: 100px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .empty-state {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #0d6efd;
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid p-4 bg-dark text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-tags me-2"></i>Produto</h1>
            <div>
                <a href="./" class="btn btn-outline-light me-2">
                    <i class="fas fa-home me-1"></i> Início
                </a>
                <a href="?page=viewProduto" class="btn btn-outline-light me-2">
                    <i class="fas fa-sync-alt me-1"></i> Recarregar
                </a>
                <a href="?page=createProduto" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Cadastrar
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header text-white">
                <i class="fas fa-list me-1"></i> Lista de Produtos
            </div>
            <div class="card-body">
                <?php
                require 'Produtos.php';
                $produtos = new Produtos();
                
                // Obter produtos separados por categoria
                $produtos_pecas = [];
                $produtos_protecoes = [];
                $produtos_sapatilhas = [];
                
                $todos_produtos = $produtos->list();
                
                if (!empty($todos_produtos)) {
                    foreach ($todos_produtos as $produto) {
                        if ($produto['id_categoria'] == 18) { // Skates
                            $produtos_pecas[] = $produto;
                        } elseif ($produto['id_categoria'] == 19) { // Proteções
                            $produtos_protecoes[] = $produto;
                        } elseif ($produto['id_categoria'] == 24) { // Sapatilhas (novo ID)
                            $produtos_sapatilhas[] = $produto;
                        }
                    }
                }
                ?>

                <!-- Abas de navegação -->
                <ul class="nav nav-tabs" id="produtosTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pecas-tab" data-bs-toggle="tab" data-bs-target="#pecas" type="button" role="tab">
                            <i class="fas fa-skating"></i> Skates
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="protecoes-tab" data-bs-toggle="tab" data-bs-target="#protecoes" type="button" role="tab">
                            <i class="fas fa-shield-alt me-1"></i> Proteções
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sapatilhas-tab" data-bs-toggle="tab" data-bs-target="#sapatilhas" type="button" role="tab">
                            <i class="fas fa-shoe-prints me-1"></i> Sapatilhas
                        </button>
                    </li>
                </ul>

                <!-- Conteúdo das abas -->
                <div class="tab-content" id="produtosTabContent">
                    <!-- Tab Skates -->
                    <div class="tab-pane fade show active" id="pecas" role="tabpanel">
                        <?php if (!empty($produtos_pecas)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="10%"><i class="fas fa-hashtag me-1"></i> ID</th>
                                            <th>Nome do Produto</th>  
                                            <th>Preço</th>  
                                            <th width="20%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos_pecas as $produto): ?>
                                            <tr>
                                                <td class='fw-bold'><?= $produto['id_produto'] ?></td>
                                                <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                                                <td>€ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                                <td class="d-flex gap-2">
                                                    <a href='?page=ListProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-secondary btn-sm btn-action'>
                                                        <i class='fas fa-eye'></i> Visualizar
                                                    </a>
                                                    <a href='?page=editProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-warning btn-sm btn-action'>
                                                        <i class='fas fa-edit me-1'></i> Editar
                                                    </a>
                                                    <a href='javascript:void(0)' 
                                                       onclick='if(confirm("Tem certeza que deseja excluir <?= addslashes($produto['nome_produto']) ?>?")) { window.location.href="?page=deleteProduto&id=<?= $produto['id_produto'] ?>"; }' 
                                                       class='btn btn-danger btn-sm btn-action'>
                                                        <i class='fas fa-trash-alt me-1'></i> Apagar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhuma peça encontrada</h4>
                                <p class="text-muted">Cadastre novas Skates usando o botão acima</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Proteções -->
                    <div class="tab-pane fade" id="protecoes" role="tabpanel">
                        <?php if (!empty($produtos_protecoes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="10%"><i class="fas fa-hashtag me-1"></i> ID</th>
                                            <th>Nome do Produto</th>  
                                            <th>Preço</th>  
                                            <th width="20%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos_protecoes as $produto): ?>
                                            <tr>
                                                <td class='fw-bold'><?= $produto['id_produto'] ?></td>
                                                <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                                                <td>€ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                                <td class="d-flex gap-2">
                                                    <a href='?page=ListProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-secondary btn-sm btn-action'>
                                                        <i class='fas fa-eye'></i> Visualizar
                                                    </a>
                                                    <a href='?page=editProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-warning btn-sm btn-action'>
                                                        <i class='fas fa-edit me-1'></i> Editar
                                                    </a>
                                                    <a href='javascript:void(0)' 
                                                       onclick='if(confirm("Tem certeza que deseja excluir <?= addslashes($produto['nome_produto']) ?>?")) { window.location.href="?page=deleteProduto&id=<?= $produto['id_produto'] ?>"; }' 
                                                       class='btn btn-danger btn-sm btn-action'>
                                                        <i class='fas fa-trash-alt me-1'></i> Apagar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhuma proteção encontrada</h4>
                                <p class="text-muted">Cadastre novas proteções usando o botão acima</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Sapatilhas (NOVA ABA) -->
                    <div class="tab-pane fade" id="sapatilhas" role="tabpanel">
                        <?php if (!empty($produtos_sapatilhas)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="10%"><i class="fas fa-hashtag me-1"></i> ID</th>
                                            <th>Nome do Produto</th>  
                                            <th>Preço</th>  
                                            <th width="20%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos_sapatilhas as $produto): ?>
                                            <tr>
                                                <td class='fw-bold'><?= $produto['id_produto'] ?></td>
                                                <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                                                <td>€ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                                <td class="d-flex gap-2">
                                                    <a href='?page=ListProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-secondary btn-sm btn-action'>
                                                        <i class='fas fa-eye'></i> Visualizar
                                                    </a>
                                                    <a href='?page=editProduto&id=<?= $produto['id_produto'] ?>' class='btn btn-warning btn-sm btn-action'>
                                                        <i class='fas fa-edit me-1'></i> Editar
                                                    </a>
                                                    <a href='javascript:void(0)' 
                                                       onclick='if(confirm("Tem certeza que deseja excluir <?= addslashes($produto['nome_produto']) ?>?")) { window.location.href="?page=deleteProduto&id=<?= $produto['id_produto'] ?>"; }' 
                                                       class='btn btn-danger btn-sm btn-action'>
                                                        <i class='fas fa-trash-alt me-1'></i> Apagar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-shoe-prints fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhuma sapatilha encontrada</h4>
                                <p class="text-muted">Cadastre novas sapatilhas usando o botão acima</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
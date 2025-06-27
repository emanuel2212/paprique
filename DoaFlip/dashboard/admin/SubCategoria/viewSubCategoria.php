<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>SubCategorias</title>
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
        .category-section {
            margin-bottom: 30px;
        }
        .category-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .category-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid p-4 bg-dark text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-tags me-2"></i>SubCategorias</h1>
            <div>
                <a href="./" class="btn btn-outline-light me-2">
                    <i class="fas fa-home me-1"></i> Início
                </a>
                <a href="?page=viewSubCategoria" class="btn btn-outline-light me-2">
                    <i class="fas fa-sync-alt me-1"></i> Recarregar
                </a>
                <a href="?page=createSubCategoria" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Cadastrar
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header text-white">
                <i class="fas fa-list me-1"></i> Lista de SubCategorias
            </div>
            <div class="card-body">
                <?php
                require 'SubCategoria.php';

                $listUsers = new SubCategorias();
                $resultUsers = $listUsers->list();

                if (!empty($resultUsers)) {
                    // Separar subcategorias por categoria
                    $pecas = array_filter($resultUsers, function($item) {
                        return $item['id_categoria'] == 18; // Peças
                    });
                    
                    $protecoes = array_filter($resultUsers, function($item) {
                        return $item['id_categoria'] == 19; // Proteções
                    });
                    
                    // Função para exibir tabela de subcategorias
                    function displaySubcategories($subcategories, $categoryName, $icon) {
                        if (!empty($subcategories)) {
                            echo '<div class="category-section">';
                            echo '<div class="category-title">';
                            echo '<i class="fas '.$icon.' category-icon"></i>';
                            echo $categoryName;
                            echo '</div>';
                            
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-hover table-striped">';
                            echo '<thead class="table-dark">';
                            echo '<tr>';
                            echo '<th width=""><i class="fas fa-hashtag me-1"></i> ID</th>';
                            echo '<th width=""><i class="fas fa-tag me-1"></i> Nome</th>';
                            echo '<th width="20%"><i class="fas fa-cogs me-1"></i> Ações</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ($subcategories as $row) {  
                                echo '<tr>';
                                echo "<td>{$row['id_subcategoria']}</td>";
                                echo "<td class='fw-bold'>{$row['nome_subcategoria']}</td>";
                                echo '<td class="d-flex gap-2">';
                                echo "<a href='?page=editSubCategoria&id={$row['id_subcategoria']}' class='btn btn-warning btn-sm btn-action'>
                                        <i class='fas fa-edit me-1'></i> Editar
                                    </a>";
                                echo "<a href='javascript:void(0)' 
                                        onclick='if(confirm(\"Tem certeza que deseja excluir {$row['nome_subcategoria']}?\")) { window.location.href=\"?page=deleteSubCategoria&id={$row['id_subcategoria']}\"; }' 
                                        class='btn btn-danger btn-sm btn-action'>
                                        <i class='fas fa-trash-alt me-1'></i> Apagar
                                    </a>";
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    
                    // Exibir seção de Peças
                    displaySubcategories($pecas, 'Peças', 'fa-cogs');
                    
                    // Exibir seção de Proteções
                    displaySubcategories($protecoes, 'Proteções', 'fa-shield-alt');
                    
                } else {
                    echo '<div class="empty-state">';
                    echo '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                    echo '<h4 class="text-muted">Nenhuma subcategoria encontrada</h4>';
                    echo '<p class="text-muted">Clique no botão "Cadastrar" para adicionar uma nova subcategoria</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>
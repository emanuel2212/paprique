<?php
$id_produto = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
require 'Produtos.php';

// Obter dados do produto para edição
$produto = new Produtos();
$produto->setId($id_produto);
$dadosProduto = $produto->view();

if (!$dadosProduto) {
    $_SESSION['msg'] = '<div class="alert alert-danger">Produto não encontrado!</div>';
    header("Location: ?page=viewProduto");
    exit();
}

// Processar formulário de edição
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($formData['EditProduto'])) {
    $atualizarProduto = new Produtos();
    $atualizarProduto->setFormData($formData);
    
    if ($atualizarProduto->edit()) {
        $_SESSION['msg'] = '<div class="alert alert-success">Produto atualizado com sucesso!</div>';
        header("Location: ?page=viewProduto");
        exit();
    } else {
        $errorMessage = '<div class="alert alert-danger">Erro ao atualizar produto!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar Produto</title>
    <style>
        .header-gradient {
            background: linear-gradient(135deg, #6c757d, #495057);
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
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid p-4 header-gradient text-white shadow mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-box-edit me-2"></i>Editar Produto</h1>
            <a href="?page=viewProduto" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="form-container">
            <?php if (!empty($errorMessage)) echo $errorMessage; ?>
            
            <form method="POST" action="" class="row g-3" enctype="multipart/form-data">
                <input type="hidden" name="id_produto" value="<?= $id_produto ?>">
                
                <!-- Categoria e Subcategoria -->
                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">
                        <i class="fas fa-tags me-1"></i> Categoria
                    </label>
                    <select name="id_categoria" id="id_categoria" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="18" <?= ($dadosProduto['id_categoria'] == 18) ? 'selected' : '' ?>>Peças</option>
                        <option value="19" <?= ($dadosProduto['id_categoria'] == 19) ? 'selected' : '' ?>>Proteções</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="id_subcategoria" class="form-label">
                        <i class="fas fa-tag me-1"></i> Subcategoria
                    </label>
                    <select name="id_subcategoria" id="id_subcategoria" class="form-select" required>
                        <option value="">Selecione uma categoria primeiro</option>
                    </select>
                </div>

                <!-- Nome e Marca -->
                <div class="col-md-6">
                    <label for="nome_produto" class="form-label">
                        <i class="fas fa-box me-1"></i> Nome do Produto
                    </label>
                    <input type="text" name="nome_produto" class="form-control" placeholder="Nome do produto" required
                        value="<?= htmlspecialchars($dadosProduto['nome_produto'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="marca" class="form-label">
                        <i class="fas fa-trademark me-1"></i> Marca
                    </label>
                    <input type="text" name="marca" class="form-control" placeholder="Marca do produto" required
                        value="<?= htmlspecialchars($dadosProduto['marca'] ?? '') ?>">
                </div>

                <!-- Preço e Imagem -->
                <div class="col-md-6">
                    <label for="preco" class="form-label">
                        <i class="fas fa-tag me-1"></i> Preço (€)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                        <input type="number" name="preco" class="form-control" placeholder="0.00" step="0.01" min="0" required
                            value="<?= htmlspecialchars($dadosProduto['preco'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="imagem" class="form-label">
                        <i class="fas fa-image me-1"></i> Imagem do Produto
                    </label>
                    <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
                    <?php if (!empty($dadosProduto['id_imagem'])): ?>
                        <div class="mt-2">
                            <small>Imagem atual:</small><br>
                            <img src="<?= htmlspecialchars($dadosProduto['id_imagem']) ?>" class="img-thumbnail mt-2" style="max-height: 100px;">
                        </div>
                    <?php endif; ?>
                    <img id="imagePreview" class="preview-image img-thumbnail" src="#" alt="Pré-visualização da imagem">
                </div>

                <!-- Descrição -->
                <div class="col-12">
                    <label for="descricao" class="form-label">
                        <i class="fas fa-align-left me-1"></i> Descrição
                    </label>
                    <textarea name="descricao" class="form-control" rows="4" placeholder="Descrição do produto"><?= htmlspecialchars($dadosProduto['descricao'] ?? '') ?></textarea>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" href="?page=viewProduto" name="EditProduto" class="btn btn-primary btn-lg w-100 btn-submit"
                        class="fas fa-save me-2" value="Atualizar Produto">      
                        <i class="fas fa-save me-2"></i> Atualizar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Dados das subcategorias organizados por categoria
        const subcategorias = {
            '18': [ // Peças
                {value: '9', text: 'Trucks'},
                {value: '10', text: 'Rodas'},
                {value: '13', text: 'Rolamentos'},
                {value: '14', text: 'Amortecedores'},
                {value: '15', text: 'Porcas e Parafusos'},
                {value: '16', text: 'Cera de Skate'},
                {value: '17', text: 'Chave de Skate'}
            ],
            '19': [ // Proteções
                {value: '18', text: 'Capacete'},
                {value: '19', text: 'Joelheira'},
                {value: '22', text: 'Protetor de Punho'},
                {value: '23', text: 'Cotoveleira'}
            ]
        };

        // Quando a categoria mudar
        $('#id_categoria').change(function() {
            const categoriaId = $(this).val();
            const $subcategoriaSelect = $('#id_subcategoria');
            
            $subcategoriaSelect.empty();
            $subcategoriaSelect.append('<option value="">Selecione...</option>');
            
            if (categoriaId && subcategorias[categoriaId]) {
                subcategorias[categoriaId].forEach(function(subcat) {
                    const isSelected = subcat.value == '<?= $dadosProduto['id_subcategoria'] ?? '' ?>' ? 'selected' : '';
                    $subcategoriaSelect.append(`<option value="${subcat.value}" ${isSelected}>${subcat.text}</option>`);
                });
            }
        });

        // Disparar o evento change ao carregar
        $('#id_categoria').trigger('change');

        // Pré-visualização da imagem
        $('#imagem').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });
    });
    </script>
</body>
</html>
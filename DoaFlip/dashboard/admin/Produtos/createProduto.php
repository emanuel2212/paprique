<?php
require './bd/Connection.php'; 
require 'Produtos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar dados do formulário
    $dados = [
        'id_categoria' => $_POST['id_categoria'],
        'id_subcategoria' => $_POST['id_subcategoria'],
        'marca' => $_POST['marca'],
        'nome_produto' => $_POST['nome_produto'],
        'descricao' => $_POST['descricao'],
        'preco' => $_POST['preco']
    ];

    // Processar imagem
    if (!empty($_FILES['imagem']['name'])) {
        $pasta = '../images/';
        $nome_arquivo = uniqid() . '_' . $_FILES['imagem']['name'];
        move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nome_arquivo);
        $dados['link_imagem'] = $nome_arquivo;
    }

    // Criar conexão
    $conn = new Connection(); // Agora vai funcionar
    $pdo = $conn->connect();

    try {
        // Inserir produto
        $sql = "INSERT INTO produtos 
               (id_categoria, id_subcategoria, marca, nome_produto, descricao, preco) 
               VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $dados['id_categoria'],
            $dados['id_subcategoria'],
            $dados['marca'],
            $dados['nome_produto'],
            $dados['descricao'],
            $dados['preco']
        ]);

        $id_produto = $pdo->lastInsertId();

        // Inserir imagem se existir
        if (!empty($dados['link_imagem'])) {
            $sql_img = "INSERT INTO imagens (link_imagem, id_produto) VALUES (?, ?)";
            $stmt_img = $pdo->prepare($sql_img);
            $stmt_img->execute([$dados['link_imagem'], $id_produto]);
        }

        $_SESSION['msg'] = '<div class="alert alert-success">Produto cadastrado com sucesso!</div>';
        header("Location: ?page=viewProduto");
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg'] = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
        header("Location: ?page=createProduto");
        exit();
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Cadastrar Produto</title>
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
            <h1 class="m-0"><i class="fas fa-box me-2"></i>Cadastrar Produto</h1>
            <a href="?page=viewProduto" class="btn btn-outline-light">
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

            <form method="POST" action="" class="row g-3" enctype="multipart/form-data">
                <!-- Categoria -->
                <div class="col-md-6">
                    <label for="id_categoria" class="form-label">
                        <i class="fas fa-tags me-1"></i> Categoria
                    </label>
                    <select name="id_categoria" id="id_categoria" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="18" <?= (isset($formData['id_categoria']) && $formData['id_categoria'] == '18') ? 'selected' : '' ?>>Peças</option>
                        <option value="19" <?= (isset($formData['id_categoria']) && $formData['id_categoria'] == '19') ? 'selected' : '' ?>>Proteções</option>
                    </select>
                </div>

                <!-- SubCategoria -->
                <div class="col-md-6">
                    <label for="id_subcategoria" class="form-label">
                        <i class="fas fa-tag me-1"></i> Subcategoria
                    </label>
                    <select name="id_subcategoria" id="id_subcategoria" class="form-select" required>
                        <option value="">Selecione uma categoria primeiro</option>
                    </select>
                </div>

                <!-- Nome do Produto -->
                <div class="col-12">
                    <label for="nome_produto" class="form-label">
                        <i class="fas fa-box me-1"></i> Nome do Produto
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        <input type="text" name="nome_produto" class="form-control" placeholder="Digite o nome do produto" required
                            value="<?= isset($formData['nome_produto']) ? htmlspecialchars($formData['nome_produto']) : '' ?>">
                    </div>
                </div>

                <!-- Marca -->
                <div class="col-md-6">
                    <label for="marca" class="form-label">
                        <i class="fas fa-trademark me-1"></i> Marca
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-industry"></i></span>
                        <input type="text" name="marca" class="form-control" placeholder="Digite a marca" required
                            value="<?= isset($formData['marca']) ? htmlspecialchars($formData['marca']) : '' ?>">
                    </div>
                </div>

                <!-- Preço -->
                <div class="col-md-6">
                    <label for="preco" class="form-label">
                        <i class="fas fa-tag me-1"></i> Preço (€)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                        <input type="number" name="preco" class="form-control" placeholder="0.00" step="0.01" min="0" required
                            value="<?= isset($formData['preco']) ? htmlspecialchars($formData['preco']) : '' ?>">
                    </div>
                </div>

                <!-- Descrição -->
                <div class="col-12">
                    <label for="descricao" class="form-label">
                        <i class="fas fa-align-left me-1"></i> Descrição
                    </label>
                    <textarea name="descricao" class="form-control" rows="3" placeholder="Digite a descrição do produto" required><?= isset($formData['descricao']) ? htmlspecialchars($formData['descricao']) : '' ?></textarea>
                </div>

                <!-- Imagem -->
                <div class="col-12">
                    <label for="imagem" class="form-label">
                        <i class="fas fa-image me-1"></i> Imagem do Produto
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-upload"></i></span>
                        <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*" required>
                    </div>
                </div> 

                <div class="col-12 mt-4">
                    <button type="submit" href="?page=viewProduto" name="AddProduto" class="btn btn-success btn-lg w-100 btn-submit"
                        class="fas fa-save me-2" value="Cadastrar Produto">
                        <i class="fas fa-save me-2"></i> Cadastrar Produto
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
                {value: '15', text: 'Rolamentos'},
                {value: '16', text: 'Amortecedores'},
                {value: '17', text: 'Porcas e Parafusos'},
                {value: '18', text: 'Cera de Skate'},
                {value: '19', text: 'Chave de Skate'},
                {value: '24', text: 'Tábua'},
                {value: '25', text: 'Lixa'}
            ],
            '19': [ // Proteções
                {value: '13', text: 'Capacete'},
                {value: '14', text: 'Joelheira'},
                {value: '22', text: 'Protetor de Punho'},
                {value: '23', text: 'Cotoveleira'}
            ]
        };

        // Quando a categoria mudar
        $('#id_categoria').change(function() {
            const categoriaId = $(this).val();
            const $subcategoriaSelect = $('#id_subcategoria');
            
            // Limpar e desabilitar se não houver categoria selecionada
            $subcategoriaSelect.empty();
            
            if (!categoriaId) {
                $subcategoriaSelect.append('<option value="">Selecione uma categoria primeiro</option>');
                return;
            }
            
            // Adicionar opção padrão
            $subcategoriaSelect.append('<option value="">Selecione...</option>');
            
            // Adicionar subcategorias correspondentes
            subcategorias[categoriaId].forEach(function(subcat) {
                // Verificar se esta subcategoria deve ser selecionada
                const selected = <?= isset($formData['id_subcategoria']) ? "'" . $formData['id_subcategoria'] . "'" : 'null' ?>;
                const isSelected = selected && selected == subcat.value ? 'selected' : '';
                
                $subcategoriaSelect.append(`<option value="${subcat.value}" ${isSelected}>${subcat.text}</option>`);
            });
        });
        
        // Disparar o evento change ao carregar se já houver uma categoria selecionada
        if ($('#id_categoria').val()) {
            $('#id_categoria').trigger('change');
        }
    });
    </script>
</body>
</html>
<?php
// Processa remoção do carrinho
if (isset($_GET['remover'])) {
    $id_produto = intval($_GET['remover']);
    if (isset($_SESSION['carrinho'][$id_produto])) {
        unset($_SESSION['carrinho'][$id_produto]);
        $_SESSION['mensagem'] = "Produto removido do carrinho!";
        header("Location: ?page=carrinho");
        exit();
    }
}

// Processa atualização do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_carrinho'])) {
    foreach ($_POST['quantidade'] as $id_prod => $quantidade) {
        $id_prod = intval($id_prod);
        $quantidade = intval($quantidade);
        
        if ($quantidade > 0 && isset($_SESSION['carrinho'][$id_prod])) {
            $_SESSION['carrinho'][$id_prod]['quantidade'] = $quantidade;
        } elseif (isset($_SESSION['carrinho'][$id_prod])) {
            unset($_SESSION['carrinho'][$id_prod]);
        }
    }
    
    $_SESSION['mensagem'] = "Carrinho atualizado com sucesso!";
    header("Location: ?page=carrinho");
    exit();
}
?>

<section class="shop checkout section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><i class="ti-shopping-cart"></i> Seu Carrinho</h2>

                <?php if (isset($_SESSION['mensagem'])): ?>
                    <div class="alert alert-info alert-dismissible fade show mb-4">
                        <?php echo htmlspecialchars($_SESSION['mensagem']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['mensagem']); ?>
                <?php endif; ?>

                <?php if (empty($_SESSION['carrinho'])): ?>
                    <div class="text-center py-5 bg-light rounded">
                        <h5 class="text-muted">Seu carrinho está vazio</h5>
                        <p class="text-muted mb-4">Adicione produtos para começar a comprar</p>
                        <a href="index.php" class="btn btn-primary text-white">Continuar Comprando</a>
                    </div>
                <?php else: ?>
                    <div class="carrinho-container">
                        <form method="post" action="?page=carrinho" id="form-carrinho">
                            <table class="carrinho-table">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço</th>
                                        <th>Quantidade</th>
                                        <th>Total</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_carrinho = 0;
                                    require_once "./Produtos.php";
                                    $produtos_class = new Produtos();

                                    foreach ($_SESSION['carrinho'] as $id_prod => $item):
                                        $produto = $produtos_class->getById($id_prod);
                                        if ($produto):
                                            $subtotal = $produto['preco'] * $item['quantidade'];
                                            $total_carrinho += $subtotal;
                                    ?>
                                            <tr data-produto-id="<?php echo $id_prod; ?>">
                                                <td data-label="Produto">
                                                    <div class="produto-info">
                                                        <img src="./images/<?php echo htmlspecialchars($produto['link_imagem'] ?? 'default-product.jpg'); ?>"
                                                            alt="<?php echo htmlspecialchars($produto['nome_produto']); ?>"
                                                            class="produto-imagem">
                                                        <div>
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($produto['nome_produto']); ?></h6>
                                                            <input type="hidden" class="preco-produto" value="<?php echo $produto['preco']; ?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td data-label="Preço" class="preco-unitario">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                                <td data-label="Quantidade">
                                                    <input type="number" name="quantidade[<?php echo $id_prod; ?>]"
                                                        value="<?php echo $item['quantidade']; ?>"
                                                        min="1" class="form-control quantidade-input" data-produto-id="<?php echo $id_prod; ?>">
                                                </td>
                                                <td data-label="Total" class="subtotal">€<?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                                <td data-label="Ações">
                                                    <a href="?page=carrinho&remover=<?php echo $id_prod; ?>" class="btn btn-sm btn-danger text-white">
                                                        <i class="fas fa-trash-alt"></i> Remover
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>

                            <div class="text-end mt-4">
                                <h5 class="carrinho-total">Total: <span id="total-geral">€<?php echo number_format($total_carrinho, 2, ',', '.'); ?></span></h5>
                            </div>

                            <div class="botoes-carrinho">
                                <a href="index.php" class="btn btn-outline-primary text-white">
                                    <i class="ti-arrow-left me-2"></i> Continuar Comprando
                                </a>
                                <div class="d-flex gap-3">
                                    <button type="submit" name="atualizar_carrinho" class="btn btn-secondary text-white">
                                        <i class="ti-reload me-2"></i> Atualizar Carrinho
                                    </button>
                                    <a href="?page=checkout" class="btn btn-primary text-white">
                                        <i class="ti-credit-card me-2"></i> Finalizar Compra
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Atualiza os totais quando a quantidade é alterada
        $('.quantidade-input').on('change', function() {
            var produtoId = $(this).data('produto-id');
            var quantidade = parseInt($(this).val());
            var preco = parseFloat($('tr[data-produto-id="' + produtoId + '"] .preco-produto').val());

            // Valida a quantidade mínima
            if (quantidade < 1) {
                $(this).val(1);
                quantidade = 1;
            }

            // Calcula o subtotal
            var subtotal = quantidade * preco;

            // Atualiza o subtotal na linha
            $('tr[data-produto-id="' + produtoId + '"] .subtotal').text('€' + subtotal.toFixed(2).replace('.', ','));

            // Recalcula o total geral
            calcularTotalGeral();
        });

        // Função para calcular o total geral
        function calcularTotalGeral() {
            var total = 0;

            $('.quantidade-input').each(function() {
                var produtoId = $(this).data('produto-id');
                var quantidade = parseInt($(this).val());
                var preco = parseFloat($('tr[data-produto-id="' + produtoId + '"] .preco-produto').val());
                total += quantidade * preco;
            });

            $('#total-geral').text('€' + total.toFixed(2).replace('.', ','));
        }
    });
</script>

<style>
    .carrinho-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    
    .carrinho-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .carrinho-table th {
        background-color: #f8f9fa;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }
    
    .carrinho-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }
    
    .produto-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .produto-imagem {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .quantidade-input {
        width: 70px;
        text-align: center;
    }
    
    .botoes-carrinho {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .carrinho-total {
        font-size: 1.25rem;
        color: #333;
    }
    
    @media (max-width: 768px) {
        .carrinho-table thead {
            display: none;
        }
        
        .carrinho-table tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .carrinho-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: none;
        }
        
        .carrinho-table td::before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 10px;
        }
        
        .produto-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        
        .botoes-carrinho {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
<?php
require_once "/Connection.php";
require_once "./Produtos.php";

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$query = trim($_GET['query'] ?? '');

if (strlen($query) < 2) {
    echo json_encode(['error' => 'Query too short']);
    exit;
}

$produtos = new Produtos();
$response = [];

// Buscar sugestões baseadas em subcategorias, marcas e termos populares
if ($action === 'suggestions') {
    $response['suggestions'] = getSearchSuggestions($query, $produtos);
    
    // Também buscar produtos para mostrar na prévia
    $response['products'] = searchProducts($query, $produtos, 6);
}

// Buscar apenas produtos para a página de resultados
if ($action === 'products') {
    $response['products'] = searchProducts($query, $produtos);
}

echo json_encode($response);

function getSearchSuggestions($query, $produtos) {
    $suggestions = [];
    
    // Buscar subcategorias relacionadas
    $subcategorias = $produtos->conn->query(
        "SELECT nome_subcategoria FROM subcategorias 
         WHERE nome_subcategoria LIKE '%$query%' 
         LIMIT 5"
    )->fetchAll(PDO::FETCH_COLUMN);
    
    // Buscar marcas relacionadas
    $marcas = $produtos->conn->query(
        "SELECT nome_marca FROM marca 
         WHERE nome_marca LIKE '%$query%' 
         LIMIT 5"
    )->fetchAll(PDO::FETCH_COLUMN);
    
    // Termos populares pré-definidos (poderiam vir de uma tabela no banco)
    $popularTerms = [
        'skate completo', 'skate cruiser', 'skate street', 
        'truck independent', 'roda 52mm', 'capacete proteção'
    ];
    
    // Filtrar termos populares que contenham a query
    $matchedTerms = array_filter($popularTerms, function($term) use ($query) {
        return stripos($term, $query) !== false;
    });
    
    // Combinar todas as sugestões
    $suggestions = array_merge($subcategorias, $marcas, array_slice($matchedTerms, 0, 5));
    
    // Remover duplicados e limitar a 8 sugestões
    $suggestions = array_unique($suggestions);
    $suggestions = array_slice($suggestions, 0, 8);
    
    return $suggestions;
}

function searchProducts($query, $produtos, $limit = null) {
    $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            WHERE P.nome_produto LIKE :query 
               OR P.descricao LIKE :query
               OR M.nome_marca LIKE :query
            ORDER BY P.id_produto DESC";
    
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    $stmt = $produtos->conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bindParam(':query', $searchTerm);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
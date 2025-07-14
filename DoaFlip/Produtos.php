<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */
ob_start(); // Inicia o buffer de saída para capturar mensagens de erro e redirecionamentos

class Produtos extends Connection
{

    protected $conn; // Removido o type hint para maior compatibilidade
    public array $formData;
    public int $id;

    public function __construct()
    {
        $this->conn = $this->connect(); // Inicializa a conexão
    }

    public function setFormData(array $formData): void
    {
        // Atribui os dados do formulário à propriedade formData.
        $this->formData = $formData;
    }


    public function setId(int $id_produto): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_produto;
    }
    public function countProdutosBySubcategoria($id_subcategoria)
    {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE id_subcategoria = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_subcategoria]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function listByMarca($nome_marca)
    {
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            WHERE M.nome_marca = ?
            ORDER BY P.id_produto DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$nome_marca]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listBySubcategoria($nome_subcategoria)
    {
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            JOIN subcategorias S ON P.id_subcategoria = S.id_subcategoria
            WHERE S.nome_subcategoria = ?
            ORDER BY P.id_produto DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$nome_subcategoria]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($id_produto, $limit = 4)
    {
        // Primeiro obtemos a categoria e subcategoria do produto atual
        $sql = "SELECT id_categoria, id_subcategoria FROM produtos WHERE id_produto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_produto]);
        $current_product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_product) {
            return [];
        }

        // Buscamos produtos da mesma categoria e subcategoria (excluindo o produto atual)
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            WHERE (P.id_categoria = :id_categoria OR P.id_subcategoria = :id_subcategoria)
            AND P.id_produto != :id_produto
            ORDER BY RAND()
            LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_categoria', $current_product['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':id_subcategoria', $current_product['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindValue(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function list(): array
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados dos categorias, limitando o resultado a 40 registros.
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
        FROM produtos P 
        LEFT JOIN imagens I ON I.id_produto = P.id_produto
        LEFT JOIN marca M ON M.id_marca = P.id_marca
        ORDER BY P.id_produto DESC";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }
    public function listMarcas()
    {
        $sql = "SELECT * FROM marca ORDER BY nome_marca";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listSubcategoriasByCategoria($id_categoria)
    {
        $sql = "SELECT * FROM subcategorias WHERE id_categoria = ? ORDER BY nome_subcategoria";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_categoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($query, $limit = null)
    {
        // Primeiro verifica se é uma pesquisa por categoria
        $categoriaIds = [];
        $queryLower = strtolower($query);

        if ($queryLower === 'proteções' || $queryLower === 'protecoes') {
            $categoriaIds = [19]; // ID da categoria de proteções
        } elseif ($queryLower === 'skate' || $queryLower === 'skates') {
            $categoriaIds = [18]; // ID da categoria de skates
        }

        // Construção da consulta SQL
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca
            LEFT JOIN categorias C ON P.id_categoria = C.id_categoria
            WHERE (P.nome_produto LIKE ? 
               OR P.descricao LIKE ?
               OR M.nome_marca LIKE ?";

        $params = ["%$query%", "%$query%", "%$query%"];

        // Se for pesquisa por categoria, adiciona condição
        if (!empty($categoriaIds)) {
            $placeholders = implode(',', array_fill(0, count($categoriaIds), '?'));
            $sql .= " OR P.id_categoria IN ($placeholders)";
            $params = array_merge($params, $categoriaIds);
        }

        $sql .= ") ORDER BY P.id_produto DESC";

        // Adicionando LIMIT se necessário
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }

        // Preparando e executando a consulta
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(): bool
    {
        $this->conn = $this->connect();

        try {
            // Debug: Verificar conexão
            error_log("Tentando conectar ao banco de dados");

            // Iniciar transação
            $this->conn->beginTransaction();
            error_log("Transação iniciada");

            // 1. Verificar ou criar marca
            $marca = trim($this->formData['marca']);
            $stmt = $this->conn->prepare("SELECT id_marca FROM marca WHERE nome_marca = ?");
            $stmt->execute([$marca]);
            $marca_existente = $stmt->fetch();

            if ($marca_existente) {
                $id_marca = $marca_existente['id_marca'];
            } else {
                $stmt = $this->conn->prepare("INSERT INTO marca (nome_marca) VALUES (?)");
                $stmt->execute([$marca]);
                $id_marca = $this->conn->lastInsertId();
            }

            // 1. Inserir imagem se existir
            $id_imagem = null;
            if (!empty($this->formData['link_imagem'])) {
                $sql = "INSERT INTO imagens (link_imagem) VALUES (?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$this->formData['link_imagem']]);
                $id_imagem = $this->conn->lastInsertId();
                error_log("Imagem inserida com ID: " . $id_imagem);
            }

            // 2. Inserir produto
            $sql = "INSERT INTO produtos (
            id_categoria, id_subcategoria, id_imagem, 
            marca, nome_produto, descricao, preco
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute([
                $this->formData['id_categoria'],
                $this->formData['id_subcategoria'],
                $id_imagem,
                $this->formData['marca'],
                $this->formData['nome_produto'],
                $this->formData['descricao'],
                $this->formData['preco']
            ]);

            if (!$success) {
                throw new Exception("Erro ao inserir produto: " . implode(", ", $stmt->errorInfo()));
            }

            $id_produto = $this->conn->lastInsertId();
            error_log("Produto inserido com ID: " . $id_produto);

            // 3. Atualizar imagem com ID do produto
            if ($id_imagem) {
                $sql = "UPDATE imagens SET id_produto = ? WHERE id_imagem = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id_produto, $id_imagem]);
                error_log("Imagem atualizada com ID do produto");
            }

            // Commit
            $this->conn->commit();
            error_log("Transação concluída com sucesso");
            return true;
        } catch (Exception $e) {
            error_log("ERRO: " . $e->getMessage());
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
                error_log("Transação revertida");
            }
            return false;
        }
    }


    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um categoria específico.
        $sql = "SELECT * FROM produtos
                WHERE id_produto = :id_produto
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_produto', $this->id);

        // Executa a consulta SQL.
        $resultUser->execute();

        // Retorna os dados do categoria ou false se não encontrado.
        return $resultUser->fetch();
    }

    /**
     * Edita as informações de um categoria existente.
     * 
     * @return bool Retorna true se o categoria for atualizado com sucesso, false caso contrário.
     */
    public function edit(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para atualizar os dados do categoria específico.
        $sql = "UPDATE produtos SET 
            nome_produto = :nome_produto,
            descricao = :descricao,
            preco = :preco,
            id_categoria = :id_categoria,
            id_subcategoria = :id_subcategoria,
            id_marca = :id_marca,
            id_imagem = :id_imagem
            WHERE id_produto = :id_produto";

        // Prepara a consulta SQL.
        $editProduto = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editProduto->bindValue(':nome_produto', $this->formData['nome_produto']);
        $editProduto->bindValue(':descricao', $this->formData['descricao']);
        $editProduto->bindValue(':preco', $this->formData['preco']);
        $editProduto->bindValue(':id_categoria', $this->formData['id_categoria']);
        $editProduto->bindValue(':id_subcategoria', $this->formData['id_subcategoria']);
        $editProduto->bindValue(':id_marca  ', $this->formData['id_marca  ']);
        $editProduto->bindValue(':id_imagem', $this->formData['id_imagem'] ?? null);
        $editProduto->bindValue(':id_produto', $this->formData['id_produto']);


        // Executa a consulta SQL.
        $editProduto->execute();

        // Verifica se a atualização foi bem-sucedida e retorna o resultado.
        if ($editProduto->rowCount()) {
            return true;
        } else {
            return false;
        }
    }
    public function filtrarProdutos($filtro, $valor)
    {
        $sql = "SELECT P.*, I.link_imagem, IFNULL(M.nome_marca, 'Sem marca') as marca 
            FROM produtos P 
            LEFT JOIN imagens I ON I.id_produto = P.id_produto
            LEFT JOIN marca M ON M.id_marca = P.id_marca";

        switch ($filtro) {
            case 'marca':
                $sql .= " WHERE M.nome_marca = :valor";
                break;
            case 'subcategoria':
                $sql .= " JOIN subcategorias S ON P.id_subcategoria = S.id_subcategoria
                     WHERE S.nome_subcategoria = :valor";
                break;
            case 'categoria':
                $sql .= " JOIN categorias C ON P.id_categoria = C.id_categoria
                     WHERE C.nome_categoria = :valor";
                break;
            default:
                return [];
        }

        $sql .= " ORDER BY P.id_produto DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exclui um categoria do banco de dados.
     * 
     * @return bool Retorna true se o categoria for excluído com sucesso, false caso contrário.
     */
    public function delete(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para excluir um status de encomenda específico baseado no seu ID.
        $sql = "DELETE FROM produtos WHERE id_produto = :id_produto LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_produto', $this->id);

        // Executa a consulta SQL.
        return $deleteUser->execute();
    }

    public function getById($id)
    {
        $this->conn = $this->connect();
        $sql = "SELECT P.*, I.link_imagem FROM produtos P INNER JOIN imagens I ON I.id_produto = P.id_produto 
            WHERE P.id_produto = :id ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

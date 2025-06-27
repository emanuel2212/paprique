<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */

 require_once './bd/Connection.php';

class Produtos extends Connection
{
    
    /**
     * Conexão com o banco de dados.
     * @var object
     */
    public object $conn;

    /**
     * Dados do formulário para criação e edição de um novo categoria.
     * @var array
     */
    public array $formData;

    /**
     * ID do categoria para operações específicas (visualização e edição).
     * @var int
     */
    public int $id;

    /**
     * Define os dados do formulário para criação de um novo categoria.
     * 
     * @param array $formData Dados do formulário contendo informações do categoria.
     * @return void
     */
    public function setFormData(array $formData): void
    {
        // Atribui os dados do formulário à propriedade formData.
        $this->formData = $formData;
    }

    /**
     * Define o ID do categoria para operações que necessitam de um identificador específico.
     * 
     * @param int $id Identificador único do categoria.
     * @return void
     */
    public function setId(int $id_produto): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_produto;
    }

    /** 
     * Lista os categorias cadastrados no banco de dados.
     * 
     * @return array Retorna um array contendo os dados dos categorias.
     */
    public function list(): array
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados dos categorias, limitando o resultado a 40 registros.
        $sql = "SELECT * FROM `produtos` WHERE 1 ORDER BY id_produto LIMIT 40";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }


  public function create(): bool {
    $this->conn = $this->connect();
    
   // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para inserir um novo categoria.
        $sql = "INSERT INTO produtos (id_categoria, id_subcategoria, id_imagem, marca, nome_produto, descricao, preco) 
        VALUES (:id_categoria, :id_subcategoria, :id_imagem, :marca, :nome_produto, :descricao, :preco)";

        // Prepara a consulta SQL para inserção de dados.
        $AddProduto = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddProduto->bindParam(':id_categoria', $this->formData['id_categoria']);
        $AddProduto->bindParam(':id_subcategoria', $this->formData['id_subcategoria']);
        $AddProduto->bindParam(':id_imagem', $this->formData['id_imagem']);
        $AddProduto->bindParam(':marca', $this->formData['marca']);
        $AddProduto->bindParam(':nome_produto', $this->formData['nome_produto']);
        $AddProduto->bindParam(':descricao', $this->formData['descricao']);
        $AddProduto->bindParam(':preco', $this->formData['preco']);


        // Executa a consulta SQL.
        $AddProduto->execute();

        // Verifica se a inserção foi bem-sucedida e retorna o resultado.
        if ($AddProduto->rowCount()) {
            return true;
        } else {
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
            marca = :marca,
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
        $editProduto->bindValue(':marca', $this->formData['marca']);
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
    
    public function getById($id) {
    $this->conn = $this->connect();
    $sql = "SELECT * FROM produtos WHERE id_produto = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
   
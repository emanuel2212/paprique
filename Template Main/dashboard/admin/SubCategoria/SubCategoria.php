<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */

 require_once './bd/Connection.php';

class SubCategorias extends Connection
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
    public function setId(int $id_subcategoria): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_subcategoria;
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
        $sql = "SELECT * FROM `subcategorias` WHERE 1 ORDER BY id_subcategoria LIMIT 40";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }


  public function create(): bool {
    $this->conn = $this->connect();
    
    // Verifica se recebeu os dados necessários
    if (!isset($this->formData['id_categoria']) || !isset($this->formData['nome_subcategoria'])) {
        return false;
    }

    // Prepara a query
    $sql = "INSERT INTO subcategorias (id_categoria, nome_subcategoria) 
            VALUES (:id_categoria, :nome_subcategoria)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $this->formData['id_categoria'], PDO::PARAM_INT);
    $stmt->bindParam(':nome_subcategoria', $this->formData['nome_subcategoria'], PDO::PARAM_STR);
    
    return $stmt->execute();
}


    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um categoria específico.
        $sql = "SELECT id_subcategoria, nome_subcategoria
                FROM subcategorias
                WHERE id_subcategoria = :id_subcategoria
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_subcategoria', $this->id);

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
        $sql = "UPDATE subcategorias SET nome_subcategoria = :nome_subcategoria
                WHERE id_subcategoria = :id_subcategoria
                LIMIT 1";

        // Prepara a consulta SQL.
        $editCategoria = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editCategoria->bindParam(':nome_subcategoria', $this->formData['nome_subcategoria']);
        $editCategoria->bindParam(':id_subcategoria', $this->formData['id_subcategoria']);

        // Executa a consulta SQL.
        $editCategoria->execute();

        // Verifica se a atualização foi bem-sucedida e retorna o resultado.
        if ($editCategoria->rowCount()) {
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

        // Consulta SQL para excluir um subcategoria específico baseado no seu ID.
        $sql = "DELETE FROM subcategorias WHERE id_subcategoria = :id_subcategoria LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_subcategoria', $this->id);
 
        // Executa a consulta SQL.
        return $deleteUser->execute();
    }
}

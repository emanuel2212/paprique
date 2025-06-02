<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */

 require_once '../bd/Connection.php';

class Categorias extends Connection
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
    public function setId(int $id_categoria): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_categoria;
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
        $sql = "SELECT * FROM `categorias` WHERE 1 ORDER BY id_categoria LIMIT 40";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }

    /**
     * Cria um novo categoria no banco de dados.
     * 
     * @return bool Retorna true se o categoria for criado com sucesso, false caso contrário.
     */
    public function create(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para inserir um novo categoria.
        $sql = "INSERT INTO categorias (nome_categoria) VALUES (:nome_categoria)";

        // Prepara a consulta SQL para inserção de dados.
        $AddCategoria = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddCategoria->bindParam(':nome_categoria', $this->formData['nome_categoria']);

        // Executa a consulta SQL.
        $AddCategoria->execute();

        // Verifica se a inserção foi bem-sucedida e retorna o resultado.
        if ($AddCategoria->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function listAll() {
    $this->conn = $this->connect();
    $sql = "SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Visualiza os detalhes de um categoria específico.
     * 
     * Recupera os dados de um categoria específico baseado no seu ID.
     * 
     * @return array|false Retorna um array contendo os dados do categoria se encontrado, ou false se não existir.
     */
    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um categoria específico.
        $sql = "SELECT id_categoria, nome_categoria
                FROM categorias
                WHERE id_categoria = :id_categoria
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_categoria', $this->id);

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
        $sql = "UPDATE categorias SET nome_categoria = :nome_categoria
                WHERE id_categoria = :id_categoria
                LIMIT 1";

        // Prepara a consulta SQL.
        $editCategoria = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editCategoria->bindParam(':nome_categoria', $this->formData['nome_categoria']);
        $editCategoria->bindParam(':id_categoria', $this->formData['id_categoria']);

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

        // Consulta SQL para excluir um categoria específico baseado no seu ID.
        $sql = "DELETE FROM categorias WHERE id_categoria = :id_categoria LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_categoria', $this->id);

        // Executa a consulta SQL.
        return $deleteUser->execute();
    }
}

<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */

 require_once '../bd/Connection.php';

class Utilizador extends Connection
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
    public function setId(int $id_utilizador): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_utilizador;
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

        // Consulta SQL para selecionar os dados dos utilizadores, limitando o resultado a 40 registros.
        $sql = "SELECT * FROM `utilizador` WHERE 1 ORDER BY id_utilizador LIMIT 40";

        // Prepara a consulta SQL.
        $stmt = $this->conn->prepare($sql);

        // Executa a consulta no banco de dados.
        $stmt->execute();

        // Retorna os resultados da consulta como um array.
        return $stmt->fetchAll();
    }

    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um utilizador específico.
        $sql = "SELECT id_utilizador, id_tipo_utilizador, username, email, morada, telefone, codigo_postal, nif, data_criado
                FROM utilizador
                WHERE id_utilizador = :id_utilizador";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_utilizador', $this->id);

        // Executa a consulta SQL.
        $resultUser->execute();

        // Retorna os dados do categoria ou false se não encontrado.
        return $resultUser->fetch();
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

    // Hash da senha usando SHA1 
    $hashedPassword = sha1($this->formData['password']);

    // Consulta SQL para inserir um novo utilizador.
    $sql = "INSERT INTO utilizador (id_tipo_utilizador, username, email, password, morada, telefone, codigo_postal, data_criado, nif) VALUES (:id_tipo_utilizador, :username, :email, :password, :morada, :telefone, :codigo_postal, NOW(), :nif)";

    // Prepara a consulta SQL para inserção de dados.
    $AddUtilizador = $this->conn->prepare($sql);

    // Associa os valores das propriedades ao SQL.
    $AddUtilizador->bindParam(':id_tipo_utilizador', $this->formData['id_tipo_utilizador']);
    $AddUtilizador->bindParam(':username', $this->formData['username']);
    $AddUtilizador->bindParam(':email', $this->formData['email']);
    $AddUtilizador->bindParam(':password', $hashedPassword); // Usando a senha com hash
    $AddUtilizador->bindParam(':morada', $this->formData['morada']);
    $AddUtilizador->bindParam(':telefone', $this->formData['telefone']);
    $AddUtilizador->bindParam(':codigo_postal', $this->formData['codigo_postal']);
    $AddUtilizador->bindParam(':nif', $this->formData['nif']);

    // Executa a consulta SQL.
    $AddUtilizador->execute();

    // Verifica se a inserção foi bem-sucedida e retorna o resultado.
    return $AddUtilizador->rowCount() > 0;
}
    public function listAll() {
    $this->conn = $this->connect();
    $sql = "SELECT id_utilizador, username FROM utilizador ORDER BY username";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
   public function getById($id) {
    $this->conn = $this->connect();
    $sql = "SELECT * FROM utilizador WHERE id_utilizador = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    /**
     * Edita as informações de um utilizador existente.
     * 
     * @return bool Retorna true se o utilizador for atualizado com sucesso, false caso contrário.
     */
    public function edit(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para atualizar os dados do categoria específico.
        $sql = "UPDATE utilizador SET id_tipo_utilizador = :id_tipo_utilizador, username = :username, email = :email, morada = :morada, telefone = :telefone, codigo_postal = :codigo_postal, nif = :nif
                WHERE id_utilizador = :id_utilizador
                LIMIT 1";

        // Prepara a consulta SQL.
        $editCategoria = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editCategoria->bindParam(':id_tipo_utilizador', $this->formData['id_tipo_utilizador']);
        $editCategoria->bindParam(':username', $this->formData['username']);
        $editCategoria->bindParam(':email', $this->formData['email']);
        $editCategoria->bindParam(':morada', $this->formData['morada']);
        $editCategoria->bindParam(':telefone', $this->formData['telefone']);
        $editCategoria->bindParam(':codigo_postal', $this->formData['codigo_postal']);
        $editCategoria->bindParam(':nif', $this->formData['nif']);
        $editCategoria->bindParam(':id_utilizador', $this->formData['id_utilizador']);

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

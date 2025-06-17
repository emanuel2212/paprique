<?php

/**
 * Classe para listar, visualizar, criar e editar encomendas no banco de dados.
 */

 require_once './bd/Connection.php';

class Encomendas extends Connection
{
    
    /**
     * Conexão com o banco de dados.
     * @var object
     */
    public object $conn;

    /**
     * Dados do formulário para criação e edição de um novo encomenda.
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
    public function setId(int $id_encomenda): void
    {
        // Atribui o ID do encomenda à propriedade id.
        $this->id = $id_encomenda;
    }

    /** 
     * Lista os encomendas cadastrados no banco de dados.
     * 
     * @return array Retorna um array contendo os dados dos encomendas.
     */
    public function list(): array
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados dos encomendas, limitando o resultado a 40 registros.
        $sql = "SELECT * FROM `encomendas` WHERE 1 ORDER BY id_encomenda LIMIT 40";

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

        // Consulta SQL para inserir um novo encomenda.
        $sql = "INSERT INTO encomendas (id_status_encomendas, id_utilizador, valor_total, data_encomenda) 
                VALUES (:id_status_encomendas, :id_utilizador, :valor_total, :data_encomenda)";

        // Prepara a consulta SQL para inserção de dados.
        $AddEncomenda = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddEncomenda->bindParam(':id_status_encomendas', $this->formData['id_status_encomendas']);
        $AddEncomenda->bindParam(':id_utilizador', $this->formData['id_utilizador']);
        $AddEncomenda->bindParam(':valor_total', $this->formData['valor_total']);
        $AddEncomenda->bindParam(':data_encomenda', $this->formData['data_encomenda']);

        // Executa a consulta SQL.
        $AddEncomenda->execute();

        // Verifica se a inserção foi bem-sucedida e retorna o resultado.
        if ($AddEncomenda->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function listAll() {
    $this->conn = $this->connect();
    $sql = "SELECT id_encomenda, id_status_encomendas, id_utilizador, valor_total, data_encomenda FROM encomendas ORDER BY id_encomenda ";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
     
   public function getById($id) {
    $this->conn = $this->connect();
    $sql = "SELECT * FROM encomendas WHERE id_encomenda = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Visualiza os detalhes de um encomenda específico.
     * 
     * Recupera os dados de um encomenda específico baseado no seu ID.
     * 
     * @return array|false Retorna um array contendo os dados do encomenda se encontrado, ou false se não existir.
     */
    public function view(): array|bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados de um encomenda específico.
        $sql = "SELECT * FROM encomendas
                WHERE id_encomenda = :id_encomenda
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_encomenda', $this->id);

        // Executa a consulta SQL.
        $resultUser->execute();

        // Retorna os dados do encomenda ou false se não encontrado.
        return $resultUser->fetch();
    }

    /**
     * Edita as informações de um encomenda existente.
     * 
     * @return bool Retorna true se o encomenda for atualizado com sucesso, false caso contrário.
     */
    public function edit(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para atualizar os dados do encomenda específico.
        $sql = "UPDATE encomendas SET id_status_encomendas = :id_status_encomendas, id_utilizador = :id_utilizador, valor_total = :valor_total, data_encomenda = :data_encomenda
                WHERE id_encomenda = :id_encomenda
                LIMIT 1";

        // Prepara a consulta SQL.
        $editEncomenda = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editEncomenda->bindParam(':id_status_encomendas', $this->formData['id_status_encomendas']);
        $editEncomenda->bindParam(':id_utilizador', $this->formData['id_utilizador']);
        $editEncomenda->bindParam(':valor_total', $this->formData['valor_total']);
        $editEncomenda->bindParam(':data_encomenda', $this->formData['data_encomenda']);
        $editEncomenda->bindParam(':id_encomenda', $this->id);

        // Executa a consulta SQL.
        $editEncomenda->execute();

        // Verifica se a atualização foi bem-sucedida e retorna o resultado.
        if ($editEncomenda->rowCount()) {
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

<?php

/**
 * Classe para listar, visualizar, criar e editar categorias no banco de dados.
 */

 require_once '../bd/Connection.php';

class StatusEncomendas extends Connection
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
    public function setId(int $id_status_encomendas): void
    {
        // Atribui o ID do categoria à propriedade id.
        $this->id = $id_status_encomendas;
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
        $sql = "SELECT * FROM `status_encomendas` WHERE 1 ORDER BY id_status_encomendas LIMIT 40";

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
        $sql = "INSERT INTO status_encomendas (status) VALUES (:status)";

        // Prepara a consulta SQL para inserção de dados.
        $AddStatus = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddStatus->bindParam(':status', $this->formData['status']);

        // Executa a consulta SQL.
        $AddStatus->execute();

        // Verifica se a inserção foi bem-sucedida e retorna o resultado.
        if ($AddStatus->rowCount()) {
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
        $sql = "SELECT id_status_encomendas, status
                FROM status_encomendas
                WHERE id_status_encomendas = :id_status_encomendas
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultUser->bindParam(':id_status_encomendas', $this->id);

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
        $sql = "UPDATE status_encomendas SET status = :status
                WHERE id_status_encomendas = :id_status_encomendas
                LIMIT 1";

        // Prepara a consulta SQL.
        $editStatusEncomenda = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editStatusEncomenda->bindParam(':status', $this->formData['status']);
        $editStatusEncomenda->bindParam(':id_status_encomendas', $this->formData['id_status_encomendas']);


        // Executa a consulta SQL.
        $editStatusEncomenda->execute();

        // Verifica se a atualização foi bem-sucedida e retorna o resultado.
        if ($editStatusEncomenda->rowCount()) {
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
        $sql = "DELETE FROM status_encomendas WHERE id_status_encomendas = :id_status_encomendas LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_status_encomendas', $this->id);
 
        // Executa a consulta SQL.
        return $deleteUser->execute();
    }
}

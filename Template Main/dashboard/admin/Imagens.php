<?php

/**
 * Classe para listar, visualizar, criar e editar imagens no banco de dados.
 */

 require_once '../bd/Connection.php';

class Imagens extends Connection
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
    public function setId(int $id_imagem): void
    {
        // Atribui o ID do imagem à propriedade id.
        $this->id = $id_imagem;
    }

    /** 
     * Lista os produtos cadastrados no banco de dados.
     * 
     * @return array Retorna um array contendo os dados dos produtos.
     */
    public function list(): array
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para selecionar os dados dos produtos, limitando o resultado a 40 registros.
        $sql = "SELECT * FROM `imagens` WHERE 1 ORDER BY id_imagem LIMIT 40";

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
        $sql = "INSERT INTO imagens (id_imagem, titulo, link_imagem, descricao) VALUES (:id_imagem, :titulo, :link_imagem, :descricao)";

        // Prepara a consulta SQL para inserção de dados.
        $AddImagem = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddImagem->bindParam(':id_imagem', $this->formData['id_imagem']);
        $AddImagem->bindParam(':titulo', $this->formData['titulo']);
        $AddImagem->bindParam(':link_imagem', $this->formData['link_imagem']);
        $AddImagem->bindParam(':descricao', $this->formData['descricao']);

        // Executa a consulta SQL.
        $AddImagem->execute();

        // Verifica se a inserção foi bem-sucedida e retorna o resultado.
        if ($AddImagem->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function listAll() {
    $this->conn = $this->connect();
    $sql = "SELECT id_imagem, titulo, link_imagem, descricao FROM imagens ORDER BY titulo";
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
        $sql = "SELECT id_imagem, titulo, link_imagem, descricao
                FROM imagens
                WHERE id_imagem = :id_imagem
                LIMIT 1";

        // Prepara a consulta SQL.
        $resultImagem = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $resultImagem->bindParam(':id_imagem', $this->id);

        // Executa a consulta SQL.
        $resultImagem->execute();

        // Retorna os dados do categoria ou false se não encontrado.
        return $resultImagem->fetch();
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
        $sql = "UPDATE imagens SET titulo = :titulo, link_imagem = :link_imagem, descricao = :descricao
                WHERE id_imagem = :id_imagem
                LIMIT 1";

        // Prepara a consulta SQL.
        $editImagem = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $editImagem->bindParam(':titulo', $this->formData['titulo']);
        $editImagem->bindParam(':link_imagem', $this->formData['link_imagem']);
        $editImagem->bindParam(':descricao', $this->formData['descricao']);
        $editImagem->bindParam(':id_imagem', $this->formData['id_imagem']);

        // Executa a consulta SQL.
        $editImagem->execute();

        // Verifica se a atualização foi bem-sucedida e retorna o resultado.
        if ($editImagem->rowCount()) {
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
        $sql = "DELETE FROM imagens WHERE id_imagem = :id_imagem LIMIT 1";

        // Prepara a consulta SQL.
        $deleteImagem = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteImagem->bindParam(':id_imagem', $this->id);

        // Executa a consulta SQL.
        return $deleteImagem->execute();
    }
}

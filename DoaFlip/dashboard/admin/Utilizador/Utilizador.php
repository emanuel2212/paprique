<?php

/**
 * Classe para listar, visualizar, criar e editar utilizadores no banco de dados.
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/paprique/DoaFlip/dashboard/bd/Connection.php';

class Utilizador extends Connection
{

    /**
     * Conexão com o banco de dados.
     * @var object
     */
    public object $conn;

    /**
     * Dados do formulário para criação e edição de um novo Utilizador.
     * @var array
     */
    public array $formData;

    /**
     * ID do Utilizador para operações específicas (visualização e edição).
     * @var int
     */
    public int $id;

    /**
     * Define os dados do formulário para criação de um novo Utilizador.
     * 
     * @param array $formData Dados do formulário contendo informações do Utilizador.
     * @return void
     */
    public function setFormData(array $formData): void
    {
        // Atribui os dados do formulário à propriedade formData.
        $this->formData = $formData;
    }

    /**
     * Define o ID do Utilizador para operações que necessitam de um identificador específico.
     * 
     * @param int $id Identificador único do Utilizador.
     * @return void
     */
    public function setId(int $id_utilizador): void
    {
        // Atribui o ID do Utilizador à propriedade id.
        $this->id = $id_utilizador;
    }

    /** 
     * Lista os Utilizadors cadastrados no banco de dados.
     * 
     * @return array Retorna um array contendo os dados dos Utilizadors.
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
        $this->conn = $this->connect();
        $sql = "SELECT id_utilizador, id_tipo_utilizador, username, nome_completo, 
                   email, morada, telefone, codigo_postal, nif, foto_perfil, data_criado
            FROM utilizador
            WHERE id_utilizador = :id_utilizador";

        $resultUser = $this->conn->prepare($sql);
        $resultUser->bindParam(':id_utilizador', $this->id);
        $resultUser->execute();

        $userData = $resultUser->fetch();

        // Garante que foto_perfil sempre terá um valor
        if (empty($userData['foto_perfil'])) {
            $userData['foto_perfil'] = 'images/default-profile.png';
        }

        return $userData;
    }

    /**
     * Cria um novo Utilizador no banco de dados.
     * 
     * @return bool Retorna true se o Utilizador for criado com sucesso, false caso contrário.
     */
    public function create(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Hash da senha usando SHA1 
        $hashedPassword = sha1($this->formData['password']);

        // Consulta SQL para inserir um novo utilizador.
        $sql = "INSERT INTO utilizador (id_tipo_utilizador, username, nome_completo, email, password, morada, telefone, codigo_postal, data_criado, nif) VALUES (:id_tipo_utilizador, :username, :nome_completo, :email, :password, :morada, :telefone, :codigo_postal, NOW(), :nif)";

        // Prepara a consulta SQL para inserção de dados.
        $AddUtilizador = $this->conn->prepare($sql);

        // Associa os valores das propriedades ao SQL.
        $AddUtilizador->bindParam(':id_tipo_utilizador', $this->formData['id_tipo_utilizador']);
        $AddUtilizador->bindParam(':username', $this->formData['username']);
        $AddUtilizador->bindParam(':nome_completo', $this->formData['nome_completo']);
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
    public function listAll()
    {
        $this->conn = $this->connect();
        $sql = "SELECT id_utilizador, username FROM utilizador ORDER BY username";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $this->conn = $this->connect();
        $sql = "SELECT * FROM utilizador WHERE id_utilizador = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Adicione estes métodos à classe Utilizador em Utilizador.php

    /**
     * Atualiza a senha do utilizador
     * 
     * @param int $id_utilizador
     * @param string $password
     * @return bool
     */
    public function updatePassword(int $id_utilizador, string $password): bool
    {
        $this->conn = $this->connect();
        $sql = "UPDATE utilizador SET password = :password WHERE id_utilizador = :id_utilizador LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_utilizador', $id_utilizador);
        return $stmt->execute();
    }

    /**
     * Atualiza a foto de perfil do utilizador
     * 
     * @param int $id_utilizador
     * @param string $fotoPath
     * @return bool
     */
    public function updateProfilePicture(int $id_utilizador, string $fotoPath): bool
    {
        $this->conn = $this->connect();
        $sql = "UPDATE utilizador SET foto_perfil = :foto_perfil WHERE id_utilizador = :id_utilizador LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':foto_perfil', $fotoPath);
        $stmt->bindParam(':id_utilizador', $id_utilizador);
        return $stmt->execute();
    }
    /**
     * Edita as informações de um utilizador existente.
     * 
     * @return bool Retorna true se o utilizador for atualizado com sucesso, false caso contrário.
     */
   public function edit(): bool
{
    $this->conn = $this->connect();
    
    // Consulta SQL atualizada para incluir todos os campos
    $sql = "UPDATE utilizador SET 
        username = :username, 
        nome_completo = :nome_completo, 
        email = :email, 
        morada = :morada, 
        telefone = :telefone, 
        codigo_postal = :codigo_postal, 
        nif = :nif
        WHERE id_utilizador = :id_utilizador
        LIMIT 1";

    try {
        $stmt = $this->conn->prepare($sql);
        
        // Campos obrigatórios
        $stmt->bindParam(':username', $this->formData['username']);
        $stmt->bindParam(':nome_completo', $this->formData['nome_completo']);
        $stmt->bindParam(':email', $this->formData['email']);
        $stmt->bindParam(':id_utilizador', $this->formData['id_utilizador']);
        
        // Campos opcionais (usar null se não existirem)
        $morada = $this->formData['morada'] ?? null;
        $telefone = $this->formData['telefone'] ?? null;
        $codigo_postal = $this->formData['codigo_postal'] ?? null;
        $nif = $this->formData['nif'] ?? null;
        
        $stmt->bindParam(':morada', $morada);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        $stmt->bindParam(':nif', $nif);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao atualizar utilizador: " . $e->getMessage());
        return false;
    }
}

    /**
     * Exclui um Utilizador do banco de dados.
     * 
     * @return bool Retorna true se o Utilizador for excluído com sucesso, false caso contrário.
     */
    public function delete(): bool
    {
        // Estabelece a conexão com o banco de dados.
        $this->conn = $this->connect();

        // Consulta SQL para excluir um utilizador específico baseado no seu ID.
        $sql = "DELETE FROM utilizador WHERE id_utilizador = :id_utilizador LIMIT 1";

        // Prepara a consulta SQL.
        $deleteUser = $this->conn->prepare($sql);

        // Associa o valor do ID ao parâmetro na consulta SQL.
        $deleteUser->bindParam(':id_utilizador', $this->id);

        // Executa a consulta SQL.
        return $deleteUser->execute();
    }
}

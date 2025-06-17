<?php

/**
 * Classe abstrata responsável pela conexão com o banco de dados.
 * 
 * 
 */
abstract class Connection
{
    /** @var string $host Endereço do servidor de banco de dados. */
    public string $host = "localhost";

    /** @var string $user Nome de usuário para acessar o banco de dados. */
    public string $user = "root";

    /** @var string $pass Senha para acessar o banco de dados. */
    public string $pass = "";

    /** @var string $dbname Nome do banco de dados. */
    public string $dbname = "doaflip";

    /** @var int $port Porta usada na conexão com o banco de dados. */
    public int $port = 3306;

    /** @var object $connection Objeto que armazenará a conexão com o banco de dados. */
    public object $connection;

    /**
     * Estabelece uma conexão com o banco de dados.
     *
     * @return object Retorna a conexão com o banco de dados.
     */
    public function connect()
    {
        try{
            // Conexão com a porta especificada (descomentada, se necessário).
             $this->connection = new PDO("mysql:host={$this->host};port={$this->port};dbname=".$this->dbname, $this->user, $this->pass);

            // Conexão sem a especificação da porta.
            // $this->connection = new PDO("mysql:host={$this->host};dbname=".$this->dbname, $this->user, $this->pass);

            // Retorna a conexão estabelecida.
            return $this->connection;

        } catch (Exception $e) {
            // Em caso de erro, encerra o script e exibe uma mensagem de erro.
            die('Erro: Por favor tente novamente. Caso o problema persista, entre em contato o administrador adm@empresa.com');
        }
    }
}

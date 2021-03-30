<?php
class Database {

    //region Atributos
    private $host  = "localhost";
    private $dbName = "app150";
    private $username = "app150";
    private $password = "1234";
    private $conn;
    //endregion

    //region Constructor
    public function __construct()
    {
        // Crea el objeto con la conexión hecha
        $this->getConnection();
    }
    //endregion

    //region métodos
    function getConnection()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "LOG > Error de Conexión " . $exception->getMessage();
        }
        echo "LOG > Conexión establecida";
    }
    //endregion



}

<?php
class TestDatabase {

    //region Atributos
    private $host  = "localhost";
    private $dbName = "app150test"; // dbName desarrollo
    private $username = "app150";
    private $password = "1234"; // Contraseña desarrollo
    private $conn;
    //endregion

    //region Constructor
    public function __construct()
    {
        // Crea el objeto con la conexión hecha
        $this->getConnection();
    }
    //endregion

    //region Getters && Setters
    public function getHost(){
        return $this->host;
    }
    
    public function setHost($host) {
        $this->host = $host;
    }

    public function getDbName(){
        return $this->dbName;
    }
    
    public function setDbName($dbName) {
        $this->dbName = $dbName;
    }

    public function getUsername(){
        return $this->username;
    }
    
    public function setusername($username) {
        $this->username = $username;
    }

    public function getPassword(){
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getConn(){
        return $this->conn;
    }
    
    //endregion


    //region métodos
    public function getConnection()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "LOG > Error de Conexión " . $exception->getMessage();
        }
        //echo "LOG > Conexión establecida";
    }
    //endregion



}

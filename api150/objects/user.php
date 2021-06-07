<?php
    /**
     * Clase que contiene todos los parametros del usuario
     * y sus métodos
     */
    class User {
        //region Atributos base de datos de la clase user
        private $conn;
        private $table_name = "user";
        //endregion

        //region Atributos propios de la clase user
        private $idUser;
        private $userName;
        private $password;
        private $mail;
        private $idRol;
        //endregion

        //region Constructor
        public function __construct($db) {
            $this->conn = $db;
        }
        //endregion
    } // Salida de la clase
?>
<?php
    include_once '../config/database.php';
    include_once '../objects/user.php';
    include_once '../objects/session.php';
class CommonFunctions {

    // Atributos
    // Constructor 
    public function __construct() {
    } 
    // Getters && Setters 
    // Metodos 

    /**
     * Recibe un token y comprueba si existe.
     * Tras esto, recoge el rol del usuario asociado a ese token
     * Si existe devolverá true y dejará realizar operaciones 
     * Si no existe devolverá false
     * @param $token
     * @return integer -1 si el token no es valido, 
     *                  0 si el token es valido pero el usuario no es admin, 
     *                  1 si el token es valido y usuario es admin 
     */
    function comprobarTokenAdmin ($token) {
        $database = new Database();
        $idObtenido = -1;
        $consulta = "SELECT idUser FROM session WHERE token LIKE '".$token."';";
        // echo "\n\nConsulta del token > ".$consulta."\n\n";
        $resultado = $database->getConn()->query($consulta);
        //Esto debe devolver un ID de usuario, si es correcto, se crea la sesion 
        if ($resultado->rowCount() == 0) {
            // El token del usuario que esta creando el nuevo usuario no existe 
            return -1;
        } else {
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $idObtenido = $row["idUser"];
            }
            // Se comprueba el rol del user
            $query = "SELECT idRol FROM user WHERE idUser LIKE '".$idObtenido."';";
            $resultado = $database->getConn()->query($query);
            $rolObtenido = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $rolObtenido = $row["idRol"];
            }
            if ($this->comprobarRolAdmin($rolObtenido)) {
                return 1;
            } else {
                return 0;
            }

        }  
    }

    /**
     * Recibe el numero de rol de un usuario y devuelve true en caso de que sea un admin.
     * en caso contrario, devuelve false
     * @param Integer 
     * @return Boolean
     */
    function comprobarRolAdmin($rol) {
        // El rol del administrador va a ser el 1
        if ($rol == 1) {
            return true;
        } else {
            return false;
        }
    }
}

?>
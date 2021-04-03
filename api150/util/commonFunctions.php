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
     * Si existe devolverá true y dejará realizar operaciones 
     * Si no existe devolverá false
     * @param $token
     * @return boolean
     */
    function comprobarToken ($token) {
        $database = new Database();
        $consulta = "SELECT idSession FROM session WHERE token LIKE '".$token."';";
        // echo "\n\nConsulta del token > ".$consulta."\n\n";
        $resultado = $database->getConn()->query($consulta);
        //Esto debe devolver un ID de usuario, si es correcto, se crea la sesion 
        
        if ($resultado->rowCount() == 0) {
            // El token del usuario que esta creando el nuevo usuario no existe 
            return false;
        } else {
            return true;
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
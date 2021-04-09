<?php
    include_once '../../config/database.php';
    include_once '../../objects/user.php';
    include_once '../../objects/session.php';
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


    /**
     * TODO
     * Recibe el token del usuario y comprueba si la sesión está expirada 
     * @param String token
     * @return boolean
     */
    public function comprobarExpireDate ($token) {
        $tiempoActual = time();
        // Recogemos el expireDate asociado al token que recibimos 
        $query = "SELECT expireDate FROM session WHERE token LIKE '".$token."';";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        $tiempoSession = "";
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $tiempoSession = $row["expireDate"];
        }

        // Convertimos el date a timestamp
        $tiempoSessionTimestamp = strtotime($tiempoSession);


        if ($tiempoSessionTimestamp > $tiempoActual) {
            // Aqui el tiempo del expire es un tiempo más grande que el tiempo actual,
            // por lo tanto token valido
            return true;
        } else {
            return false;
        }


    }

    /**
     * TODO
     * Funcion que se llama cada vez que el usuario realiza una acción
     * Otorga 2 minutos más de login desde el momento en el que se realiza la acción
     * @param String Token
     * @return Void
     */
    public function ActualizarExpireDate($tokenUsuario) {
        $tiempoActual = time();
        $tiempoActual = $tiempoActual +120;
        $expireDate = gmdate("Y-m-d H:i:s", $tiempoActual);
        // Recogemos el expireDate asociado al token que recibimos 
        $query = "UPDATE session SET expireDate = UNIX_TIMESTAMP(".$expireDate.") WHERE token LIKE '".$tokenUsuario."';";
        $database = new Database();
        $database->getConn()->query($query);
    }


}

?>
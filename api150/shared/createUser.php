<?php

    //region imports
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    //endregion

    // Conexión con la base de datos 
    include_once '../config/database.php';
    include_once '../objects/user.php';
    include_once '../objects/session.php';

    $database = new Database();

    // recogemos los datos pasados 
    $data = json_decode(file_get_contents("php://input"));

    // Aqui tengo que recibir usuario, contraseña, mail, el rol (opcional), y el token de quien crea el user
    // La logica es que si el token no coincide, no se puede crear un usuario

    $userNameRecibido = htmlspecialchars($_GET["username"]);
    $passwordRecibida = htmlspecialchars($_GET["password"]);
    $mailRecibido = htmlspecialchars($_GET["mail"]);
    $rolRecibido = htmlspecialchars($_GET["rol"]);
    $tokenRecibido = htmlspecialchars($_GET["token"]);

    // Comprobamos que todo esta en orden 
    // echo "Datos recibidos > ".$userNameRecibido." > ".$passwordRecibida." > ".$mailRecibido." > ".$rolRecibido." > ".$tokenRecibido;

    // Comprobamos que no faltan datos esenciales 
    if (!empty($userNameRecibido) && !empty($passwordRecibida) && !empty($mailRecibido) && !empty($tokenRecibido)) {

        // Comprobamos si el rol está asignado 
        $rolRecibido = comprobarRol($rolRecibido);
        // Comprobamos si el token es valido 
        $tokenValido = comprobarToken($tokenRecibido);

        if ($tokenValido) {
            // Se le permite crear el usuario 
            $idUser = crearUsuario($userNameRecibido, $passwordRecibida, $mailRecibido, $rolRecibido);
            echo json_encode("Se ha creado el nuevo usuario ");

        } else {
            echo json_encode("El token de usuario que está utilizando no es válido");
        }
        
    } else {
        echo json_encode("Faltan datos necesarios para la creación del usuario");
    }


    /**
     * Recibe todos los parametros para crear al usuario
     * @param username, password, mail, rol
     * @return Void
     */
    function crearUsuario ($username, $password, $mail, $rol) {
        $database = new Database();
        // Lanzamos la consulta para crear el user
        $query = "INSERT INTO user (idUser, userName, password, mail, idRol) VALUES (null, '".$username."', '".$password."', '".$mail."', ".$rol.");";
        // echo "\nConsulta para insertar > ".$query."\n";
        $stmt = $database->getConn()->prepare($query);
        // ejecutamos la inserción
        $stmt->execute();

    } 

    /**
     * Recibe el rol que se recoge por CGI
     * Comprueba si está asignado, si no lo está, le asigna un rol 
     * @param $rol
     * @return integer 
     */
    function comprobarRol ($rol) {
        // Si todo esto tiene valores, se comprueba que el rol está especificado
        if (empty($rol)) {
            // No hay rol asignado, por defecto se le da 1
            $rol = 1;
            return $rol;
        } else {
            // El rol ya está asignado
            return $rol; 
        }
    }

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


?>
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

    /**
     * Comprobación de que la conexión es valida
     */

    // echo "\nEsto es lo que posee el objeto db > ".$database->getHost()."/".$database->getDbName()."/".$database->getUsername()."/".$database->getPassword();

    // recoger los datos que se pasan por post 
    $data = json_decode(file_get_contents("php://input"));

    // Pruebas
    $nombreRecibidoPorGet = htmlspecialchars($_GET["username"]);
    $passwordRecibidaPorGet = htmlspecialchars($_GET["password"]);
    //echo "\nRecibo por get > ".$nombreRecibidoPorGet;
    //echo "\nRecibo por get > ".$passwordRecibidaPorGet;

    // Mirar a ver si hay datos 
    if (!empty($nombreRecibidoPorGet) && !empty($passwordRecibidaPorGet)) {

        // Consultamos a ver si el usuario existe 
        $query = "SELECT idUser FROM user WHERE username LIKE '".$nombreRecibidoPorGet."' AND password LIKE '".$passwordRecibidaPorGet."';";
       // echo "\nLOG > Class LOGIN > esta es la consulta que estoy enviando al SQL para loguear --> ".$query;
        // declarar la query
        $stmt = $database->getConn()->prepare($query);
        //Esto debe devolver un ID de usuario, si es correcto, se crea la sesion 
        $idObtenido = $stmt->execute();

        if ($idObtenido != null) {
            // Usuario existe
            // echo "\nLOG > Class LOGIN > Usuario Logado";
            // Aqui ahora se genera el token y se crea la sesion 
            $tokenGenerado = generateToken();
            crearSesion($tokenGenerado, $idObtenido, $database);
            // echo "\nLOG > Class LOGIN > Si estas viendo esto, la session se creó correctamente";
            echo json_encode(array("token : ".$tokenGenerado)); 
            return true;
        } else {
            // Usuario no existe
            // echo "\nLOG > Class LOGIN > Los Datos no coinciden con ningun usuario";
            return false;
        }

        
    } else {
        // si faltan datos, se comunica 
        http_response_code(400);
        echo json_encode(array("\nLOG"=> "Introduce un usuario y contraseña válidos"));
        
    }

    /**
     * Función que genera un token para la sesion
     * @renturn String
     */
    function generateToken() {
        $token = "SampleToken";
        return $token;
    }

    /**
     * Función que genera una sesion con los datos disponibles 
     */
    function crearSesion($tokenGenerado, $idObtenido, $database) {
        $expireDateGenerated = generateExpireDate();

        // Lanzamos la consulta para crear la sesion
        $query = "INSERT INTO session (idSession, idUser, token, expireDate) VALUES (null, ".$idObtenido.", '".$tokenGenerado."', '".$expireDateGenerated."');";
        // echo "LOG > Class LOGIN > esta es la consulta que estoy enviando al SQL para loguear --> ".$query;
        // declarar la query
        $stmt = $database->getConn()->prepare($query);
        // ejecutamos la inserción
        $stmt->execute();

        /*
        // Pruebas
        echo "\n\n He llegado hasta aqui en la consulta de insertar la session"; 
        $queryConfirmar = "SELECT * FROM session WHERE token LIKE 'sampletoken'";
        echo "\n".$queryConfirmar;
        $stmt = $database->getConn()->prepare($queryConfirmar);
        echo "\n\nDEVUELTO > ".$stmt->execute()."\n";
        */


    }

    function generateExpireDate() {
        // Se usa el expire date de prueba
        $expireDate = date("Y-m-d");
        /*
        $expireDate=mktime(11, 14, 54, 8, 12, 2021);
         */
        $expireDate = $expireDate." 23:59:59";
        // echo "\n\nCreated date is " .$expireDate."\n";

        return $expireDate; 
    }


?>

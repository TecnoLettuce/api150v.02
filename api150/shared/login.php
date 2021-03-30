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
    $db = $database->getConnection();

    // recoger los datos que se pasan por post 
    $data = json_decode(file_get_contents("php://input"));

    // Mirar a ver si hay datos 
    if (!empty($data->userName) && !empty($data->password)) {
        // Si todo tiene valores, se asignan a las variables 
        $usuarioPasado = $data->userName;
        $contraseñaPasada = $data->password;

        // Consultamos a ver si el usuario existe 
        $query = "SELECT idUser FROM user WHERE username LIKE ".$usuarioPasado." AND password LIKE ".$contraseñaPasada.";";
        echo "LOG > Class LOGIN > esta es la consulta que estoy enviando al SQL para loguear --> ".$query;
        // declarar la query
        $stmt = $this->conn->prepare($query);
        //Esto debe devolver un ID de usuario, si es correcto, se crea la sesion 
        $idObtenido = $stmt->execute();

        if ($idObtenido != null) {
            // Usuario existe
            echo "LOG > Class LOGIN > Usuario Logado";
            // Aqui ahora se genera el token y se crea la sesion 
            $tokenGenerado = generateToken();
            crearSesion($tokenGenerado, $idObtenido);
            echo 'LOG > Class LOGIN > Si estas viendo esto, la session se creó correctamente';
            return true;
        } else {
            // Usuario no existe
            echo "LOG > Class LOGIN > Los Datos no coinciden con ningun usuario";
            return false;
        }

        
    } else {
        // si faltan datos, se comunica 
        http_response_code(400);
        echo json_encode(array("LOG"=> "Introduce un usuario y contraseña válidos"));
        
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
    function crearSesion($tokenGenerado, $idObtenido) {
        $expireDateGenerated = generateExpireDate();

        // Lanzamos la consulta para crear la sesion
        $query = "INSERT INTO session (idSession, idUser, token, expireDate) VALUES (null, ".$idObtenido.", ".$tokenGenerado.", ".$expireDateGenerated.");";
        echo "LOG > Class LOGIN > esta es la consulta que estoy enviando al SQL para loguear --> ".$query;
        // declarar la query
        $db = new Database();
        $db->getConnection();
        $stmt = $db->conn->prepare($query);
        // ejecutamos la inserción
        $stmt->execute();
    }

    function generateExpireDate() {
        // Se usa el expire date de prueba
        $expireDate=mktime(11, 14, 54, 8, 12, 2021);
        echo "Created date is " .$expireDate;
        return $expireDate; 
    }


?>

<?php 

    //region imports
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    //endregion

    // Conexión con la base de datos 
    include_once '../../config/database.php';
    include_once '../../objects/user.php';
    include_once '../../objects/session.php';


    $database = new Database();

    // recoger los datos que se pasan por post 
    $data = json_decode(file_get_contents("php://input"));


    // Recogida de datos 
    $tokenRecibido = htmlspecialchars($_GET["token"]);

    // Comprobar que el token no esta vacío
    if (!empty($tokenRecibido)) {
        // Comprobamos que corresponde con un usuario 
        $query = "SELECT idSession FROM session WHERE token LIKE '".$tokenRecibido."';";
        // echo "DEBUG > la consulta para consultar la sesion es ".$query;
        
        $resultado = $database->getConn()->query($query);
        // Comprobar que se devuelve algo 
        if ($resultado->rowCount() != 0) {
            //Declaramos la variable id
            $idSesionObtenido;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $idSesionObtenido = $row["idSession"];
            }

            // comprobación debug 
            // echo json_encode(array("idSession obtenido : ".$idSesionObtenido));
            if (!empty($idSesionObtenido)) {
                // Tenemos id de sesion
                // UPDATE table_name SET field1 = new-value1, field2 = new-value2 [WHERE Clause]
                // Recoger tiempo actual
                $time = time();
                // echo "La expireDate generada es ".$time;
                $queryExpireSession = "UPDATE session SET expireDate = UNIX_TIMESTAMP(".$time.") WHERE idSession LIKE ".$idSesionObtenido.";";
                // echo "\nquery de actualización de la sesion > ".$queryExpireSession;
                // TODO comprobar que funciona
                echo json_encode(array("error : 0")); // TodoOk
            } else {
                // La sesion está vacía
                echo json_encode(array("error : 1"));
            }


        } else {
            // no existe sesion asociada al token 
            echo json_encode(array("error : 2"));
        }
    }





?>
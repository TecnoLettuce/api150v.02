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
    include_once '../../util/commonFunctions.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();

    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $tituloRecibido = htmlspecialchars($_GET["titulo"]);
    $letraRecibida = htmlspecialchars($_GET["letra"]);
    //endregion

    $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        // comprobación de que los datos se reciben correctamente
        if (!empty($letraRecibida) && !empty($tituloRecibido)) {
            // tengo todos los datos que necesito
            //Comprobamos que el registro no existe ya en la base de datos 
            if ($cf->comprobarExisteHimnoPorTitulo($tituloRecibido)) {
                // El ambiente ya existe
                echo json_encode(array("error : 1, message : El himno ya existe" ));
            } else {
                // el ambiente no existe 
                $query = "INSERT INTO himnos (id_Himno, titulo, letra) VALUES (null,'".$tituloRecibido."', '".$letraRecibida."');";
                // echo "La consulta para insertar un ambiente es ".$query;
                $stmt = $database->getConn()->prepare($query);
                    
                $stmt->execute();

                echo json_encode(array("error : 0, message : Elemento creado"));
            }

        } else {
            echo json_encode(" error : 1, message : Faltan uno o más datos");
        }


    } elseif ($cf->comprobarTokenAdmin($token) == 0) {
        echo json_encode("error : 2, message : no tiene permisos para realizar esta operación");
    } else {
        echo json_encode("error : 3, message : token no valido");
    }

    

    



?>
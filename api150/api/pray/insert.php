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

    $titulo = htmlspecialchars($_GET["titulo"]);
    $texto = htmlspecialchars($_GET["texto"]);
    //endregion

    $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        // comprobamos que no faltan datos vitales
        if (!empty($titulo) && !empty($texto) ) {
            // Tenemos todos los datos
            //Comprobamos que el registro no existe ya en la base de datos 
            if ($cf->comprobarExisteOracionPorTitulo($titulo)) {
                // la oración ya existe
                echo json_encode(array("error : 1, message : La oración ya existe" ));
            } else {
                // la oración no existe 
                $query = "INSERT INTO oraciones (id_Oracion, titulo, texto) VALUES (null,'".$titulo."','".$texto."');";
                // echo "La consulta para insertar una oración es ".$query;
                $stmt = $database->getConn()->prepare($query);
                // echo "La consulta para insertar la oración es ".$query;
                
                $stmt->execute();
                echo json_encode(array("error : 0, message : Elemento creado"));
            }
        } else {
            echo json_encode("error : 1, message : Faltan uno o más datos");
        }

    } elseif ($cf->comprobarTokenAdmin($token) == 0) {
        echo json_encode("error : 2, message : no tiene permisos para realizar esta operación");
    } else {
        echo json_encode("error : 3, message : token no valido");
    }

    

    


?>
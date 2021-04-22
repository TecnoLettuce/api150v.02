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


    /**
     * Aquí va la lógica de subir un archivo por url 
     */

    $url = htmlspecialchars($_GET["url"]);
    $tipo = htmlspecialchars($_GET["tipo"]);
    $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        // comprobamos que no faltan datos vitales
        if (!empty($url) && !empty($tipo)) {
            // Tenemos todos los datos 
            // Comprobar si existe el medio 
            if ($cf->comprobarExisteMedioPorURL($url)) {
                // Ya existe 
                echo json_encode("error : 4, message : El elemento que intenta insertar ya existe");
            } else {
                // No existe 
                // lo insertamos 
                // el programa no existe 
                $query = "INSERT INTO medios (id_Medio, url, id_Tipo) VALUES (null,'".$url."',".$tipo.");";
                // echo $query;
                // echo "La consulta para insertar un programa es ".$query;
                $stmt = $database->getConn()->prepare($query);
                // echo "La consulta para insertar el programa es ".$query;
                
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
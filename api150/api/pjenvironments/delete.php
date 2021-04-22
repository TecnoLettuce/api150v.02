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

    $idRecibida = htmlspecialchars($_GET["idAmbiente"]);
    
    $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        if (!empty($idRecibida)) {
            // Tengo todos los datos
            // Comprobamos que la id corresponde a un registro 
            if ($cf->comprobarExisteAmbientePorId($idRecibida)) {
                // Efectivamente existe y se puede eliminar
                $query = "DELETE FROM ambiente WHERE id_Ambiente like ".$idRecibida.";";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
                echo json_encode(array(" error : 0, message : Elemento eliminado"));
            } else {
                //No existe y por lo tanto no se puede eliminar 
                echo json_encode(array("error : 1, message : El registro no existe"));
            }
        } else {
            echo json_encode(" error : 1, message : faltan uno o más datos");
        }


    } elseif ($cf->comprobarTokenAdmin($token) == 0) {
        echo json_encode("error : 2, message : no tiene permisos para realizar esta operación");
    } else {
        echo json_encode("error : 3, message : token no valido");
    }
    
    
    


?>
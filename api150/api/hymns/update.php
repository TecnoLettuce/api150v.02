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

$idHimno = htmlspecialchars($_GET["idHimno"]);
$nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);
$nuevaLetra = htmlspecialchars($_GET["nuevaLetra"]);
$boolEnUso = htmlspecialchars($_GET["enUso"]);


//endregion


$token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        // lo primero es comprobar que existe el elemento que se quiere modificar 
        if (!empty($idHimno) && !empty($nuevoTitulo) && !empty($nuevaLetra) && $boolEnUso != null) {
            // Tenemos todos los datos ok
            // Comprobamos que el id existe
            if ($cf->comprobarExisteHimnoPorId($idHimno)) {
        
                $database = new Database();
                $query = "UPDATE himnos SET titulo = '".$nuevoTitulo."', letra= '".$nuevaLetra."', enUso = ".$boolEnUso." WHERE id_Himno LIKE ".$idHimno.";";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
                echo json_encode(" status : 200, message : Elemento actualizado");
            } else {
                echo json_encode(" status : 406, message : El registro no existe");
            }
        } else {
            echo json_encode(" status : 400, message : Faltan uno o más datos");
        }
    } elseif ($cf->comprobarTokenAdmin($token) == 0) {
        echo json_encode("status : 401, message : no tiene permisos para realizar esta operación");
    } else {
        echo json_encode("status : 403, message : token no valido");
    }



?>
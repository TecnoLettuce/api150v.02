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

$idVisita = htmlspecialchars($_GET["idVisita"]);
$nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);

//endregion

// lo primero es comprobar que existe el elemento que se quiere modificar 
if (!empty($idVisita) && !empty($nuevoTitulo) ) {
   // Tenemos todos los datos ok
   // Comprobamos que el id existe
   if ($cf->comprobarExisteVisitaPorId($idVisita)) {

       $database = new Database();
       $query = "UPDATE visitas SET titulo = '".$nuevoTitulo."' WHERE id_Visita LIKE ".$idVisita.";";
       $stmt = $database->getConn()->prepare($query);
       $stmt->execute();
       echo json_encode(" error : 0, message : Elemento actualizado");
   } else {
       echo json_encode(" error : 2, message : El registro no existe");
   }
} else {
    echo json_encode(" error : 1, message : Faltan uno o más datos");
}

?>
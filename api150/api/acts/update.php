<?php 

     //region imports
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Access-Control-Allow-Headers, Authorization, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
header("Access-Control-Max-Age: 3600");
//endregion

/* cosa de la que no me fio */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Indica los métodos permitidos.
    header('Access-Control-Allow-Methods: GET, POST, DELETE');
    // Indica los encabezados permitidos.
    header('Access-Control-Allow-Headers: Authorization');
    http_response_code(204);
}
     //endregion
 
     // Conexión con la base de datos 
     include_once '../../config/database.php';
     include_once '../../util/commonFunctions.php';
     include_once '../../objects/DAO.php';
     include_once '../../util/logger.php';
     //Creación de la base de datos 
     $database = new Database();
     // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $dao = new Dao();
    $logger = new Logger();

     //region Definicion de los datos que llegan
     $data = json_decode(file_get_contents("php://input"));
 
     $idPrograma = htmlspecialchars($_GET["idPrograma"]);
     $nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);
     $nuevaDescripcion = htmlspecialchars($_GET["nuevaDescripcion"]);
     $nuevaUbicacion = htmlspecialchars($_GET["nuevaUbicacion"]);
     $nuevaFecha = htmlspecialchars($_GET["nuevaFecha"]);
     $boolEnUso = htmlspecialchars($_GET["enUso"]);

     $arrayMedios = array();
	$arrayMedios = $data->medios;

	$mediosAInsertar = array();
	$tiposAInsertar = array();

    for ($i=0; $i < count($arrayMedios, COUNT_NORMAL); $i++) { 
    array_push($mediosAInsertar, $arrayMedios[$i]->url);
    array_push($tiposAInsertar, $arrayMedios[$i]->tipo);
}
      
     $token = htmlspecialchars($_GET["token"]);
    //endregion
    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) >= 0) { 

        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            // lo primero es comprobar que existe el elemento que se quiere modificar 
            if (!empty($idPrograma)
             && !empty($nuevoTitulo)
             && !empty($nuevaDescripcion)
             && !empty($nuevaUbicacion)
              && !empty($nuevaFecha)
               && $boolEnUso!=null
           		&& !empty($mediosAInsertar) 
                && !empty($tiposAInsertar) 
                && ( count($tiposAInsertar, COUNT_NORMAL) == count($mediosAInsertar, COUNT_NORMAL))) {
                // Tenemos todos los datos ok
                // Comprobamos que el id existe
                if ($cf->comprobarExisteActoPorId($idPrograma)) {
                    // efectivamente existe 
                    
                	$dao->actualizarActo($nuevoTitulo, $nuevaDescripcion, $nuevaUbicacion, $nuevaFecha, $boolEnUso, $idPrograma, $mediosAInsertar, $tiposAInsertar);
                    http_response_code(200);
                    echo $logger->updated_element();
                    
                } else {
                    http_response_code(406);
                    echo $logger->not_exists("acto");
                }

            } else {
                http_response_code(400);
                echo $logger->incomplete_data();
            }

        } else {
            http_response_code(401);
            echo $logger->expired_session();
        }

    } else {
        http_response_code(403);
        echo $logger->invalid_token();
    }

     
     

     

?>
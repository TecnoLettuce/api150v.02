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
     
 
     // Conexión con la base de datos 
     include_once '../../config/database.php';
     include_once '../../util/commonFunctions.php';
     include_once '../../objects/DAO.php';
     include_once '../../util/logger.php';
     //Creación de la base de datos 
     $database = new Database();
     // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $logger = new Logger();
    $dao = new Dao();
     //region Definicion de los datos que llegan
     $data = json_decode(file_get_contents("php://input"));
 
     $idSaludo = htmlspecialchars($_GET["idSaludo"]);
     $nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);
     $nuevaDescripcion = htmlspecialchars($_GET["nuevaDescripcion"]);
     $nuevoTexto = htmlspecialchars($_GET["nuevoTexto"]);
     $boolEnuso = htmlspecialchars($_GET["enUso"]);

     $arrayMedios = array();
    $arrayMedios = $data->medios;

    $mediosAInsertar = array();
    $tiposAInsertar = array();



    for ($i=0; $i < count($arrayMedios, COUNT_NORMAL); $i++) { 
    array_push($mediosAInsertar, $arrayMedios[$i]->url);
    array_push($tiposAInsertar, $arrayMedios[$i]->tipo);
    }

     
     //endregion
     $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) >= 0) { 

        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            if (!empty($idSaludo) 
                && !empty($nuevoTitulo) 
                && !empty($nuevaDescripcion) 
                && !empty($nuevoTexto)
                && $boolEnUso != null 
                && !empty($mediosAInsertar) 
                && !empty($tiposAInsertar) 
                && ( count($tiposAInsertar, COUNT_NORMAL) == count($mediosAInsertar, COUNT_NORMAL))) {
                // Tenemos todos los datos ok
                // Comprobamos que el id existe
                if ($cf->comprobarExisteSaludoPorId($idSaludo)) {
                    // efectivamente existe 

                    $dao->actualizarSaludo($nuevoTitulo, $nuevaDescripcion, $nuevoTexto, $boolEnuso, $idSaludo, $mediosAInsertar, $tiposAInsertar);
                    http_response_code(200);
                    echo $logger->updated_element();
                } else {
                    http_response_code(406);
                    echo $logger->not_exists("saludo");
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
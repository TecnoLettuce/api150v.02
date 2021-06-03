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
$logger = new Logger();
$dao = new Dao();
include_once '../../config/rolConfig.php';

$rolConfig = new RolConfig();
$permissionLevel = [$rolConfig->adminRol, $rolConfig->editorRol]; // Ambos


//region Definicion de los datos que llegan
$data = json_decode(file_get_contents("php://input"));

$idAmbiente = htmlspecialchars($_GET["id"]);
$nuevoTitulo = htmlspecialchars($_GET["titulo"]);
$descripcion = htmlspecialchars($_GET["descripcion"]);
$ubicacion = htmlspecialchars($_GET["ubicacion"]);
$fecha = htmlspecialchars($_GET["fecha"]);
$boolEnUso = htmlspecialchars($_GET["enUso"]);

$arrayMedios = array();
$arrayMedios = $data->medios;

//endregion

$token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) >= 0) { 

        if ($cf->checkPermission($token, $permissionLevel) == 1) {
            // La sesión es válida
            // lo primero es comprobar que existe el elemento que se quiere modificar 
            if (!empty($idAmbiente) 
            	&& !empty($nuevoTitulo)
            	&& !empty($descripcion)
            	&& !empty($ubicacion)
            	&& !empty($fecha)
            	&& $boolEnUso != null 
                && !empty($arrayMedios)) {
                // Tenemos todos los datos ok
                // Comprobamos que el id existe
                if ($cf->comprobarExisteAmbientePorId($idAmbiente)) {
            
                    $dao->actualizarAmbiente($nuevoTitulo, $descripcion, $ubicacion, $fecha, $boolEnUso, $idAmbiente, $arrayMedios);
                    http_response_code(200);
                    echo $logger->updated_element();
                } else {
                    http_response_code(406);
                    echo $logger->not_exists("ambiente");
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
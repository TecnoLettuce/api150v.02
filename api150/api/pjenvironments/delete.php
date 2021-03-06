<?php 

    //region imports
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Access-Control-Allow-Headers, Authorization, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
    header("Access-Control-Max-Age: 3600");
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

    $idRecibida = $data->idAmbiente;
    $token = $data->token;

    // Comprobamos que tiene permisos de administrador
    if ($cf->checkPermission($token, $permissionLevel) == 1) { 
        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            if (!empty($idRecibida)) {
                // Tengo todos los datos
                // Comprobamos que la id corresponde a un registro 
                if ($cf->comprobarExisteAmbientePorId($idRecibida)) {
                    // Efectivamente existe y se puede eliminar
                    $dao->borrarAmbiente($idRecibida);
                    http_response_code(200);
                    echo $logger->deleted_element();
                } else {
                    //No existe y por lo tanto no se puede eliminar
                    http_response_code(406); 
                    echo $logger->not_exists("ambiente");
                }
            } else {
                http_response_code(400);
                echo $logger->incomplete_data();
            }

        } else {
            http_response_code(400);
            echo $logger->incomplete_data();
        }

    } else {
        http_response_code(403);
        echo $logger->invalid_token();
    }
    
?>
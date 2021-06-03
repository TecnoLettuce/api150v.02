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


    /**
     * Aquí va la lógica de subir un archivo por url 
     */
    $nombres = array(); // Declaramos el array
    $nombres = $data->nombre; // Este es el campo nombre añadido recientemente
    $url = array(); // Declaramos el array
    $url = $data->url; // Esto puede ser un array
    $tipo = array(); // Declaramos el array
    $tipo = $data->tipo; 
    $token = $data->token;
    $ucf = new UploadCommonFunctions();
    $cf = new CommonFunctions();

    if ($cf->checkPermission($token, $permissionLevel) == 1) {


        $result = array();
        $result = $ucf->insertarMedios($nombres,$url, $tipo); // TODO ESTO NO FUNCIONA LA FUNCION AHORA RECIBE UN ARRAY DE MEDIOS

        // Comprobar si ha devuelto fallo o acierto 
        if (count($result, COUNT_NORMAL) > 0) {
            // Es un array y tenemos resultados 
            echo json_encode($result);
        } else {
            // Es un código de error
            echo $result;
        }
    } else {
        http_response_code(403);
        $logger->not_permission();
    }

    
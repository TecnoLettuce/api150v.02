<?php 
        //region imports
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header('Access-Control-Allow-Origin: *');
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
        include_once '../../util/ambiente.php';
        include_once '../../objects/DAO.php';
        include_once '../../util/logger.php';
        $logger = new Logger();
        $dao = new Dao();
        
        //Creación de la base de datos 
        $database = new Database();
        // Declaración de commonFunctions
        $cf = new CommonFunctions();
        
        // No tiene que recibir parámetros es solo la consulta pelada
        echo json_encode($dao->listarCadaAmbiente());


?>
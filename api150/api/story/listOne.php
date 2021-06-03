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
    include_once '../../util/historia.php';
    include_once '../../util/logger.php';
    include_once '../../objects/DAO.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $dao = new Dao();

    if (isset($_GET["idHistoria"]) && isset($_GET["titulo"]) ) {

        // Recibe el titulo o el ID de un historia y lo busca en la base de datos 
        $id = htmlspecialchars($_GET["idHistoria"]);
        $titulo = htmlspecialchars($_GET["titulo"]);
        echo  json_encode($dao->listarUnaHistoriaPorIdyTitulo($id, $titulo));

    } else if (isset($_GET["idHistoria"])) {

        $id = htmlspecialchars($_GET["idHistoria"]);
        echo  json_encode($dao->listarUnaHistoriaPorId($id));
        
    } else if (isset($_GET["titulo"])) {

        $titulo = htmlspecialchars($_GET["titulo"]);
        echo  json_encode($dao->listarUnaHistoriaPorTitulo($titulo));
        
    } else {

        $log = new Logger();
        http_response_code(406);
        $log->incomplete_data();
        
    }

?>
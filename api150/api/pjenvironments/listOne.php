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
    include_once '../../util/ambiente.php';
    include_once '../../objects/DAO.php';
    include_once '../../util/logger.php';
    $logger = new Logger();
    $dao = new Dao();

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $logger = new Logger();
    $dao = new Dao();

    if (isset($_GET["idAmbiente"]) && isset($_GET["titulo"]) ) {

        // Recibe el titulo o el ID de un historia y lo busca en la base de datos 
        $id = htmlspecialchars($_GET["idAmbiente"]);
        $titulo = htmlspecialchars($_GET["titulo"]);
        echo  json_encode($dao->ListarUnAmbientePorIdyTitulo($id,$titulo));

    } else if (isset($_GET["idAmbiente"])) {

        $id = htmlspecialchars($_GET["idAmbiente"]);
        echo json_encode($dao->ListarUnAmbientePorId($id));
        
    } else if (isset($_GET["titulo"])) {

        $titulo = htmlspecialchars($_GET["titulo"]);
        echo json_encode($dao->ListarUnAmbientePorTitulo($titulo));
        
    } else {

        $log = new Logger();
        http_response_code(406);
        $log->incomplete_data();
        
    }

   

?>
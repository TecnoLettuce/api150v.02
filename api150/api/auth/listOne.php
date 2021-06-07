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
    include_once '../../util/user.php';
    include_once '../../objects/DAO.php';
    include_once '../../util/logger.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $logger = new Logger();
    $dao = new Dao();


    if (isset($_GET["idUser"]) && isset($_GET["userName"])) {
        $id = htmlspecialchars($_GET["idUser"]);
        $userName = htmlspecialchars($_GET["userName"]);

        echo json_encode($dao->listarUsuarioPorIdYNombre($id, $userName));

    } else if (isset($_GET["idUser"])) {
        $id = htmlspecialchars($_GET["idUser"]);
        echo  json_encode($dao->listarUsuarioPorId($id));
    } else if (isset($_GET["userName"])) {
        $userName = htmlspecialchars($_GET["userName"]);
        echo json_encode($dao->listarUsuarioPorNombre($userName));
    } else {
        http_response_code(400);
        $logger->incomplete_data();
    }
   
?>
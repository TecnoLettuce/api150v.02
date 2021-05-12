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
    include_once '../../util/material.php';
    include_once '../../objects/DAO.php';
    include_once '../../util/logger.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $logger = new Logger();
    $dao = new Dao();


    if (isset($_GET["idMedio"]) && isset($_GET["URL"])) {
        // Recibe el fecha o el ID de una frase y lo busca en la base de datos 
        $id = htmlspecialchars($_GET["idMedio"]);
        $url = htmlspecialchars($_GET["URL"]);
        echo $dao->listarMedioPorIdyURL($id,$url);

    } else if (isset($_GET["idMedio"])) {
        $id = htmlspecialchars($_GET["idMedio"]);
        echo $dao->listarMedioPorId($id);
    } else if (isset($_GET["URL"])) {
        $url = htmlspecialchars($_GET["URL"]);
        echo $dao->listarMedioPorURL($url);
    } else {
        http_response_code(406);
        $logger->incomplete_data();
    }

?>
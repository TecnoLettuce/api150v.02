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
    include_once '../../util/act.php';
    
    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $dao = new Dao();

    $resultado = $dao->listarActos();
    
    $arr = array();
    
    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $programa = new Programa();
        $programa->id=$row["id_Programa"];
        $programa->titulo=$row["titulo"];
        $programa->fecha=$row["fecha"];
        $programa->enUso=$row["enUso"];
        $programa->categoria=$row["id_Categoria"];
        array_push($arr, $programa);
    }
    echo json_encode($arr);
?>

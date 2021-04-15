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

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();

    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $titulo = htmlspecialchars($_GET["titulo"]);
    $categoria = htmlspecialchars($_GET["categoria"]);
    $fecha = htmlspecialchars($_GET["fecha"]);
    //endregion

    // comprobamos que no faltan datos vitales
    if (!empty($titulo) && !empty($categoria) && !empty($fecha) ) {
        // Tenemos todos los datos
        //Comprobamos que el registro no existe ya en la base de datos 
        if ($cf->comprobarExisteActoPorTitulo($titulo)) {
            // el programa ya existe
            echo json_encode(array("error : 1, message : El acto ya existe" ));
        } else {
            // el programa no existe 
            $query = "INSERT INTO programas (id_Programa, titulo, categoria, fecha) VALUES (null,'".$titulo."','".$categoria."','".$fecha."');";
            // echo "La consulta para insertar un programa es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar el programa es ".$query;
            
            $stmt->execute();
            echo json_encode(array("error : 0, message : Elemento creado"));
        }
    } else {
        echo json_encode("error : 1, message : Faltan uno o más datos");
    }

    


?>
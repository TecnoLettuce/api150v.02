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

    $tituloHistoriaRecibido = htmlspecialchars($_GET["tituloHistoria"]);
    $subtituloHistoriaRecibido = htmlspecialchars($_GET["subtituloHistoria"]);
    $descripcionRecibida = htmlspecialchars($_GET["descripcion"]);
    //endregion

    // comprobamos que no faltan datos vitales
    if (!empty($tituloHistoriaRecibido) && !empty($subtituloHistoriaRecibido) && !empty($descripcionRecibida) ) {
        // Tenemos todos los datos
        //Comprobamos que el registro no existe ya en la base de datos 
        if ($cf->comprobarExisteHistoriaPorTitulo($tituloHistoriaRecibido)) {
            // la Historia ya existe
            echo json_encode(array("error : 1, message : La historia ya existe" ));
        } else {
            // la historia no existe 
            $query = "INSERT INTO historias (id_Historia, titulo, subtitulo, descripcion) VALUES (null,'".$tituloHistoriaRecibido."','".$subtituloHistoriaRecibido."', '".$descripcionRecibida."');";
            // echo "La consulta para insertar una historia es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar la historia es ".$query;
            
            $stmt->execute();
            echo json_encode(array("error : 0, message : Elemento creado"));
        }
    } else {
        echo json_encode("error : 1, message : Faltan uno o más datos");
    }

    


?>
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
 
     $idSaludo = htmlspecialchars($_GET["idSaludo"]);
     $nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);
     $nuevaDescripcion = htmlspecialchars($_GET["nuevaDescripcion"]);
     $nuevoTexto = htmlspecialchars($_GET["nuevoTexto"]);

     
     //endregion
     $token = htmlspecialchars($_GET["token"]);

    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) == 1) { 
        if (!empty($idSaludo) && !empty($nuevoTitulo) && !empty($nuevaDescripcion) && !empty($nuevoTexto)) {
            // Tenemos todos los datos ok
            // Comprobamos que el id existe
            if ($cf->comprobarExisteSaludoPorId($idSaludo)) {
    
                $database = new Database();
                $query = "UPDATE saludos SET titulo = '".$nuevoTitulo."', descripcion = '".$nuevaDescripcion."', texto = '".$nuevoTexto."' WHERE id_Saludo LIKE ".$idSaludo.";";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
                echo json_encode(" error : 0, message : Elemento actualizado");
            } else {
                echo json_encode(" error : 2, message : El registro no existe");
            }
         } else {
             echo json_encode(" error : 1, message : Faltan uno o más datos");
         }
    
    } elseif ($cf->comprobarTokenAdmin($token) == 0) {
        echo json_encode("error : 2, message : no tiene permisos para realizar esta operación");
    } else {
        echo json_encode("error : 3, message : token no valido");
    }

     // lo primero es comprobar que existe el elemento que se quiere modificar 
     
?>
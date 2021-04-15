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
 
     $idPrograma = htmlspecialchars($_GET["idPrograma"]);
     $nuevoTitulo = htmlspecialchars($_GET["nuevoTitulo"]);
     $nuevaCategoria = htmlspecialchars($_GET["nuevaCategoria"]);
     $nuevaFecha = htmlspecialchars($_GET["nuevaFecha"]);

     
     //endregion

     // lo primero es comprobar que existe el elemento que se quiere modificar 
     if (!empty($idPrograma)) {
        // Tenemos todos los datos ok
        // Comprobamos que el id existe
        if ($cf->comprobarExisteActoPorId($idPrograma)) {

            // Comporbar qué dato es el que se desea cambiar, mirando cuales están vacíos
            if (!empty($nuevoTitulo)) {
                $cf->insertarTituloActo($idPrograma, $nuevoTitulo);
            }

            if (!empty($nuevaCategoria)) {
                $cf->insertarCategoriaActo($idPrograma, $nuevaCategoria);
            }

            if (!empty($nuevaFecha)) {
                $cf->insertarFechaActo($idPrograma, $nuevaFecha);
            }

            echo json_encode("error : 0, message : Elemento actualizado");

        } else {
            echo json_encode(" error : 2, message : El registro no existe");
        }
     } else {
         echo json_encode(" error : 1, message : Faltan uno o más datos");
     }



?>
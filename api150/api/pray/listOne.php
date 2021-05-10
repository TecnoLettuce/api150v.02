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
    include_once '../../util/pray.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();

    if (isset($_GET["idOracion"]) && isset($_GET["titulo"]) ) {

        // Recibe el titulo o el ID de un historia y lo busca en la base de datos 
        $id = htmlspecialchars($_GET["idOracion"]);
        $titulo = htmlspecialchars($_GET["titulo"]);
        echo buscarPorAmbos($id, $titulo);

    } else if (isset($_GET["idOracion"])) {

        $id = htmlspecialchars($_GET["idOracion"]);
        echo buscarPorId($id);
        
    } else if (isset($_GET["titulo"])) {

        $titulo = htmlspecialchars($_GET["titulo"]);
        echo buscarPorTitulo($titulo);
        
    } else {

        $log = new Logger();
        $log->incomplete_data();
        
    }


   
    /**
     * Recibe la id de una oración y busca por ella en la base de datos 
     * @param integer $id
     * @return Result Object
     */
    function buscarPorId($id) {
        $query = "SELECT * FROM oraciones WHERE id_Oracion LIKE ".$id.";";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $pray = new Pray();
            $pray->id=$row["id_Oracion"];
            $pray->titulo=$row["titulo"];
            $pray->texto=$row["texto"];
            $pray->enUso=$row["enUso"];
            array_push($arr, $pray);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;
    }
    /**
     * Recibe el titulo de una oración y busca por el en la base de datos 
     * @param string $titulo
     * @return Result Object
     */
    function buscarPorTitulo($titulo) {
        $query = "SELECT * FROM oraciones WHERE titulo LIKE '".$titulo."';";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $pray = new Pray();
            $pray->id=$row["id_Oracion"];
            $pray->titulo=$row["titulo"];
            $pray->texto=$row["texto"];
            $pray->enUso=$row["enUso"];
            array_push($arr, $pray);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;

    }

    /**
     * Recibe la id y el título de una oración y busca por ellos en la base de datos 
     * @param integer $id string $titulo
     * @return Result Object
     */
    function buscarPorAmbos($id, $titulo) {
        $query = "SELECT * FROM oraciones WHERE id_Oracion LIKE ".$id." AND titulo LIKE '".$titulo."';";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $pray = new Pray();
            $pray->id=$row["id_Oracion"];
            $pray->titulo=$row["titulo"];
            $pray->texto=$row["texto"];
            $pray->enUso=$row["enUso"];
            array_push($arr, $pray);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;

    }

?>
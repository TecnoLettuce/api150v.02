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
    include_once '../../util/greetings.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();

    if (isset($_GET["idSaludo"]) && isset($_GET["titulo"]) ) {

        // Recibe el titulo o el ID de un historia y lo busca en la base de datos 
        $id = htmlspecialchars($_GET["idSaludo"]);
        $titulo = htmlspecialchars($_GET["titulo"]);
        echo buscarPorAmbos($id, $titulo);

    } else if (isset($_GET["idSaludo"])) {

        $id = htmlspecialchars($_GET["idSaludo"]);
        echo buscarPorId($id);
        
    } else if (isset($_GET["titulo"])) {

        $titulo = htmlspecialchars($_GET["titulo"]);
        echo buscarPorTitulo($titulo);
        
    } else {

        $log = new Logger();
        $log->incomplete_data();
        
    }

    /**
     * Recibe la id de un saludo y busca por ella en la base de datos 
     * @param integer $id
     * @return Result Object
     */
    function buscarPorId($id) {
        $query = "SELECT * FROM saludos WHERE id_Saludo LIKE ".$id.";";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $saludo = new Saludo();
            $saludo->id=$row["id_Saludo"];
            $saludo->titulo=$row["titulo"];
            $saludo->descripcion=$row["descripcion"];
            $saludo->texto=$row["texto"];
            $saludo->enUso=$row["enUso"];
            array_push($arr, $saludo);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;
    }
    /**
     * Recibe el titulo de un saludo y busca por el en la base de datos 
     * @param string $titulo
     * @return Result Object
     */
    function buscarPorTitulo($titulo) {
        $query = "SELECT * FROM saludos WHERE titulo LIKE '".$titulo."';";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $saludo = new Saludo();
            $saludo->id=$row["id_Saludo"];
            $saludo->titulo=$row["titulo"];
            $saludo->descripcion=$row["descripcion"];
            $saludo->texto=$row["texto"];
            $saludo->enUso=$row["enUso"];
            array_push($arr, $saludo);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;

    }

    /**
     * Recibe la id y el título de un saludo y busca por ellos en la base de datos 
     * @param integer $id string $titulo
     * @return Result Object
     */
    function buscarPorAmbos($id, $titulo) {
        $query = "SELECT * FROM saludos WHERE id_Saludo LIKE ".$id." AND titulo LIKE '".$titulo."';";
        $database = new Database();
        $resultado = $database->getConn()->query($query);
        
        $arr = array();
        
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $saludo = new Saludo();
            $saludo->id=$row["id_Saludo"];
            $saludo->titulo=$row["titulo"];
            $saludo->descripcion=$row["descripcion"];
            $saludo->texto=$row["texto"];
            $saludo->enUso=$row["enUso"];
            array_push($arr, $saludo);
        }
        $paraDevolver = json_encode($arr);
        return $paraDevolver;

    }

?>
<?php
include_once '../../config/database.php';
include_once '../../objects/user.php';
include_once '../../objects/session.php';
include_once 'commonFunctions.php';
include_once 'logger.php';

class UploadCommonFunctions {

    // Atributos
    // Constructor 
    public function __construct() {
    }
    // Getters && Setters 
    // Metodos 

    // Esto debe recibir un array de medios
    function insertarMedios($arrayDeMedios) {
        $database = new Database();
        $cf = new CommonFunctions();
        $log = new Logger();
        $idsParadevolver = array();

        // Divide el array
        $urlsAInsertar = array();
        $tiposAInsertar = array();
        $nombresAInsertar = array();
    
        for ($i=0; $i < count($arrayDeMedios, COUNT_NORMAL); $i++) { 

            array_push($nombresAInsertar, $arrayDeMedios[$i]->nombre);
            array_push($urlsAInsertar, $arrayDeMedios[$i]->url);
            array_push($tiposAInsertar, $arrayDeMedios[$i]->tipo);
        }

        if (count($urlsAInsertar, COUNT_NORMAL) > 0 && count($tiposAInsertar, COUNT_NORMAL) > 0) {
            // Tenemos todos los datos
            // Recorremos el array para realizar la inserción
            for ($i = 0; $i < count($urlsAInsertar, COUNT_NORMAL); $i++) {

                // Comprobamos si existe el medio
                if ($cf->comprobarExisteMedioPorURL($urlsAInsertar[$i])) {
                    // Existe medio 
                    // $log->already_exists("Medio");
                    // Si el medio existe se extrae la id y se mete al array de ids
                    $query = "SELECT id_medio FROM medios WHERE url LIKE '".$urlsAInsertar[$i]."';";
                    $resultado = $database->getConn()->query($query);
                    $idObtenida = -1;
                    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        $idObtenida = $row["id_medio"];
                    }
                    array_push($idsParadevolver, $idObtenida);

                } else {
                    // No existe 
                    $nombreParaInsertar = $nombresAInsertar[$i];
                    $urlParaInsertar = $urlsAInsertar[$i];
                    $tipoParaInsertar = $tiposAInsertar[$i];
                    $query = "INSERT INTO medios(nombre, url, id_Tipo) VALUES ( '".$nombreParaInsertar."', '" . $urlParaInsertar . "' , (SELECT tipos.id_Tipo FROM tipos WHERE tipos.descripcion LIKE '" . $tipoParaInsertar . "'));";
                    // echo "DEBUG > Consulta que se manda a la inserción de medios > ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                    // Ahora se hace la comprobación de que se ha insertado bien 
                    $query = "SELECT id_medio FROM medios WHERE url LIKE '" . $urlParaInsertar . "';";
                    $resultado = $database->getConn()->query($query);
                    $idObtenida = -1;
                    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        $idObtenida = $row["id_medio"];
                    }

                    if ($idObtenida < 0) {
                        // Algo ha ido mal
                        $log->fatal_error("Algo ha ido mal insertando el medio");
                    } else {
                        // echo json_encode(array("status : 0, message : Elemento creado"));
                        array_push($idsParadevolver, $idObtenida);
                    }
                }
            } // Salida del for
            return $idsParadevolver; // devolvemos las id
        } else {
            $log->incomplete_data();
        }
    }
}
?>
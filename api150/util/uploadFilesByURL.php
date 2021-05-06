<?php
    include_once '../../config/database.php';
    include_once '../../objects/user.php';
    include_once '../../objects/session.php';
    include_once './commonFunctions.php';
class UploadCommonFunctions {

    // Atributos
    // Constructor 
    public function __construct() {
    } 
    // Getters && Setters 
    // Metodos 
    function insertarMedios ($arrayURL, $arrayTipo, $token) {
        $database = new Database();
        $cf = new CommonFunctions();
        $idsParadevolver = array();

        // Comprobar token admin
        if ($cf->comprobarTokenAdmin($token) == 1) {
            // Authenticated
            if ($cf->comprobarExpireDate($token)) {
                $cf->actualizarExpireDate($token);

                if (count($arrayURL, COUNT_NORMAL) > 0 && count($arrayTipo, COUNT_NORMAL) > 0) {
                    // Tenemos todos los datos
                    // Recorremos el array para realizar la inserción
                    for ($i=0; $i < count($arrayURL,COUNT_NORMAL); $i++) { 

                        // Comprobamos si existe el medio
                        if ($cf->comprobarExisteMedioPorURL($arrayURL[$i])) {
                            // Existe medio 
                            return json_encode("status : 406, message : El elemento que intenta insertar ya existe");
                        } else {
                            // No existe 
                            $urlParaInsertar = $arrayURL[$i];
                            $tipoParaInsertar = $arrayTipo[$i];
                            $query = "INSERT INTO medios( url, id_Tipo) VALUES ('".$urlParaInsertar."' , (SELECT tipos.id_Tipo FROM tipos WHERE tipos.descripcion LIKE '".$tipoParaInsertar."'));";
                            // echo "DEBUG > Consulta que se manda a la inserción de medios > ".$query;
                            $stmt = $database->getConn()->prepare($query);
                            $stmt->execute();
                            // Ahora se hace la comprobación de que se ha insertado bien 
                            $query = "SELECT id_medio FROM medios WHERE url LIKE '".$urlParaInsertar."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_medio"];
                            }

                            if ($idObtenida < 0) {
                                // Algo ha ido mal
                                echo json_encode("Fatal error : Algo ha ido mal en la consulta de inserción");
                            } else {
                                // echo json_encode(array("status : 0, message : Elemento creado"));
                                array_push($idsParadevolver, $idObtenida);
                            }
                        }
                    } // Salida del for
                    return $idsParadevolver; // devolvemos las id
                } else {
                    return json_encode("status : 400, message : Faltan uno o más datos");
                }
            } else {
                // Tiempo de sesión excedido
                return json_encode("status : 401, message : Tiempo de sesión excedido");
            }
        } else if ($cf->comprobarTokenAdmin($token) == 0) {
            // Sin permisos
            return json_encode("status : 401, message : no tiene permisos para insertar el medio");
        } else {
            return json_encode("status : 403, message : token no valido");
        }
    }
}

?>
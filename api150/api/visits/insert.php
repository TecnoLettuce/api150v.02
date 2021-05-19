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

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $logger = new Logger();
    $dao = new Dao();
    $ucf = new UploadCommonFunctions();

    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $tituloVisita = $data->tituloVisita;
    $descripcion = $data->descripcion;
    $token = $data->token;

    // Datos de los medios

    $arrayMedios = array();
    $arrayMedios = $data->medios;
    
    $mediosAInsertar = array();
    $tiposAInsertar = array();
    
    
    
    for ($i=0; $i < count($arrayMedios, COUNT_NORMAL); $i++) { 
        array_push($mediosAInsertar, $arrayMedios[$i]->url);
        array_push($tiposAInsertar, $arrayMedios[$i]->tipo);
    }
    //endregion


    // Comprobamos que tiene permisos de administrador
    if ($cf->comprobarTokenAdmin($token) >= 0) { 

        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            $cf->actualizarExpireDate($token);
            // comprobación de que los datos se reciben correctamente
            if (!empty($tituloVisita)) {
                // tengo todos los datos que necesito
                //Comprobamos que el registro no existe ya en la base de datos 
                if ($cf->comprobarExisteVisitaPorTitulo($tituloVisita)) {
                    // El visita ya existe
                    http_response_code(406);
                    echo $logger->already_exists("visita");
                } else {
                    // el visita no existe 

                    // Hay medios para insertar? 
                    if (!empty($mediosAInsertar) && !empty($tiposAInsertar) && ( count($tiposAInsertar, COUNT_NORMAL) == count($mediosAInsertar, COUNT_NORMAL))) {
                        // Hay medios
                        $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

                        // Comprobamos el resultado 
                        if (is_array($resultadoMedios)) {
                            // Tenemos array de ids
                            // Insertamos la visita
                            $dao->insertarVisita($tituloVisita, $descripcion);

                            // Consultamos el elemento que acabamos de insertar
                            $query = "SELECT id_Visita FROM visitas WHERE titulo LIKE '".$tituloVisita."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_Visita"];
                            }

                            // Comprobamos si la id obtenida es válida
                            if ($idObtenida < 0 ) {
                                // no es válida
                                http_response_code(503);
                                $logger->fatal_error("Algo ha ido mal extrayendo la id");
                            } else {
                                // Tenemos la id y los medios insertados
                                // Insertamos las relaciones
                                $relacionesInsertadas = array();
                                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                                    $query = "INSERT INTO rel_visita( id_Medio, id_Visita) VALUES (".$resultadoMedios[$i].",".$idObtenida.");";
                                    // echo "La consulta para insertar las relaciones es es ".$query;
                                    $stmt = $database->getConn()->prepare($query);
                                    $stmt->execute();
                                    array_push($relacionesInsertadas, $logger->created_element());
                                } // Salida del for
                                http_response_code(201);
                                $logger->created_element();
                            }
                        } else {
                            echo json_encode(array("status" => 418, "message" => "El servidor se rehúsa a intentar hacer café con una tetera"));
                        }
                        
                    } else {
                        // No hay medios
                        // Insertamos solamente la visita
                        $dao->insertarVisita($tituloVisita, $descripcion);
                        http_response_code(201);
                        echo $logger->created_element();
                    }
                }

            } else {
                http_response_code(400);
                echo $logger->incomplete_data();
            }

        } else {
            http_response_code(401);

            echo $logger->expired_session();
        }


    } else {
        http_response_code(403);
        echo $logger->invalid_token();
    }
    

?>
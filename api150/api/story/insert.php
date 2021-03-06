<?php 
    //region imports
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Access-Control-Allow-Headers, Authorization, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
    header("Access-Control-Max-Age: 3600");
    //endregion
    /* cosa de la que no me fio */
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Indica los métodos permitidos.
        header('Access-Control-Allow-Methods: GET, POST, DELETE');
        // Indica los encabezados permitidos.
        header('Access-Control-Allow-Headers: Authorization'); 
        http_response_code(204);
    }

    // Conexión con la base de datos 
    include_once '../../config/database.php';
    include_once '../../util/commonFunctions.php';
    include_once '../../util/uploadFilesByURL.php';
    include_once '../../util/logger.php';
    include_once '../../objects/DAO.php';

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $ucf = new UploadCommonFunctions();
    $logger = new Logger();
    $dao = new Dao();
    include_once '../../config/rolConfig.php';

    $rolConfig = new RolConfig();
    $permissionLevel = [$rolConfig->adminRol, $rolConfig->editorRol]; // Ambos

    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $tituloHistoriaRecibido = $data->titulo;
    $subtituloHistoriaRecibido = $data->subtitulo;
    $descripcionRecibida = $data->descripcion;
    $boolEnUso = $data->enUso;
    $token = $data->token;
    // Datos de los medios

    $arrayMedios = array();
    $arrayMedios = $data->medios;

    //endregion

    // Comprobamos que tiene permisos de administrador
    if ($cf->checkPermission($token, $permissionLevel) == 1) { 
        // comprobamos que la sesión no ha caducado
        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            $cf->actualizarExpireDate($token);

            if (!empty($tituloHistoriaRecibido) && !empty($subtituloHistoriaRecibido) && !empty($descripcionRecibida) && $boolEnUso != null ) {
                // Tenemos todos los datos
                //Comprobamos que el registro no existe ya en la base de datos 
                if ($cf->comprobarExisteHistoriaPorTitulo($tituloHistoriaRecibido)) {
                    // la Historia ya existe
                    http_response_code(406);
                    $logger->already_exists("historia");
                } else {
                    // la historia no existe 

                    // Hay medios para insertar? 
                    if (!empty($arrayMedios)) {
                        // Hay medios para insertar 
                        // Insertamos los medios
                        $resultadoMedios = $ucf->insertarMedios($arrayMedios);
                        
                        // Comprobamos el resultado
                        if (is_array($resultadoMedios)) {
                            // Tenemos array de ids

                            // Insertamos la historia
                            $dao->insertarHistoria($tituloHistoriaRecibido, $subtituloHistoriaRecibido, $descripcionRecibida, $boolEnUso);
        
                            
                            // Consultar el elemento que se acaba de insertar
                            $query = "SELECT id_Historia FROM historias WHERE titulo LIKE '".$tituloHistoriaRecibido."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_Historia"];
                            }

                            // Comprobamos que la id obtenida es válida
                            if ($idObtenida < 0) {
                                // No es valida
                                http_response_code(503);
                                $logger->fatal_error("Algo ha ido mal extrayendo la id");
                            } else {
                                // Tenemos la id y los medios insertados

                                // Insertamos las relaciones
                                $relacionesInsertadas = array();
                                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                                    $query = "INSERT INTO rel_historia( id_Medio, id_Historia) VALUES (".$resultadoMedios[$i].",".$idObtenida.");";
                                    // echo "La consulta para insertar las relaciones es es ".$query;
                                    $stmt = $database->getConn()->prepare($query);
                                    $stmt->execute();
                                    array_push($relacionesInsertadas, $logger->created_element());
                                } // Salida del for
                                http_response_code(200);
                                $logger->created_element();
                            }

                        } else {
                            // Ha dado fallo
                            echo json_encode(array("status" => 418, "message" => "El servidor se rehúsa a intentar hacer café con una tetera"));
                        }
                    } else {
                        // No hay medios para insertar
                        // Insertamos solo la historia
                        // Insertamos la historia
                        $dao->insertarHistoria($tituloHistoriaRecibido, $subtituloHistoriaRecibido, $descripcionRecibida, $boolEnUso);
                        http_response_code(200);
                        $logger->created_element();
                    }                        
                }
            } else {
                http_response_code(400);
                $logger->incomplete_data();
            }
        } else {
            http_response_code(401);
            $logger->expired_session();
        }
    } else {
        http_response_code(403);
        $logger->invalid_token();
    }
?>
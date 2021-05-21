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
    include_once '../../config/rolConfig.php';

    $rolConfig = new RolConfig();
    $permissionLevel = [$rolConfig->adminRol, $rolConfig->editorRol]; // Ambos

    //Creación de la base de datos 
    $database = new Database();
    // Declaración de commonFunctions
    $cf = new CommonFunctions();
    $dao = new Dao(); 
    $logger = new Logger();
    $ucf = new UploadCommonFunctions();


    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $titulo = $data->titulo;
    $descripcion = $data->descripcion;
    $texto = $data->texto;
    $boolEnUso = $data->enUso;
    $token = $data->token;
    //endregion

    $arrayMedios = array();
    $arrayMedios = $data->medios;

    $mediosAInsertar = array();
    $tiposAInsertar = array();



    for ($i=0; $i < count($arrayMedios, COUNT_NORMAL); $i++) { 
        array_push($mediosAInsertar, $arrayMedios[$i]->url);
        array_push($tiposAInsertar, $arrayMedios[$i]->tipo);
    }



    // Comprobamos que tiene permisos de administrador
    if ($cf->checkPermission($token, $permissionLevel) == 1) { 

        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            $cf->actualizarExpireDate($token);

            if (!empty($titulo) && !empty($descripcion) && !empty($texto) && $boolEnUso != null) {
                // Tenemos todos los datos
                //Comprobamos que el registro no existe ya en la base de datos 
                if ($cf->comprobarExisteSaludoPorTitulo($titulo)) {
                    // El Saludo ya existe
                    http_response_code(406);
                    echo $logger->already_exists("saludo");
                } else {
                    // El saludo no existe 

                    // Hay medios para insertar?
                    if (!empty($mediosAInsertar) && !empty($tiposAInsertar) && ( count($mediosAInsertar, COUNT_NORMAL) == count($tiposAInsertar, COUNT_NORMAL))) {
                        $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

                        // Comprobamos el resultado 
                        if (is_array($resultadoMedios)) {
                            // Tenemos array de URLs

                            // insertamos el saludo
                            $dao->insertarSaludo($titulo, $descripcion, $texto, $boolEnUso);

                            // Consultamos el elemento que acabamos de insertar
                            $query = "SELECT id_Saludo FROM saludos WHERE titulo LIKE '".$titulo."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_Saludo"];
                            }

                            // comprobamos que la id obtenida es válida
                            if ($idObtenida < 0) {
                                http_response_code(503);
                                $logger->fatal_error("Algo ha ido mal extrayendo la id");
                            } else {
                                
                                // Tenemos la id y los medios insertados 
                                // Insertamos las relaciones
                                $relacionesInsertadas = array();
                                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                                    $query = "INSERT INTO rel_saludo( id_Medio, id_Saludo) VALUES (".$resultadoMedios[$i].",".$idObtenida.");";
                                    // echo "La consulta para insertar las relaciones es es ".$query;
                                    $stmt = $database->getConn()->prepare($query);
                                    $stmt->execute();
                                    array_push($relacionesInsertadas, $logger->created_element());
                                } // Salida del for
                                http_response_code(200);
                                $logger->created_element();
                            }


                        } else {
                            echo json_encode(array("status" => 418, "message" => "El servidor se rehúsa a intentar hacer café con una tetera"));
                        }
                    } else {
                        // No hay medios para insertar
                        // Insertamos solo el saludo
                        $dao->insertarSaludo($titulo, $descripcion, $texto, $boolEnUso);
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
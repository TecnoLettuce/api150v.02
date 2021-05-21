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
    $logger = new Logger();
    $dao = new Dao();
    $ucf = new UploadCommonFunctions();
    //region Definicion de los datos que llegan
    $data = json_decode(file_get_contents("php://input"));

    $tituloRecibido = $data->titulo;
    $letraRecibida = $data->letra;
    $boolEnUso = $data->enUso;
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
    if ($cf->checkPermission($token, $permissionLevel) == 1) { 

        if ($cf->comprobarExpireDate($token)) {
            // La sesión es válida
            $cf->actualizarExpireDate($token);
            // comprobación de que los datos se reciben correctamente
            if (!empty($letraRecibida) && !empty($tituloRecibido) && $boolEnUso != null) {
                // tengo todos los datos que necesito
                //Comprobamos que el registro no existe ya en la base de datos 
                if ($cf->comprobarExisteHimnoPorTitulo($tituloRecibido)) {
                    // El himno ya existe
                    http_response_code(406);
                    echo $logger->already_exists("himno");
                } else {
                    // El himno no existe 

                    // Hay medios para insertar? 
                    if (!empty($mediosAInsertar) && !empty($tiposAInsertar) && ( count($tiposAInsertar, COUNT_NORMAL) == count($mediosAInsertar, COUNT_NORMAL))) {
                        // Hay medios para insertar
                        // Insertamos los medios 
                        $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

                        // comprobamos el reusltado 
                        if (is_array($resultadoMedios)) {

                            // Insertamos el himno 
                            $dao->insertarHimno($tituloRecibido, $letraRecibida, $boolEnUso);

                            // Consultamos el himno que se acaba de insertar 
                            $query = "SELECT id_Himno FROM himnos WHERE titulo LIKE '".$tituloRecibido."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_Himno"];
                            }

                            // Consultamos que la id obtenida es válida 

                            if ($idObtenida < 0) {
                                http_response_code(503);
                                $logger->fatal_error("Algo ha ido mal extrayendo la id");

                            } else {
                                // Tenemos la id y los medios insertados 

                                // insertamos las relaciones 
                                $relacionesInsertadas = array();
                                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                                    $query = "INSERT INTO rel_himno (id_Medio, id_Himno) VALUES (".$resultadoMedios[$i].",".$idObtenida.");";
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
                        $dao->insertarHimno($tituloRecibido, $letraRecibida, $boolEnUso);
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
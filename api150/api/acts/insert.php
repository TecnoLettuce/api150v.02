<?php 
    //region imports
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
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
    $ubicacion = $data->$ubicacion;
    $fecha = $data->fecha;
    $boolEnUso = $data->enUso;
    $categoria = $data->categoria;
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


    // Comprobamos que tiene permisos de administrador
    if ($cf->checkPermission($token, $permissionLevel) == 1) { 
        // Comprobamos que su sesión no ha caducado
        if ($cf->comprobarExpireDate($token)) {
            // La sesión no ha caducado, por lo que seguimos adelante y le otorgamos 2 minutos más
            

            $cf->actualizarExpireDate($token); 


            // comprobamos que no faltan datos vitales
            if (!empty($titulo) && !empty($descripcion) && !empty($ubicacion) &&!empty($categoria) && !empty($fecha) && $boolEnUso!=null) {
                // Tenemos todos los datos
                //Comprobamos que el registro no existe ya en la base de datos 
                if ($cf->comprobarExisteActoPorTitulo($titulo)) {
                    // el programa ya existe
                    http_response_code(406);
                    echo $logger->already_exists("acto");
                } else {
                    // el programa no existe 

                    // Hay medios para insertar?
                    if (!empty($mediosAInsertar) && !empty($tiposAInsertar) && (count($tiposAInsertar, COUNT_NORMAL) == count($mediosAInsertar,COUNT_NORMAL))) {
                        // Hay medios para insertar, insertamos los medios
                        $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

                        // Comprobamos los resultados
                        if (is_array($resultadoMedios)) {
                            // tenemos el array de ids
                            // insertamos el acto 
                            $dao->insertarActo( $titulo, $descripcion, $ubicacion, $fecha, $boolEnUso, $categoria);

                            // Consultamos la id del elemento que acabamos de insertar
                            // Consultar el elemento que se acaba de insertar
                            $query = "SELECT id_Programa FROM programas WHERE titulo LIKE '".$titulo."';";
                            $resultado = $database->getConn()->query($query);
                            $idObtenida = -1;
                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                $idObtenida = $row["id_Programa"];
                            }

                            //Comprobamos que la id es válida
                            if ($idObtenida < 0) {
                                // No es válida
                                http_response_code(503);
                                $logger->fatal_error("Algo ha ido mal extrayendo la id");

                            } else {
                                //tenemos la id y los medios insertados

                                // Insertamos las relaciones 
                                $relacionesInsertadas = array();
                                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                                    $query = "INSERT INTO rel_programa ( id_Medio, id_Programa) VALUES (".$resultadoMedios[$i].",".$idObtenida.");";
                                    // echo "La consulta para insertar las relaciones es es ".$query;
                                    $stmt = $database->getConn()->prepare($query);
                                    $stmt->execute();
                                    array_push($relacionesInsertadas, $logger->created_element());
                                } // Salida del for
                                http_response_code(200);
                                $logger->created_element();
                            }
                            
                        } else {
                            echo json_encode(array("status" => 418, "message" => "El servidor se rehusa a intentar hacer café con una tetera"));
                        }

                    } else {
                        // No hay medios para insertar, insertamos solo el acto
                        $dao->insertarActo( $titulo, $descripcion, $ubicacion, $fecha, $boolEnUso, $categoria);
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
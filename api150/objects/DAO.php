<?php 
/*
    include_once '../config/database.php';
    include_once '../util/act.php';
    include_once '../util/ambiente.php';
    include_once '../util/greetings.php';
    include_once '../util/commonFunctions.php';
    include_once '../util/historia.php';
    include_once '../util/hymn.php';
    include_once '../util/logger.php';
    include_once '../util/material.php';
    include_once '../util/phrase.php';
    include_once '../util/pray.php';
    include_once '../util/visit.php';
    include_once '../objects/session.php';
    include_once '../util/uploadFilesByURL.php';

*/

    class Dao {

        public function __construct() {
            $database = new Database();
            $cf = new CommonFunctions();
            $logger = new Logger();
            $cfu = new UploadCommonFunctions();
			
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);
        }

        //region Actos
        // Métodos para el endpoint de insertar actos 
        function insertarActo ($titulo, $descripcion, $ubicacion, $fecha, $boolEnUso, $categoria) {
            $database = new Database();
            $timestamp = strtotime($fecha);
            $query = "INSERT INTO programas (id_Programa, titulo, descripcion, ubicacion, fecha, enUso, id_Categoria) VALUES (null,'".$titulo."', '".$descripcion."','".$ubicacion."', ".$timestamp.",".$boolEnUso.", '".$categoria."');";
            // echo "La consulta para insertar un programa es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar el programa es ".$query;
            $stmt->execute();
        }

        // Métodos para el endpoint de borrar actos
        function borrarActo($idRecibida) {
            $database = new Database();
            $query = "DELETE FROM programas WHERE id_Programa like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        // Métodos para listar actos
        function listarActos() {
            $database = new Database();
            // No tiene que recibir parámetros es solo la consulta pelada
            $query = "SELECT * FROM programas;";
            $resultado = $database->getConn()->query($query);
            return $resultado;
        }

        // Listar acto por id + titulo
        function listarUnActoPorIdyTitulo($id, $titulo) {
            $query = "SELECT programas.id_Programa, programas.titulo, programas.descripcion AS 'descPrograma', programas.ubicacion, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.id_Programa LIKE ".$id." AND programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
                $programa->descripcion=$row["descPrograma"];
                $programa->ubicacion=$row["ubicacion"];
                $programa->fecha=$row["fecha"];
                $programa->enUso=$row["enUso"];
                $programa->categoria=$row["id_Categoria"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $programa->medios = $arrayMedios;
            $paraDevolver = json_encode($programa);
            return $paraDevolver;
        }
        // Listar acto por id
        function listarUnActoPorId($id) {
            $query = "SELECT programas.id_Programa, programas.titulo, programas.descripcion AS 'descPrograma', programas.ubicacion, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.id_Programa LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
                $programa->descripcion=$row["descPrograma"];
                $programa->ubicacion=$row["ubicacion"];
                $programa->fecha=$row["fecha"];
                $programa->enUso=$row["enUso"];
                $programa->categoria=$row["id_Categoria"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $programa->medios = $arrayMedios;
            $paraDevolver = json_encode($programa);
            return $paraDevolver;
            
        }
        // Listar acto por titulo
        function listarUnActoPorTitulo($titulo) {
            $query = "SELECT programas.id_Programa, programas.titulo, programas.descripcion AS 'descPrograma', programas.ubicacion, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
                $programa->descripcion=$row["descPrograma"];
                $programa->ubicacion=$row["ubicacion"];
                $programa->fecha=$row["fecha"];
                $programa->enUso=$row["enUso"];
                $programa->categoria=$row["id_Categoria"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $programa->medios = $arrayMedios;
            $paraDevolver = json_encode($programa);
            return $paraDevolver;
        }

        public function listarCadaActo() {
            $database = new Database();
            $query = "SELECT * FROM programas INNER JOIN rel_programa ON programas.id_Programa = rel_programa.id_Programa LEFT JOIN medios ON medios.id_Medio = rel_programa.id_Medio";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];

                if ($programa->id != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT programas.id_Programa, programas.titulo, programas.descripcion as 'descr', programas.ubicacion, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.id_Programa LIKE ".$programa->id.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                     while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $programa = new Programa();
                        $programa->id=$row2["id_Programa"];
                        $programa->titulo=$row2["titulo"];
                        $programa->descripcion=$row2["descr"];
                        $programa->ubicacion=$row2["ubicacion"];
                        $programa->fecha=$row2["fecha"];
                        $programa->enUso=$row2["enUso"];
                        $programa->categoria=$row2["id_Categoria"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                   $programa->medios = $arrayMedios;
                   array_push($arr, $programa);
                } else {
                    // No se lista
                }
                $idsYaListadas = $programa->id;
            }
            return $arr;
        }

        // Métodos para el endpoint de update Actos 
        function actualizarActo ($nuevoTitulo, $nuevaDescripcion, $nuevaUbicacion, $nuevaFecha, $boolEnUso, $idPrograma, $mediosAInsertar, $tiposAInsertar) {
            $database = new Database();
            $query = "DELETE FROM rel_programa WHERE id_Programa LIKE ".$idPrograma.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);
                   
            // Comprobamos el resultado
            if (is_array($resultadoMedios)) {
                // Tenemos array de ids
                // Actualizar
                $timestamp = strtotime($nuevaFecha);
               $query = "UPDATE programas SET titulo = '".$nuevoTitulo."', descripcion = '".$nuevaDescripcion."', ubicacion = '".$nuevaUbicacion."' , fecha = ".$timestamp.", enUso = ".$boolEnUso." WHERE id_Programa LIKE ".$idPrograma.";";
                //echo "consulta > ".$query;
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
        
                // Actualizar las relaciones 
                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_programa( id_Medio, id_Programa) VALUES (".$resultadoMedios[$i].",".$idPrograma.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } // Salida del for

            } else {
                // Algo ha ido mal al insertar los medios 
                echo "Algo ha ido mal al actualizar los medios desde el endpoint historia";
            }
           
        }
        
        //endregion

        //region Saludos
        // Métodos para el endpoint de insertar saludos
        function insertarSaludo($titulo, $descripcion, $texto, $boolEnUso){
            $database = new Database();
            $query = "INSERT INTO saludos (id_Saludo, titulo, descripcion, texto, enUso) VALUES (null,'".$titulo."','".$descripcion."', '".$texto."', ".$boolEnUso.");";
            // echo "La consulta para insertar una historia es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar la historia es ".$query;
            $stmt->execute();
        }

        // Métodos para el endpoint de borrar saludos
        function borrarSaludo($idRecibida){
            $database  = new Database();
            $query = "DELETE FROM saludos WHERE id_Saludo like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        // Métodos para el endpoint de listar saludos
        function listarSaludos(){
            $database = new Database();
            $query = "SELECT * FROM saludos;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = new SaludoDTO();
                $saludo->id=$row["id_Saludo"];
                $saludo->titulo=$row["titulo"];
                // $saludo->descripcion=$row["descripcion"];
                // $saludo->texto=$row["texto"];
                // $saludo->enUso=$row["enUso"];
                array_push($arr, $saludo);
            }
            return $arr;
        }

        // Métodos para el endpoint de listar un saludo
        function listarUnSaludoPorId($id) {
            $query = "SELECT saludos.id_Saludo, saludos.titulo, saludos.descripcion AS 'saludosDesc', saludos.texto, saludos.enUso, medios.url, tipos.descripcion FROM saludos INNER JOIN rel_saludo ON saludos.id_Saludo=rel_saludo.id_Saludo INNER JOIN medios ON rel_saludo.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE saludos.id_Saludo LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = new Saludo();
                $saludo->id=$row["id_Saludo"];
                $saludo->titulo=$row["titulo"];
                $saludo->descripcion=$row["saludosDesc"];
                $saludo->texto=$row["texto"];
                $saludo->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $saludo->medios = $arrayMedios;
            $paraDevolver = json_encode($saludo);
            return $paraDevolver;
        }

        public function listarCadaSaludo() {
            $database = new Database();
            $query = "SELECT * FROM saludos INNER JOIN rel_saludo ON saludos.id_Saludo = rel_saludo.id_Saludo LEFT JOIN medios ON medios.id_Medio = rel_saludo.id_Medio";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = new Saludo();
                $saludo->id=$row["id_Saludo"];

                if ($saludo->id != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT saludos.id_Saludo, saludos.titulo, saludos.descripcion as 'descr', saludos.texto, saludos.enUso, medios.url, tipos.descripcion FROM saludos INNER JOIN rel_saludo ON saludos.id_Saludo=rel_saludo.id_Saludo INNER JOIN medios ON rel_saludo.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE saludos.id_Saludo LIKE ".$saludo->id.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                    while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $saludo = new Saludo();
                        $saludo->id=$row2["id_Saludo"];
                        $saludo->titulo=$row2["titulo"];
                        $saludo->descripcion=$row2["descr"];
                        $saludo->texto=$row2["texto"];
                        $saludo->enUso=$row2["enUso"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                    $saludo->medios = $arrayMedios;
                    array_push($arr, $saludo);
                } else {
                    // No se lista
                }
                $idsYaListadas = $saludo->id;
            }
            return $arr;
        }

        function listarUnSaludoPorIdyTitulo($id, $titulo) {
            $query = "SELECT saludos.id_Saludo, saludos.titulo, saludos.descripcion AS 'saludosDesc', saludos.texto, saludos.enUso, medios.url, tipos.descripcion FROM saludos INNER JOIN rel_saludo ON saludos.id_Saludo=rel_saludo.id_Saludo INNER JOIN medios ON rel_saludo.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE saludos.id_Saludo LIKE ".$id." AND saludos.titulo LIKE '".$titulo."'";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = new Saludo();
                $saludo->id=$row["id_Saludo"];
                $saludo->titulo=$row["titulo"];
                $saludo->descripcion=$row["saludosDesc"];
                $saludo->texto=$row["texto"];
                $saludo->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $saludo->medios = $arrayMedios;
            $paraDevolver = json_encode($saludo);
            return $paraDevolver;
        }

        function listarUnSaludoPorTitulo($titulo) {
            $query = "SELECT saludos.id_Saludo, saludos.titulo, saludos.descripcion AS 'saludosDesc', saludos.texto, saludos.enUso, medios.url, tipos.descripcion FROM saludos INNER JOIN rel_saludo ON saludos.id_Saludo=rel_saludo.id_Saludo INNER JOIN medios ON rel_saludo.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE saludos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = new Saludo();
                $saludo->id=$row["id_Saludo"];
                $saludo->titulo=$row["titulo"];
                $saludo->descripcion=$row["saludosDesc"];
                $saludo->texto=$row["texto"];
                $saludo->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $saludo->medios = $arrayMedios;
            $paraDevolver = json_encode($saludo);
            return $paraDevolver;
        }

        // Métodos para el endpoint de update saludos
        function actualizarSaludo($nuevoTitulo, $nuevaDescripcion, $nuevoTexto, $boolEnUso, $idSaludo, $mediosAInsertar, $tiposAInsertar) {
             // Hay que borrar las relaciones de la tabla de relaciones 
            $database = new Database();
            $query = "DELETE FROM rel_saludo WHERE id_Saludo LIKE ".$idSaludo.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);
                   
            // Comprobamos el resultado
             if (is_array($resultadoMedios)) {
                // Tenemos array de ids
                // Actualizar
            $query = "UPDATE saludos SET titulo = '".$nuevoTitulo."', descripcion = '".$nuevaDescripcion."', texto = '".$nuevoTexto."', enUso = ".$boolEnUso." WHERE id_Saludo LIKE ".$idSaludo.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Actualizar las relaciones 
                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_saludo( id_Medio, id_Saludo) VALUES (".$resultadoMedios[$i].",".$idSaludo.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } // Salida del for

            } else {
                // Algo ha ido mal al insertar los medios 
                echo "Algo ha ido mal al actualizar los medios desde el endpoint historia";
            }
        }
        //endregion

        //region himnos
        public function insertarHimno($tituloRecibido, $letraRecibida, $boolEnUso) {
            $database = new Database();
            // el himno no existe 
            $query = "INSERT INTO himnos (id_Himno, titulo, letra, enUso) VALUES (null,'".$tituloRecibido."', '".$letraRecibida."', ".$boolEnUso.");";
            // echo "La consulta para insertar un ambiente es ".$query;
            $stmt = $database->getConn()->prepare($query);
                
            $stmt->execute();

        }

        public function borrarHimno($idRecibida) {
            $database = new Database();
            $query = "DELETE FROM himnos WHERE id_Himno like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarHimnos() {
            $database = new Database();
            $query = "SELECT * FROM himnos;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = new HimnoDTO();
                $himno->id=$row["id_Himno"];
                $himno->titulo=$row["titulo"];
                // $himno->letra=$row["letra"];
                // $himno->enUso=$row["enUso"];
                array_push($arr, $himno);
            }
            return $arr;
        }

        public function listarUnHimnoPorId($id) {
            $query = "SELECT himnos.id_Himno, himnos.titulo, himnos.letra, himnos.enUso, medios.url, tipos.descripcion 
            FROM himnos INNER JOIN rel_himno ON himnos.id_Himno=rel_himno.id_Himno INNER JOIN medios ON rel_himno.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE himnos.id_Himno LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = new Himno();
                $himno->id=$row["id_Himno"];
                $himno->titulo=$row["titulo"];
                $himno->letra=$row["letra"];
                $himno->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $himno->medios = $arrayMedios;
            $paraDevolver = json_encode($himno);
            return $paraDevolver;
        }

        public function listarCadaHimno() {
            $database = new Database();
            $query = "SELECT * FROM himnos INNER JOIN rel_himno ON himnos.id_Himno = rel_himno.id_Himno LEFT JOIN medios ON medios.id_Medio = rel_himno.id_Medio";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = new Himno();
                $himno->id=$row["id_Himno"];

                if ($himno->id != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT himnos.id_Himno, himnos.titulo, himnos.letra, himnos.enUso, medios.url, tipos.descripcion 
                    FROM himnos INNER JOIN rel_himno ON himnos.id_Himno=rel_himno.id_Himno INNER JOIN medios ON rel_himno.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE himnos.id_Himno LIKE ".$himno->id.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                    while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $himno = new Himno();
                        $himno->id=$row2["id_Himno"];
                        $himno->titulo=$row2["titulo"];
                        $himno->letra=$row2["letra"];
                        $himno->enUso=$row2["enUso"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                    $himno->medios = $arrayMedios;
                    array_push($arr, $himno);
                } else {
                    // No se lista
                }
                $idsYaListadas = $himno->id;
            }
            return $arr;
        }

        public function listarUnHimnoPorIdyTitulo($id, $titulo){
            $query = "SELECT himnos.id_Himno, himnos.titulo, himnos.letra, himnos.enUso, medios.url, tipos.descripcion 
            FROM himnos INNER JOIN rel_himno ON himnos.id_Himno=rel_himno.id_Himno INNER JOIN medios ON rel_himno.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE himnos.id_Himno LIKE ".$id." AND himnos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = new Himno();
                $himno->id=$row["id_Himno"];
                $himno->titulo=$row["titulo"];
                $himno->letra=$row["letra"];
                $himno->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $himno->medios = $arrayMedios;
            $paraDevolver = json_encode($himno);
            return $paraDevolver;
        }

        public function listarUnHimnoPorTitulo($titulo){
            $query = "SELECT himnos.id_Himno, himnos.titulo, himnos.letra, himnos.enUso, medios.url, tipos.descripcion 
            FROM himnos INNER JOIN rel_himno ON himnos.id_Himno=rel_himno.id_Himno INNER JOIN medios ON rel_himno.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE himnos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = new Himno();
                $himno->id=$row["id_Himno"];
                $himno->titulo=$row["titulo"];
                $himno->letra=$row["letra"];
                $himno->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $himno->medios = $arrayMedios;
            $paraDevolver = json_encode($himno);
            return $paraDevolver;
        }

        public function actualizarHimno($nuevoTitulo, $nuevaLetra, $boolEnUso, $idHimno, $mediosAInsertar, $tiposAInsertar ) {
            // Hay que borrar las relaciones de la tabla de relaciones
            $database = new Database();
            $query = "DELETE FROM rel_himno WHERE id_Himno LIKE ".$idHimno.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

             if (is_array($resultadoMedios)) {
                // Tenemos array de ids
                // Actualizar
            $query = "UPDATE himnos SET titulo = '".$nuevoTitulo."', letra= '".$nuevaLetra."', enUso = ".$boolEnUso." WHERE id_Himno LIKE ".$idHimno.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Actualizar las relaciones 
                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_himno( id_Medio, id_Himno) VALUES (".$resultadoMedios[$i].",".$idHimno.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } // Salida del for

            } else {
                // Algo ha ido mal al insertar los medios 
                echo "Algo ha ido mal al actualizar los medios desde el endpoint historia";
            }

        }
        //endregion

        //region Frases
        public function insertarFrase ($texto, $fecha, $boolEnUso) {
            $database = new Database();
            $query = "INSERT INTO frase_inicio (id_Frase, texto, fecha, enUso) VALUES (null,'".$texto."','".$fecha."', ".$boolEnUso.");"; 
            // echo "La consulta para insertar un programa es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar el programa es ".$query;    
            $stmt->execute();
        }

        public function borrarFrase ($id){
            $database = new Database();
            $query = "DELETE FROM frase_inicio WHERE id_Frase like ".$id.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarFrases () {
            $database = new Database();
            $query = "SELECT * FROM frase_inicio;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = new Frase();
                $frase->id=$row["id_Frase"];
                $frase->texto=$row["texto"];
                $frase->fecha=$row["fecha"];
                $frase->enUso=$row["enUso"];
                array_push($arr, $frase);
            }
            return $arr;
        }  
        
        public function listarFrasesDTO () {
            $database = new Database();
            $query = "SELECT * FROM frase_inicio;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = new FraseDTO();
                $frase->id=$row["id_Frase"];
                $frase->texto=$row["texto"];
                array_push($arr, $frase);
            }
            return $arr;
        }  

        public function listarUnaFrasePorId ($id){
            $query = "SELECT * FROM frase_inicio WHERE id_Frase LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = new Frase();
                $frase->id=$row["id_Frase"];
                $frase->texto=$row["texto"];
                $frase->fecha=$row["fecha"];
                $frase->enUso=$row["enUso"];
                array_push($arr, $frase);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarUnaFrasePorIdyFecha($id, $fecha){
            $query = "SELECT * FROM frase_inicio WHERE id_Frase LIKE ".$id." AND fecha LIKE '".$fecha."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = new Frase();
                $frase->id=$row["id_Frase"];
                $frase->texto=$row["texto"];
                $frase->fecha=$row["fecha"];
                $frase->enUso=$row["enUso"];
                array_push($arr, $frase);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarUnaFrasePorFecha($fecha){
            $query = "SELECT * FROM frase_inicio WHERE fecha LIKE '".$fecha."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = new Frase();
                $frase->id=$row["id_Frase"];
                $frase->texto=$row["texto"];
                $frase->fecha=$row["fecha"];
                $frase->enUso=$row["enUso"];
                array_push($arr, $frase);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function actualizarFrase($nuevoTexto, $nuevaFecha, $boolEnUso, $idFrase) {
            $database = new Database();
            $query = "UPDATE frase_inicio SET texto = '".$nuevoTexto."',fecha = '".$nuevaFecha."', enUso=".$boolEnUso." WHERE id_Frase LIKE ".$idFrase.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }
        
        //endregion

        //region Ambientes
        public function insertarAmbiente($tituloAmbienteRecibido, $descripcionAmbienteRecibido, $boolEnUso) {
            $database = new Database();
            $query = "INSERT INTO ambiente (id_Ambiente, titulo, descripcion, enUso) VALUES (null,'".$tituloAmbienteRecibido."', '".$descripcionAmbienteRecibido."', ".$boolEnUso.");";
            // echo "La consulta para insertar un ambiente es ".$query;
            $stmt = $database->getConn()->prepare($query);
                        
            $stmt->execute();
        }

        public function borrarAmbiente($idRecibida) {
            $database = new Database();
            $query = "DELETE FROM ambiente WHERE id_Ambiente like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarAmbientes() {
            $database = new Database();
            $query = "SELECT * FROM ambiente;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new AmbienteDTO();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                // $ambiente->descripcion=$row["descripcion"];
                // $ambiente->enUso=$row["enUso"];
                array_push($arr, $ambiente);
            }
            return $arr;
        }

        public function ListarUnAmbientePorId($id) {
            $query = "SELECT ambiente.id_Ambiente, ambiente.titulo, ambiente.descripcion AS 'ambienteDesc', ambiente.enUso, medios.url, tipos.descripcion 
            FROM ambiente INNER JOIN rel_ambiente ON ambiente.id_Ambiente=rel_ambiente.id_Ambiente INNER JOIN medios ON rel_ambiente.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE ambiente.id_Ambiente LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new ambiente();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                $ambiente->descripcion=$row["ambienteDesc"];
                $ambiente->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $ambiente->medios = $arrayMedios;
            $paraDevolver = json_encode($ambiente);
            return $paraDevolver;
        }

        public function listarCadaAmbiente() {
            $database = new Database();
            $query = "SELECT * FROM ambiente INNER JOIN rel_ambiente ON ambiente.id_Ambiente = rel_ambiente.id_Ambiente LEFT JOIN medios ON medios.id_Medio = rel_ambiente.id_Medio";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new Ambiente();
                $ambiente->id=$row["id_Ambiente"];

                if ($ambiente->id != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT ambiente.id_Ambiente, ambiente.titulo, ambiente.descripcion as 'descr', ambiente.enUso, medios.url, tipos.descripcion 
                    FROM ambiente INNER JOIN rel_ambiente ON ambiente.id_Ambiente=rel_ambiente.id_Ambiente INNER JOIN medios ON rel_ambiente.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE ambiente.id_Ambiente LIKE ".$ambiente->id.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                    while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $ambiente = new Ambiente();
                        $ambiente->id=$row2["id_Ambiente"];
                        $ambiente->titulo=$row2["titulo"];
                        $ambiente->descripcion=$row2["descr"];
                        $ambiente->enUso=$row2["enUso"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                    $ambiente->medios = $arrayMedios;
                    array_push($arr, $ambiente);
                } else {
                    // No se lista
                }
                $idsYaListadas = $ambiente->id;
            }
            return $arr;
        }

        public function ListarUnAmbientePorIdyTitulo($id, $titulo) {
            $query = "SELECT ambiente.id_Ambiente, ambiente.titulo, ambiente.descripcion AS 'ambienteDesc', ambiente.enUso, medios.url, tipos.descripcion 
            FROM ambiente INNER JOIN rel_ambiente ON ambiente.id_Ambiente=rel_ambiente.id_Ambiente INNER JOIN medios ON rel_ambiente.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE ambiente.id_Ambiente LIKE ".$id." AND ambiente.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new ambiente();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                $ambiente->descripcion=$row["ambienteDesc"];
                $ambiente->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $ambiente->medios = $arrayMedios;
            $paraDevolver = json_encode($ambiente);
            return $paraDevolver;
        }

        public function ListarUnAmbientePorTitulo($titulo) {
            $query = "SELECT ambiente.id_Ambiente, ambiente.titulo, ambiente.descripcion AS 'ambienteDesc', ambiente.enUso, medios.url, tipos.descripcion 
            FROM ambiente INNER JOIN rel_ambiente ON ambiente.id_Ambiente=rel_ambiente.id_Ambiente INNER JOIN medios ON rel_ambiente.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE ambiente.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new ambiente();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                $ambiente->descripcion=$row["ambienteDesc"];
                $ambiente->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $ambiente->medios = $arrayMedios;
            $paraDevolver = json_encode($ambiente);
            return $paraDevolver;
        }
        
        public function actualizarAmbiente($nuevoTitulo, $boolEnUso, $idAmbiente, $mediosAInsertar, $tiposAInsertar) {
            $database = new Database();
            $query = "DELETE FROM rel_ambiente WHERE id_Ambiente LIKE ".$idAmbiente.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

            // Comprobamos el resultado
            if (is_array($resultadoMedios)) {
            // Tenemos array de ids
            // Actualizar   
            $query = "UPDATE ambiente SET titulo = '".$nuevoTitulo."', enUso = ".$boolEnUso." WHERE id_Ambiente LIKE ".$idAmbiente.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

            for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_ambiente( id_Medio, id_Ambiente) VALUES (".$resultadoMedios[$i].",".$idAmbiente.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } 
            }else{
                // Algo ha ido mal al insertar los medios
                echo "Algo ha ido mal al actualizar los medios desde el endpoint visita";
            }
           
        }            
           
           
        
        //endregion

        //region Oraciones
        public function insertarOracion($titulo, $texto, $boolEnUso) {

            $database = new Database();
            $query = "INSERT INTO oraciones (id_Oracion, titulo, texto, enUso) VALUES (null,'".$titulo."','".$texto."', ".$boolEnUso.");";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function borrarOracion($idRecibida){
            $database = new Database();
            $query = "DELETE FROM oraciones WHERE id_Oracion like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarOracion(){
            $database = new Database();
            $query = "SELECT * FROM oraciones;";
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
            return $arr;
        }

        public function listarOracionDTO(){
            $database = new Database();
            $query = "SELECT * FROM oraciones;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $pray = new PrayDTO();
                $pray->id=$row["id_Oracion"];
                $pray->titulo=$row["titulo"];
                // $pray->texto=$row["texto"];
                // $pray->enUso=$row["enUso"];
                array_push($arr, $pray);
            }
            return $arr;
        }

        public function listarUnaOracionPorId($id){
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


        public function listarUnaOracionPorIdyTitulo($id, $titulo){
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

        public function listarUnaOracionPorTitulo($titulo){
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

        public function actualizarOracion($nuevoTitulo, $nuevoTexto, $boolEnUso, $idOracion){
                        
            $database = new Database();
            $query = "UPDATE oraciones SET titulo = '".$nuevoTitulo."',texto = '".$nuevoTexto."', enUso = ".$boolEnUso." WHERE id_Oracion LIKE ".$idOracion.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }
        //endregion

        //region historias
        public function insertarHistoria($tituloHistoriaRecibido, $subtituloHistoriaRecibido, $descripcionRecibida, $boolEnUso) {
            $database = new Database();
            $query = "INSERT INTO historias (id_Historia, titulo, subtitulo, descripcion, enUso) VALUES (null,'".$tituloHistoriaRecibido."','".$subtituloHistoriaRecibido."', '".$descripcionRecibida."', ".$boolEnUso.");";
            // echo "La consulta para insertar una historia es ".$query;
            $stmt = $database->getConn()->prepare($query);
            // echo "La consulta para insertar la historia es ".$query;
            $stmt->execute();
        }

        public function borrarHistoria($idRecibida) {
            $database = new Database();
            $query = "DELETE FROM historias WHERE id_Historia like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarHistoria() {
            $database = new Database();
            $query = "SELECT * FROM historias;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new HistoriaDTO();
                $historia->idHistoria=$row["id_Historia"];
                $historia->titulo=$row["titulo"];
                // $historia->subtitulo=$row["subtitulo"];
                // $historia->descripcion=$row["descripcion"];
                // $historia->enUso=$row["enUso"];
                array_push($arr, $historia);
            }
            return $arr;
        }

        public function listarCadaHistoria() {
            $database = new Database();
            $query = "SELECT * FROM `historias` INNER JOIN rel_historia ON historias.id_Historia = rel_historia.id_Historia LEFT JOIN medios ON medios.id_Medio = rel_historia.id_Medio;";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new Historia();
                $historia->idHistoria=$row["id_Historia"];

                if ($historia->idHistoria != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT historias.id_Historia, historias.titulo as title, historias.subtitulo, historias.descripcion as 'descr', historias.enUso, medios.url, tipos.descripcion FROM historias INNER JOIN rel_historia ON historias.id_Historia=rel_historia.id_Historia INNER JOIN medios ON rel_historia.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE historias.id_Historia LIKE ".$historia->idHistoria.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                    while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $historia = new Historia();
                        $historia->idHistoria=$row2["id_Historia"];
                        $historia->titulo=$row2["title"];
                        $historia->subtitulo=$row2["subtitulo"];
                        $historia->descripcion=$row2["descr"];
                        $historia->enUso=$row2["enUso"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                    $historia->medios = $arrayMedios;
                    array_push($arr, $historia);
                } else {
                    // No se lista
                }
                $idsYaListadas = $historia->idHistoria;
            }
            return $arr;
        }



        public function listarUnaHistoriaPorId($id) {
            $query = "SELECT historias.id_Historia, historias.titulo, historias.subtitulo, historias.descripcion AS 'Desc_Historia', historias.enUso, medios.url, tipos.descripcion FROM historias INNER JOIN rel_historia ON historias.id_Historia=rel_historia.id_Historia INNER JOIN medios ON rel_historia.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE historias.id_Historia LIKE ".$id.";";

            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new Historia();
                $historia->idHistoria=$row["id_Historia"];
                $historia->titulo=$row["titulo"];
                $historia->subtitulo=$row["subtitulo"];
                $historia->descripcion=$row["Desc_Historia"];
                $historia->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $historia->medios = $arrayMedios;
            $paraDevolver = json_encode($historia);
            return $paraDevolver;
        }

        public function listarUnaHistoriaPorTitulo($titulo) {
            $query = "SELECT historias.id_Historia, historias.titulo, historias.subtitulo, historias.descripcion AS 'Desc_Historia', historias.enUso, medios.url, tipos.descripcion FROM historias INNER JOIN rel_historia ON historias.id_Historia=rel_historia.id_Historia INNER JOIN medios ON rel_historia.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE historias.titulo LIKE '".$titulo."';";


            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new Historia();
                $historia->idHistoria=$row["id_Historia"];
                $historia->titulo=$row["titulo"];
                $historia->subtitulo=$row["subtitulo"];
                $historia->descripcion=$row["Desc_Historia"];
                $historia->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $historia->medios = $arrayMedios;
            $paraDevolver = json_encode($historia);
            return $paraDevolver;
        }

        public function listarUnaHistoriaPorIdyTitulo($id, $titulo) {
            $query = "SELECT historias.id_Historia, historias.titulo, historias.subtitulo, historias.descripcion AS 'Desc_Historia', historias.enUso, medios.url, tipos.descripcion FROM historias INNER JOIN rel_historia ON historias.id_Historia=rel_historia.id_Historia INNER JOIN medios ON rel_historia.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo WHERE historias.titulo LIKE '".$titulo."' AND historias.id_Historia LIKE ".$id.";";

            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new Historia();
                $historia->idHistoria=$row["id_Historia"];
                $historia->titulo=$row["titulo"];
                $historia->subtitulo=$row["subtitulo"];
                $historia->descripcion=$row["Desc_Historia"];
                $historia->enUso=$row["enUso"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $historia->medios = $arrayMedios;
            $paraDevolver = json_encode($historia);
            return $paraDevolver;
        }

        public function actualizarHistoria($idHistoria, $nuevoTitulo, $nuevoSubtitulo, $nuevaDescripcion,$boolEnUso, $mediosAInsertar, $tiposAInsertar) {
            // Hay que borrar las relaciones de la tabla de relaciones 
            $database = new Database();
            $query = "DELETE FROM rel_historia WHERE id_Historia LIKE ".$idHistoria.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);
                   
            // Comprobamos el resultado
            if (is_array($resultadoMedios)) {
                // Tenemos array de ids
                // Actualizar
                $query = "UPDATE historias SET titulo = '".$nuevoTitulo."',subtitulo = '".$nuevoSubtitulo."',descripcion = '".$nuevaDescripcion."', enUso = ".$boolEnUso." WHERE id_Historia LIKE ".$idHistoria.";";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                // Actualizar las relaciones 
                for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_historia( id_Medio, id_Historia) VALUES (".$resultadoMedios[$i].",".$idHistoria.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } // Salida del for

            } else {
                // Algo ha ido mal al insertar los medios 
                echo "Algo ha ido mal al actualizar los medios desde el endpoint historia";
            }
        }
        //endregion

        //region visitas

        public function insertarVisita ($tituloVisita, $descripcion) {
            $database = new Database();
            $query = "INSERT INTO visitas (id_Visita, titulo, descripcion) VALUES (null,'".$tituloVisita."', '".$descripcion."');";
            // echo "La consulta para insertar un visita es ".$query;
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function borrarVisita ($idRecibida) {
            $database = new Database();
            $query = "DELETE FROM visitas WHERE id_Visita like ".$idRecibida.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            
        }

        public function listarVisita () {
            $database = new Database();
            $query = "SELECT * FROM visitas;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new VisitDTO();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                array_push($arr, $visita);
            }
            return $arr;
        }

        public function listarVisitaPorId ($id) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, visitas.descripcion AS 'descVisita',  medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.id_Visita LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                $visita->descripcion=$row["descVisita"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function listarCadaVisita() {
            $database = new Database();
            $query = "SELECT * FROM visitas INNER JOIN rel_visita ON visitas.id_Visita = rel_visita.id_Visita LEFT JOIN medios ON medios.id_Medio = rel_visita.id_Medio";
            $resultado = $database->getConn()->query($query);
            $arr = array();
            
            $idsYaListadas = -1;
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];

                if ($visita->id != $idsYaListadas) {
                    // Hace la segunda consulta
                    $query = "SELECT visitas.id_Visita, visitas.titulo, visitas.descripcion as 'descr', medios.url, tipos.descripcion 
                    FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE visitas.id_Visita LIKE ".$visita->id.";";

                    $database = new Database();
                    $resultado2 = $database->getConn()->query($query);
                    
                    $arrayMedios = array();
                    
                    while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                        $visita = new Visit();
                        $visita->id=$row2["id_Visita"];
                        $visita->titulo=$row2["titulo"];
                        $visita->descripcion=$row2["descr"];
                        array_push($arrayMedios, array("url"=> $row2["url"], "tipo"=> $row2["descripcion"]) );
                    }
                    $visita->medios = $arrayMedios;
                    array_push($arr, $visita);
                } else {
                    // No se lista
                }
                $idsYaListadas = $visita->id;
            }
            return $arr;
        }

        public function listarVisitaPorTitulo ($titulo) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, visitas.descripcion AS 'descVisita', medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                $visita->descripcion=$row["descVisita"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function listarVisitaPorIdyTitulo ($id, $titulo) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, visitas.descripcion AS 'descVisita', medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.id_Visita LIKE ".$id." AND visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                $visita->descripcion=$row["descVisita"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function actualizarVisita ($nuevoTitulo, $idVisita, $nuevaDescripcion, $mediosAInsertar, $tiposAInsertar) {
        	// Hay que borrar las relaciones de la tabla de relaciones
            $database = new Database();
            $query = "DELETE FROM rel_visita WHERE id_Visita LIKE ".$idVisita.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
           	$resultadoMedios = $ucf->insertarMedios($mediosAInsertar, $tiposAInsertar);

           	// Comprobamos el resultado
           	if (is_array($resultadoMedios)) {
           	// Tenemos array de ids
           	// Actualizar	
           	$query = "UPDATE visitas SET titulo = '".$nuevoTitulo."', descripcion = '".$nuevaDescripcion."' WHERE id_Visita LIKE ".$idVisita.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

            for ($i=0; $i < count($resultadoMedios, COUNT_NORMAL); $i++) { 
                    $query = "INSERT INTO rel_visita( id_Medio, id_Visita) VALUES (".$resultadoMedios[$i].",".$idVisita.");";
                    // echo "La consulta para insertar las relaciones es es ".$query;
                    $stmt = $database->getConn()->prepare($query);
                    $stmt->execute();
                } 
           	}else{
           		// Algo ha ido mal al insertar los medios
           		echo "Algo ha ido mal al actualizar los medios desde el endpoint visita";
           	}
           
        }
        //endregion
    
        //region Medios

         public function borrarMedio($id) {
            $database = new Database();
            $query = "DELETE FROM medios WHERE id_Medio like ".$id.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        public function listarMedio() {
            $database = new Database();
            $query = "SELECT * FROM medios;";
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio = new Medio();
                $medio->id=$row["id_Medio"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            return $arr;
        }

        public function listarMedioPorIdyURL($id, $url) {
            $query = "SELECT * FROM medios WHERE id_Medio LIKE ".$id." AND url LIKE '".$url."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio = new Medio();
                $medio->id=$row["id_Medio"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarMedioPorId($id) {
            $query = "SELECT * FROM medios WHERE id_Medio LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio = new Medio();
                $medio->id=$row["id_Medio"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarMedioPorURL($url) {
            $query = "SELECT * FROM medios WHERE url LIKE '".$url."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio = new Medio();
                $medio->id=$row["id_Medio"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function actualizarMedio($nuevaURL, $idMedio) {
            $database = new Database();
            $query = "UPDATE medios SET url = '".$nuevaURL."' WHERE id_Medio LIKE ".$idMedio.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }
        //endregion

    } // Salida de la clase
    
?>
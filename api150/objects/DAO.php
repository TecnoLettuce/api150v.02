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
            $query = "SELECT programas.id_Programa 
                    FROM programas  
                    WHERE programas.id_Programa LIKE ".$id." AND programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            $programa = new Programa();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa->id=$row["id_Programa"];
            }
            return $this->listarUnActoPorId($programa->id);
        }
        // Listar acto por id
        function listarUnActoPorId($id) {
            $query = "SELECT programas.id_Programa, programas.titulo, programas.descripcion AS 'descPrograma', programas.ubicacion, programas.fecha, programas.enUso, programas.id_Categoria 
                    FROM programas  
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

                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_programa ON rel_programa.id_Medio = medios.id_Medio
                WHERE rel_programa.id_Programa LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $programa->medios = $arrayMedios;
            $paraDevolver = $programa;
            return $paraDevolver;
            
        }
        // Listar acto por titulo
        function listarUnActoPorTitulo($titulo) {
            $query = "SELECT programas.id_Programa FROM programas WHERE programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            $programa = new Programa();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa->id=$row["id_Programa"];
            }
            return $this->listarUnActoPorId($programa->id);
        }

        public function listarCadaActo() {
            $database = new Database();
            $query = "SELECT id_Programa FROM programas";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $acto = $this->listarUnActoPorId($row["id_Programa"]);
                array_push($arr, $acto);
            }
            return $arr;
        }

        // Métodos para el endpoint de update Actos 
        function actualizarActo ($nuevoTitulo, $nuevaDescripcion, $nuevaUbicacion, $nuevaFecha, $boolEnUso, $idPrograma, $arrayMedios) {
            $database = new Database();
            $query = "DELETE FROM rel_programa WHERE id_Programa LIKE ".$idPrograma.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($arrayMedios);
                   
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
            $query = "SELECT saludos.id_Saludo, saludos.titulo, saludos.descripcion AS 'saludosDesc', saludos.texto, saludos.enUso 
            FROM saludos 
            WHERE saludos.id_Saludo LIKE ".$id.";";
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


                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_saludo ON rel_saludo.id_Medio = medios.id_Medio
                WHERE rel_saludo.id_Saludo LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $saludo->medios = $arrayMedios;
            $paraDevolver = $saludo;
            return $paraDevolver;
        }

        public function listarCadaSaludo() {
            $database = new Database();
            $query = "SELECT id_Saludo FROM saludos";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo = $this->listarUnSaludoPorId($row["id_Saludo"]);
                array_push($arr, $saludo);
            }
            return $arr;
        }

        function listarUnSaludoPorIdyTitulo($id, $titulo) {
            $query = "SELECT saludos.id_Saludo FROM saludos WHERE saludos.id_Saludo LIKE ".$id." AND saludos.titulo LIKE '".$titulo."'";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $saludo = new Saludo();
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo->id=$row["id_Saludo"];
            }
            return $this->listarUnSaludoPorId($saludo->id);
        }

        function listarUnSaludoPorTitulo($titulo) {
            $query = "SELECT saludos.id_Saludo FROM saludos WHERE saludos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $saludo = new Saludo();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $saludo->id=$row["id_Saludo"];
            }
            return $this->listarUnSaludoPorId($saludo->id);
        }

        // Métodos para el endpoint de update saludos
        function actualizarSaludo($nuevoTitulo, $nuevaDescripcion, $nuevoTexto, $boolEnUso, $idSaludo, $arrayMedios) {
             // Hay que borrar las relaciones de la tabla de relaciones 
            $database = new Database();
            $query = "DELETE FROM rel_saludo WHERE id_Saludo LIKE ".$idSaludo.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($arrayMedios);
                   
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
                echo "Algo ha ido mal al actualizar los medios desde el endpoint Saludo";
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
            $query = "SELECT himnos.id_Himno, himnos.titulo, himnos.letra, himnos.enUso 
            FROM himnos  
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

                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_himno ON rel_himno.id_Medio = medios.id_Medio
                WHERE rel_himno.id_Himno LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $himno->medios = $arrayMedios;
            $paraDevolver = $himno;
            return $paraDevolver;
        }

        public function listarCadaHimno() {
            $database = new Database();
            $query = "SELECT id_Himno FROM himnos";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno = $this->listarUnHimnoPorId($row["id_Himno"]);
                array_push($arr, $himno);
            }
            return $arr;
        }

        public function listarUnHimnoPorIdyTitulo($id, $titulo){
            $query = "SELECT himnos.id_Himno FROM himnos WHERE himnos.id_Himno LIKE ".$id." AND himnos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $himno = new Himno();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno->id=$row["id_Himno"];
            }
            
            return $this->listarUnHimnoPorId($himno->id);
        }

        public function listarUnHimnoPorTitulo($titulo){
            $query = "SELECT himnos.id_Himno
            FROM himnos 
            WHERE himnos.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $himno = new Himno();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $himno->id=$row["id_Himno"];
            }
            
            return $this->listarUnHimnoPorId($himno->id );
        }

        public function actualizarHimno($nuevoTitulo, $nuevaLetra, $boolEnUso, $idHimno, $arrayMedios) {
            // Hay que borrar las relaciones de la tabla de relaciones
            $database = new Database();
            $query = "DELETE FROM rel_himno WHERE id_Himno LIKE ".$idHimno.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($arrayMedios);

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

        public function listarCadaFrase () {
            $database = new Database();
            $query = "SELECT id_Frase FROM frase_inicio";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase = $this->listarUnaFrasePorId($row["id_Frase"]);
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
            $paraDevolver = $arr;
            return $paraDevolver;
        }

        public function listarUnaFrasePorIdyFecha($id, $fecha){
            $query = "SELECT id_Frase FROM frase_inicio WHERE id_Frase LIKE ".$id." AND fecha LIKE '".$fecha."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            $frase = new Frase();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase->id=$row["id_Frase"];
            }
          
            return $this->listarUnaFrasePorId($frase->id);
        }

        public function listarUnaFrasePorFecha($fecha){
            $query = "SELECT id_Frase FROM frase_inicio WHERE fecha LIKE '".$fecha."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $frase = new Frase();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $frase->id=$row["id_Frase"];
            }
            return $this->listarUnaFrasePorId($frase->id);
        }

        public function actualizarFrase($nuevoTexto, $nuevaFecha, $boolEnUso, $idFrase) {
            $database = new Database();
            $query = "UPDATE frase_inicio SET texto = '".$nuevoTexto."',fecha = '".$nuevaFecha."', enUso=".$boolEnUso." WHERE id_Frase LIKE ".$idFrase.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }
        
        //endregion

        //region Ambientes
        public function insertarAmbiente($tituloAmbienteRecibido, $descripcionAmbienteRecibido, $ubicacion, $fecha, $boolEnUso) {
            $database = new Database();
            $fecha = strtotime($fecha);
            $query = "INSERT INTO ambiente (id_Ambiente, titulo, descripcion, ubicacion, fecha, enUso) VALUES (null,'".$tituloAmbienteRecibido."', '".$descripcionAmbienteRecibido."', '".$ubicacion."' , ".$fecha." , ".$boolEnUso.");";
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
            $query = "SELECT ambiente.id_Ambiente, ambiente.titulo, ambiente.descripcion AS 'ambienteDesc', ambiente.ubicacion, ambiente.fecha, ambiente.enUso 
            FROM ambiente  
            WHERE ambiente.id_Ambiente LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = new ambiente();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                $ambiente->descripcion=$row["ambienteDesc"];
                $ambiente->ubicacion=$row["ubicacion"];
                $ambiente->fecha=$row["fecha"];
                $ambiente->enUso=$row["enUso"];
                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_ambiente ON rel_ambiente.id_Medio = medios.id_Medio
                WHERE rel_ambiente.id_Ambiente LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $ambiente->medios = $arrayMedios;
            $paraDevolver = $ambiente;
            return $paraDevolver;
        }

        public function listarCadaAmbiente() {
            $database = new Database();
            $query = "SELECT id_Ambiente FROM ambiente";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente = $this->ListarUnAmbientePorId($row["id_Ambiente"]);
                array_push($arr, $ambiente);
            }
            return $arr;
        }

        public function ListarUnAmbientePorIdyTitulo($id, $titulo) {
            $query = "SELECT ambiente.id_Ambiente 
            FROM ambiente  
            WHERE ambiente.id_Ambiente LIKE ".$id." AND ambiente.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $ambiente = new ambiente();
            

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente->id=$row["id_Ambiente"];
            }
            
            return $this->ListarUnAmbientePorId($ambiente->id);
        }

        public function ListarUnAmbientePorTitulo($titulo) {
            $query = "SELECT ambiente.id_Ambiente 
            FROM ambiente 
            WHERE ambiente.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $ambiente = new ambiente();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $ambiente->id = $row["id_Ambiente"];
            }

            return $this->ListarUnAmbientePorId($ambiente->id);
        }
        
        public function actualizarAmbiente($nuevoTitulo, $descripcion, $ubicacion, $fecha, $boolEnUso, $idAmbiente, $arrayMedios) {
            $database = new Database();
            $query = "DELETE FROM rel_ambiente WHERE id_Ambiente LIKE ".$idAmbiente.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($arrayMedios);

            // Comprobamos el resultado
            if (is_array($resultadoMedios)) {
            // Tenemos array de ids
            // Actualizar
            
            // Se cambia la fecha a timestamp
            $date = new DateTime($fecha);
            $fecha = $date->getTimestamp();
            
            $query = "UPDATE ambiente SET titulo = '".$nuevoTitulo."', descripcion= '".$descripcion."', ubicacion = '".$ubicacion."', fecha = ".$fecha.", enUso = ".$boolEnUso." WHERE id_Ambiente LIKE ".$idAmbiente.";";
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

        public function listarCadaOracion(){
            $database = new Database();
            $query = "SELECT id_Oracion FROM oraciones";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $oracion = $this->listarUnaOracionPorId($row["id_Oracion"]);
                array_push($arr, $oracion);
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
            $paraDevolver = $arr;
            return $paraDevolver;

        }


        public function listarUnaOracionPorIdyTitulo($id, $titulo){
            $query = "SELECT id_Oracion FROM oraciones WHERE id_Oracion LIKE ".$id." AND titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $pray = new Pray();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $pray->id=$row["id_Oracion"];
            }
            return $this->listarUnaOracionPorId($pray->id);

        }

        public function listarUnaOracionPorTitulo($titulo){
            $query = "SELECT id_Oracion FROM oraciones WHERE titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $pray = new Pray();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $pray->id = $row["id_Oracion"];
            }
            
            return $this->listarUnaOracionPorId($pray->id);

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
            $query = "SELECT id_Historia FROM historias";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = $this->listarUnaHistoriaPorId($row["id_Historia"]);
                array_push($arr, $historia);
            }
            return $arr;
        }


        public function listarUnaHistoriaPorId($id) {
            $query = "SELECT historias.id_Historia, historias.titulo, historias.subtitulo, historias.descripcion AS 'Desc_Historia', historias.enUso
            FROM historias 
            WHERE historias.id_Historia LIKE ".$id.";";

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

                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_historia ON rel_historia.id_Medio = medios.id_Medio
                WHERE rel_historia.id_Historia LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $historia->medios = $arrayMedios;
            $paraDevolver = $historia;
            return $paraDevolver;
        }

        public function listarUnaHistoriaPorTitulo($titulo) {
            $query = "SELECT historias.id_Historia FROM historias WHERE historias.titulo LIKE '".$titulo."';";

            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $historia = new Historia();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia->idHistoria=$row["id_Historia"];
            }
            return $this->listarUnaHistoriaPorId($historia->idHistoria);
        }

        public function listarUnaHistoriaPorIdyTitulo($id, $titulo) {
            $query = "SELECT historias.id_Historia FROM historias WHERE historias.titulo LIKE '".$titulo."' AND historias.id_Historia LIKE ".$id.";";

            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $historia = new Historia();
                $historia->idHistoria = $row["id_Historia"];
            }
            return $this->listarUnaHistoriaPorId($historia->idHistoria);
        }

        public function actualizarHistoria($idHistoria, $nuevoTitulo, $nuevoSubtitulo, $nuevaDescripcion,$boolEnUso, $mediosAInsertar) {
            // Hay que borrar las relaciones de la tabla de relaciones 
            $database = new Database();
            $query = "DELETE FROM rel_historia WHERE id_Historia LIKE ".$idHistoria.";"; 
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
            $resultadoMedios = $ucf->insertarMedios($mediosAInsertar);
                   
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
            $query = "SELECT visitas.id_Visita, visitas.titulo, visitas.descripcion AS 'descVisita' 
            FROM visitas  
            WHERE visitas.id_Visita LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                $visita->descripcion=$row["descVisita"];
                
                // Consulta para sacar los medios
                $query2 = "SELECT medios.nombre, medios.url, tipos.descripcion
                FROM medios INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo INNER JOIN rel_visita ON rel_visita.id_Medio = medios.id_Medio
                WHERE rel_visita.id_Visita LIKE ".$id.";";
                $database2 = new Database();
                $resultado2 = $database2->getConn()->query($query2);
                
                while ($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayMedios, array("nombre"=> $row2["nombre"], "url"=> $row2["url"], "tipo"=> $row2["descripcion"]));
                }
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = $visita;
            return $paraDevolver;
        }

        public function listarCadaVisita() {
            $database = new Database();
            $query = "SELECT id_Visita FROM visitas";
            $resultado = $database->getConn()->query($query);
            $arr = array();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = $this->listarVisitaPorId($row["id_Visita"]);
                array_push($arr, $visita);
            }
            return $arr;
        }

        public function listarVisitaPorTitulo ($titulo) {
            $query = "SELECT visitas.id_Visita 
            FROM visitas  
            WHERE visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id = $row["id_Visita"];
            }
            return $this->listarVisitaPorId($visita->id);
        }

        public function listarVisitaPorIdyTitulo ($id, $titulo) {
            $query = "SELECT visitas.id_Visita
            FROM visitas 
            WHERE visitas.id_Visita LIKE ".$id." AND visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $visita = new Visit();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita->id=$row["id_Visita"];
            }
            
            return $this->listarVisitaPorId($visita->id);
        }

        public function actualizarVisita ($nuevoTitulo, $idVisita, $nuevaDescripcion, $arrayMedios) {
        	// Hay que borrar las relaciones de la tabla de relaciones
            $database = new Database();
            $query = "DELETE FROM rel_visita WHERE id_Visita LIKE ".$idVisita.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
            // Insertamos los medios
            $ucf = new UploadCommonFunctions();
           	$resultadoMedios = $ucf->insertarMedios($arrayMedios);

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
                $medio->nombre=$row["nombre"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            return $arr;
        }

        public function listarMedioPorIdyURL($id, $url) {
            $query = "SELECT id_Medio FROM medios WHERE id_Medio LIKE ".$id." AND url LIKE '".$url."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            $medio = new Medio();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio->id=$row["id_Medio"];
            }
            
            return $this->listarMedioPorId($medio->id);
        }

        public function listarMedioPorId($id) {
            $query = "SELECT * FROM medios WHERE id_Medio LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio = new Medio();
                $medio->id=$row["id_Medio"];
                $medio->nombre=$row["nombre"];
                $medio->url=$row["url"];
                $medio->tipo=$row["id_Tipo"];
                array_push($arr, $medio);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarMedioPorURL($url) {
            $query = "SELECT id_Medio FROM medios WHERE url LIKE '".$url."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $medio = new Medio();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $medio->id=$row["id_Medio"];
            }
            return $this->listarMedioPorId($medio->id);
        }

        public function actualizarMedio($nombre, $nuevaURL, $idMedio) {
            $database = new Database();
            $query = "UPDATE medios SET nombre = '".$nombre."' ,url = '".$nuevaURL."' WHERE id_Medio LIKE ".$idMedio.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }
        //endregion


        //region CRUD de usuarios
        
        public function listarUsuarios() {
            $database = new Database();
            $query = "SELECT idUser, userName, password, mail, rolName FROM user INNER JOIN user_rol ON user_rol.idRol = user.idRol";
            $resultado = $database->getConn()->query($query);

            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $usuario = new Usuario();
                $usuario->id=$row["idUser"];
                $usuario->username=$row["userName"];
                $usuario->password=$row["password"];
                $usuario->mail=$row["mail"];
                $usuario->rolName=$row["rolName"];
                array_push($arr, $usuario);
            }
            $paraDevolver = json_encode($arr);
            return $paraDevolver;
        }

        public function listarUsuarioPorId($id) {
            $query = "SELECT idUser, userName, password, mail, rolName 
            FROM user INNER JOIN user_rol ON user_rol.idRol = user.idRol
            WHERE idUser LIKE ".$id."";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arr = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $usuario = new Usuario();
                $usuario->id=$row["idUser"];
                $usuario->username=$row["userName"];
                $usuario->password=$row["password"];
                $usuario->mail=$row["mail"];
                $usuario->rolName=$row["rolName"];
                array_push($arr, $usuario);
            }
            $paraDevolver = $arr;
            return $paraDevolver;
        }

        public function listarUsuarioPorNombre($userName) {

            $query = "SELECT idUser FROM user WHERE userName LIKE '".$userName."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $usuario = new Usuario();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $usuario->id=$row["idUser"];
            }
            return $this->listarUsuarioPorId($usuario->id);
        }

        public function listarUsuarioPorIdYNombre($id, $userName) {

            $query = "SELECT idUser FROM user 
            WHERE userName LIKE '".$userName."' AND idUser LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $usuario = new Usuario();

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $usuario->id=$row["idUser"];
            }
            return $this->listarUsuarioPorId($usuario->id);
        }

        public function borrarUsuario($idUser){
            $database  = new Database();
            $query = "DELETE FROM user WHERE idUser like ".$idUser.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
        }

        function actualizarUsuario($idUser, $userName, $password, $mail, $rolName) {

            // buscar el rol que se le quiere asignar 

            $query = "SELECT idRol 
            FROM user_rol 
            WHERE rolName LIKE '".$rolName."'";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $idRol = -1;

            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {    
                $idRol=$row["idRol"];
            }

            // Encriptar la contraseña
            $passwordEncriptada = sha1($password, $raw_output = false);

           $query = "UPDATE user SET userName = '".$userName."', password = '".$passwordEncriptada."', mail = '".$mail."', idRol = ".$idRol." WHERE idUser LIKE ".$idUser.";";
           $stmt = $database->getConn()->prepare($query);
           $stmt->execute();
       }

        //endregion

        //region
        function inicializarBaseDatos() {
            // comprueba que la base de datos tenga los elementos minimos para funcionar

            // Comprobamos las categorías
            $query = "SELECT * 
            FROM categoria";
            $database = new Database();
            $resultado = $database->getConn()->query($query);

            $descripcionExtraida = "";
            $bool1 = -1;
            $bool2 = -1;
            $bool3 = -1;
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {    
                $descripcionExtraida = $row["descripcion"];

                if (strcmp($descripcionExtraida, "Salesianas") == 0) {
                    $bool1 = 0;
                } else if (strcmp($descripcionExtraida, "Inspectoria") == 0) {
                    $bool2 = 0;
                } else if (strcmp($descripcionExtraida, "Eventos") == 0) {
                    $bool3 = 0;
                }    
            }

            if ($bool1 == 0 && $bool2 == 0 && $bool3 == 0) {
                // Todos los elementos necesarios en las categorias están (No se hace nada)
            } else {
                // insertamos lo necesario
                $database = new Database();
                $query = "INSERT INTO categoria (id_Categoria, descripcion) VALUES (null, 'Salesianas');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO categoria (id_Categoria, descripcion) VALUES (null, 'Inspectoria');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO categoria (id_Categoria, descripcion) VALUES (null, 'Eventos');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

            }

            
            // Comprobamos los tipos de los medios
            $query = "SELECT * 
            FROM tipos";
            $database = new Database();
            $resultado = $database->getConn()->query($query);

            $descripcionExtraida = "";
            $bool1 = -1;
            $bool2 = -1;
            $bool3 = -1;
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {    
                $descripcionExtraida = $row["descripcion"];

                if (strcmp($descripcionExtraida, "image/jpg") == 0) {
                    $bool1 = 0;
                } else if (strcmp($descripcionExtraida, "video/mp4") == 0) {
                    $bool2 = 0;
                } else if (strcmp($descripcionExtraida, "audio/mp3") == 0) {
                    $bool3 = 0;
                }    
            }

            if ($bool1 == 0 && $bool2 == 0 && $bool3 == 0) {
                // Todos los elementos necesarios en los tipos están (No se hace nada)
            } else {
                // insertamos lo necesario
                $database = new Database();
                $query = "INSERT INTO tipos (id_Tipo, descripcion) VALUES (null, 'image/jpg');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO tipos (id_Tipo, descripcion) VALUES (null, 'video/mp4');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO tipos (id_Tipo, descripcion) VALUES (null, 'audio/mp3');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

            }


            // Comprobamos los usuarios minimos para funcionar
            $query = "SELECT * 
            FROM user";
            $database = new Database();
            $resultado = $database->getConn()->query($query);

            $usuarioExtraido = "";
            $bool1 = -1;
            $bool2 = -1;
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {    
                $usuarioExtraido = $row["userName"];

                if (strcmp($usuarioExtraido, "admin") == 0) {
                    $bool1 = 0;
                } else if (strcmp($usuarioExtraido, "editor") == 0) {
                    $bool2 = 0;
                } 
            }

            if ($bool1 == 0 && $bool2 == 0) {
                // Todos los elementos necesarios en los usuarios están (No se hace nada)
            } else {
                // insertamos lo necesario
                $database = new Database();
                $query = "INSERT INTO user (idUser, userName, password, mail, idRol) VALUES (null, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', '', 0);";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO user (idUser, userName, password, mail, idRol) VALUES (null, 'editor', 'ab41949825606da179db7c89ddcedcc167b64847', '', 0);";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
            }

            // Comprobamos los roles minimos para funcionar
            $query = "SELECT * 
            FROM user_rol";
            $database = new Database();
            $resultado = $database->getConn()->query($query);

            $rolExtraido = "";
            $bool1 = -1;
            $bool2 = -1;
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {    
                $rolExtraido = $row["rolName"];

                if (strcmp($rolExtraido, "admin") == 0) {
                    $bool1 = 0;
                } else if (strcmp($rolExtraido, "editor") == 0) {
                    $bool2 = 0;
                } 
            }

            if ($bool1 == 0 && $bool2 == 0) {
                // Todos los elementos necesarios en los roles están (No se hace nada)
            } else {
                // insertamos lo necesario
                $database = new Database();
                $query = "INSERT INTO user_rol (idRol, rolName) VALUES (null, 'admin');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();

                $query = "INSERT INTO user_rol (idRol, rolName) VALUES (null, 'editor');";
                $stmt = $database->getConn()->prepare($query);
                $stmt->execute();
            }
        }
        //endregion

    } // Salida de la clase
    
?>
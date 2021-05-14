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
        }

        //region Actos
        // Métodos para el endpoint de insertar actos 
        function insertarActo ($titulo, $fecha, $boolEnUso, $categoria) {
            $database = new Database();
            $query = "INSERT INTO programas (id_Programa, titulo, fecha, enUso, id_Categoria) VALUES (null,'".$titulo."','".$fecha."',".$boolEnUso.", '".$categoria."');";
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
            $query = "SELECT programas.id_Programa, programas.titulo, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.id_Programa LIKE ".$id." AND programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
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
            $query = "SELECT programas.id_Programa, programas.titulo, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.id_Programa LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
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
            $query = "SELECT programas.id_Programa, programas.titulo, programas.fecha, programas.enUso, programas.id_Categoria, medios.url, tipos.descripcion 
                    FROM programas INNER JOIN rel_programa ON programas.id_Programa=rel_programa.id_Programa INNER JOIN medios ON rel_programa.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
                    WHERE programas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $programa = new Programa();
                $programa->id=$row["id_Programa"];
                $programa->titulo=$row["titulo"];
                $programa->fecha=$row["fecha"];
                $programa->enUso=$row["enUso"];
                $programa->categoria=$row["id_Categoria"];
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $programa->medios = $arrayMedios;
            $paraDevolver = json_encode($programa);
            return $paraDevolver;
        }

        // Métodos para el endpoint de update Actos 
        function actualizarActo ($nuevoTitulo, $nuevaFecha, $boolEnUso, $idPrograma) {
            $database = new Database();
            $query = "UPDATE programas SET titulo = '".$nuevoTitulo."', fecha = '".$nuevaFecha."', enUso = ".$boolEnUso." WHERE id_Programa LIKE ".$idPrograma.";";
            echo "consulta > ".$query;
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
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
                $saludo = new Saludo();
                $saludo->id=$row["id_Saludo"];
                $saludo->titulo=$row["titulo"];
                $saludo->descripcion=$row["descripcion"];
                $saludo->texto=$row["texto"];
                $saludo->enUso=$row["enUso"];
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
        function actualizarSaludo($nuevoTitulo, $nuevaDescripcion, $nuevoTexto, $boolEnUso, $idSaludo) {
            $database = new Database();
            $query = "UPDATE saludos SET titulo = '".$nuevoTitulo."', descripcion = '".$nuevaDescripcion."', texto = '".$nuevoTexto."', enUso = ".$boolEnUso." WHERE id_Saludo LIKE ".$idSaludo.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();
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
                $himno = new Himno();
                $himno->id=$row["id_Himno"];
                $himno->titulo=$row["titulo"];
                $himno->letra=$row["letra"];
                $himno->enUso=$row["enUso"];
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

        public function actualizarHimno($nuevoTitulo, $nuevaLetra, $boolEnUso, $idHimno) {
            $database = new Database();
            $query = "UPDATE himnos SET titulo = '".$nuevoTitulo."', letra= '".$nuevaLetra."', enUso = ".$boolEnUso." WHERE id_Himno LIKE ".$idHimno.";";
            $stmt = $database->getConn()->prepare($query);
            $stmt->execute();

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
                $ambiente = new Ambiente();
                $ambiente->id=$row["id_Ambiente"];
                $ambiente->titulo=$row["titulo"];
                $ambiente->descripcion=$row["descripcion"];
                $ambiente->enUso=$row["enUso"];
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
                $historia = new Historia();
                $historia->idHistoria=$row["id_Historia"];
                $historia->titulo=$row["titulo"];
                $historia->subtitulo=$row["subtitulo"];
                $historia->descripcion=$row["descripcion"];
                $historia->enUso=$row["enUso"];
                array_push($arr, $historia);
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

        public function insertarVisita ($tituloVisita) {
            $database = new Database();
            $query = "INSERT INTO visitas (id_Visita, titulo) VALUES (null,'".$tituloVisita."');";
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
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                array_push($arr, $visita);
            }
            return $arr;
        }

        public function listarVisitaPorId ($id) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.id_Visita LIKE ".$id.";";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function listarVisitaPorTitulo ($titulo) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function listarVisitaPorIdyTitulo ($id, $titulo) {
            $query = "SELECT visitas.id_Visita, visitas.titulo, medios.url, tipos.descripcion 
            FROM visitas INNER JOIN rel_visita ON visitas.id_Visita=rel_visita.id_Visita INNER JOIN medios ON rel_visita.id_Medio=medios.id_Medio INNER JOIN tipos ON medios.id_Tipo = tipos.id_Tipo 
            WHERE visitas.id_Visita LIKE ".$id." AND visitas.titulo LIKE '".$titulo."';";
            $database = new Database();
            $resultado = $database->getConn()->query($query);
            
            $arrayMedios = array();
            
            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                $visita = new Visit();
                $visita->id=$row["id_Visita"];
                $visita->titulo=$row["titulo"];
                
                array_push($arrayMedios, array("url"=> $row["url"], "tipo"=> $row["descripcion"]) );
            }
            $visita->medios = $arrayMedios;
            $paraDevolver = json_encode($visita);
            return $paraDevolver;
        }

        public function actualizarVisita ($nuevoTitulo, $idVisita, $mediosAInsertar, $tiposAInsertar) {
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
           	$query = "UPDATE visitas SET titulo = '".$nuevoTitulo."' WHERE id_Visita LIKE ".$idVisita.";";
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
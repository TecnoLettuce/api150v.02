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

    $data = json_decode(file_get_contents("php://input"));

    // $token = $data->token;

    include_once '../util/logger.php';
    include_once '../util/commonFunctions.php';
    include_once '../util/uploadFilesByURL.php';
    include_once '../objects/DAO.php';
    include_once 'mockDatabase.php';
    include_once '../config/database.php';

    include_once '../util/act.php';
    include_once '../util/ambiente.php';
    include_once '../util/greetings.php';
    include_once '../util/historia.php';
    include_once '../util/hymn.php';
    include_once '../util/material.php';
    include_once '../util/phrase.php';
    include_once '../util/pray.php';
    include_once '../util/user.php';
    include_once '../util/visit.php';

    // $database = new Database();
    $testDB = new TestDatabase();
    // $cf = new CommonFunctions();
    $dao = new Dao();

    
    // Aqui se realizan todas las operaciones en una base de datos limpia 
    // Se crea una instancia de cada objeto y se testea que los objetos recibidos sean los esperados 


    //region
    $acto = new Programa();
    $acto->id = 1;
    $acto->titulo = "testActo";
    $acto->descripcion = "testActo";
    $acto->ubicacion = "Somewhere";
    $acto->fecha = "10-10-2021";
    $acto->enUso = 1;
    $acto->categoria = 1;

    $dao->insertarActo($acto->titulo, $acto->descripcion, $acto->ubicacion, $acto->fecha, $acto->enUso, $acto->categoria);
    $resultado = $dao->listarActos();
    
    $actoDevuelto = new Programa();

    while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $actoDevuelto->id=$row["id_Programa"];
        $actoDevuelto->titulo=$row["titulo"];
        $actoDevuelto->descripcion=$row["descripcion"];
        $actoDevuelto->ubicacion=$row["ubicacion"];
        $actoDevuelto->fecha=$row["fecha"];
        $actoDevuelto->enUso=$row["enUso"];
        $actoDevuelto->categoria=$row["id_Categoria"];
    }

    echo $actoDevuelto->id;
    echo $acto->id;
    if ($acto->id == $actoDevuelto->id) {
        echo "test Pasado";
    } else {
        echo "test Fallido";
    }


    //endregion

    $ambiente = new Ambiente();
    $ambiente->id = 1;
    $ambiente->titulo = "testAmbiente";
    $ambiente->descripcion = "testAmbiente";
    $ambiente->ubicacion = "testAmbiente";
    $ambiente->fecha = "10-10-2021";
    $ambiente->enUso = 1;

    $saludo = new Saludo();
    $saludo->id = 1;
    $saludo->titulo = "testSaludo";
    $saludo->descripcion = "testSaludo";
    $saludo->texto = "testSaludo";
    $saludo->enUso = 1;

    $historia = new Historia();
    $historia->idHistoria = 1;
    $historia->titulo = "testHistoria";
    $historia->subtitulo = "testHistoria";
    $historia->descripcion = "testHistoria";
    $historia->enUso = 1;

    $himno = new Himno();
    $himno->id = 1;
    $himno->titulo = "testHimno";
    $himno->letra = "testHimno";
    $himno->enUso = 1;

    $medio = new Medio();
    $medio->id = 1;
    $medio->nombre = "testMedio";
    $medio->url = "testMedio";
    $medio->tipo = 1;

    $frase = new Frase();
    $frase->id = 1;
    $frase->texto = "testFrase";
    $frase->fecha = "10-10-2021";
    $frase->enUso = 1;

    $oracion = new Pray();
    $oracion->id = 1;
    $oracion->titulo = "testOracion";
    $oracion->texto = "testOracion";
    $oracion->enUso = 1;

    $user = new Usuario();
    $user->id = 1;
    $user->username = "testUser";
    $user->password = "testUser";
    $user->mail = "testUser";
    $user->rolName = 1;

    $visita = new Visit();
    $visita->id = 1;
    $visita->titulo = "testVisita";
    $visita->descripcion = "testVisita";


    /* 
        TODO LIST 
            
            Crear acto
            listar actos 
            modificar acto 
            listar un acto 
            eliminar acto
            
            Crear saludo
            listar saludos 
            modificar saludo 
            listar un saludo 
            eliminar saludo
            
            Crear himno
            listar himno 
            modificar himno 
            listar un himno 
            eliminar himno
            
            Crear medio
            listar medios 
            modificar medio 
            listar un medios 
            eliminar medio
            
            Crear frase
            listar frases 
            modificar frase 
            listar un frase 
            eliminar frase 

            Crear ambiente 
            listar ambientes 
            modificar ambiente 
            listar un ambiente 
            eliminar ambiente
            
            Crear oracion
            listar oracion 
            modificar oracion 
            listar un oracion 
            eliminar oracion
            
            Crear historia
            listar historia 
            modificar historia 
            listar un historia 
            eliminar historia

            Crear visita
            listar visita 
            modificar visita 
            listar un visita 
            eliminar visita 
    */

    //region



    //endregion


?>
<?php
require_once('db_config.php');


/** Create 'files_solicitudes' directory: */
$cd = getcwd();
if (!file_exists($cd . "/files_solicitudes")){
    mkdir($cd . "/files_solicitudes", "0777");
}


/**NON file data: */
$nombreSol = strip_tags($_POST["nombre-solicitante"]);
$regionSol = strip_tags($_POST["region-solicitante"]);
$comunaSol = strip_tags($_POST["comuna-solicitante"]);
$celSol = strip_tags($_POST["celular-solicitante"]);
$mailSol = strip_tags($_POST["email-solicitante"]);
$twitSol = strip_tags($_POST["twitter-solicitante"]);
$sinSol = strip_tags($_POST["sintomas-solicitante"]);
$espSol = strip_tags($_POST["especialidad-solicitante"]);

/**Upload adicional files: */
/**Create dir for files: */
$nameDir = str_replace(' ', '', $nombreSol);
$cd = getcwd();
if (!file_exists($cd . "/files_solicitudes//". $nameDir)){
    mkdir($cd . "/files_solicitudes//". $nameDir, "0777");
}
//One directory for each person
$target_dir =  "files_solicitudes/". $nameDir. "/";

//$allowed_file_extension = array("jpg","png","jpeg", "gif");
$files_array = array();
$msg = array();
$uploadOkArray = array();
$fileTypeArray = array();

foreach($_FILES as $file){
    if ($file["name"] == NULL){
    break;
    }
    $target_file = $target_dir . basename($file["name"]);

    $fileTypeArray[] = $file["type"];

    array_push($files_array,basename($file["name"]));

    $uploadOk = 1;

    if(isset($_POST["submitSol"])){
        $check = filesize($file["tmp_name"]);
        if($check == false){
            $msg[] =  "Archivo ". basename( $file["name"]). " no es una imagen.";
            $uploadOk = 0;
        }else{
            $uploadOk = 1;
        }
    }
    if(file_exists($target_file)){
        $msg[] = "Archivo ". basename( $file["name"]). " ya existe";
        $uploadOk = 0;
    }
    if ($file["size"] > 25000000) {
        $msg[] = "Archivo ". basename( $file["name"]). " excede tamano permitido (25 [mb]).";
        $uploadOk = 0;
    }

    if(!$uploadOk){
        $msg[] = "Archivo ". basename( $file["name"]). " no fue subida.";
        $uploadOkArray[] = 0;
    } else{
        if (move_uploaded_file($file["tmp_name"], $target_file)){
            $msg[] = "Archivo ". basename( $file["name"]). " subido correctamente.";
            $uploadOkArray[] = 1;
        } else{
            $msg[] = "Archivo ". basename( $file["name"]). " no fue subido.";
            $uploadOkArray[] = 0;
        }
    }
}


$db = DbConfig::getConnection();
if(isset($_POST["submitSol"])){
    //insertSol(args);
    insertSol($db, $nombreSol, $comunaSol, $celSol, $mailSol, $twitSol, $sinSol, $espSol,$msg, $files_array, $uploadOkArray, $target_dir, $fileTypeArray);
}
$db->close();

function insertSol($db, $nombreSol, $comunaSol, $celSol, $mailSol, $twitSol, $sinSol, $espSol, $msg, $files_array, $okArray, $target_dir, $fileTypeArray){

    $find_especialidad = "SELECT id FROM especialidad WHERE descripcion LIKE '$espSol'";
    $result = $db->query($find_especialidad);
    $id_especialidad = mysqli_fetch_array($result)["id"];

    $find_comuna = "SELECT id FROM comuna WHERE nombre LIKE '$comunaSol'";
    $resultado = $db->query($find_comuna);
    $id_comuna = mysqli_fetch_array($resultado)["id"];

    //INSERT INTO solicitud_atencion (nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id) VALUES (?, ?, ?, ?, ?, ?, ?)
    $insertSolData = $db->prepare("INSERT INTO solicitud_atencion (nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id) 
    VALUES ('$nombreSol', '$id_especialidad', '$sinSol', '$twitSol', '$mailSol','$celSol', '$id_comuna')");

    $insertSolData->execute();

    $id_solicitante = $db->insert_id;

    foreach($files_array as $key => $value){
        //Insertamos solo si se cargo en el directorio media. 
        if($okArray[$key]){
            $insertSolFile=$db->prepare("INSERT INTO archivo_solicitud (ruta_archivo,nombre_archivo, mimetype, solicitud_atencion_id)
             VALUES ('$target_dir','$value', '$fileTypeArray[$key]', '$id_solicitante')");
		    $insertSolFile->execute();
        }
    }
    
    echo "<h1>Registro de solicitud completado.</h1><h2>Redireccionando a pagina inicial.</h2>";
    

    foreach($msg as $key => $value){
        echo $value, '<br>';
    }

    echo "<td><button onclick = \"location.href = 'Inicio.html';\">Volver a pagina inicial</button></td>";
}


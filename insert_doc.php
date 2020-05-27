<?php
require_once('db_config.php');


/** Create 'media' directory: */
$cd = getcwd();
if (!file_exists($cd . "/media")){
    mkdir($cd . "/media", "0777");
}

/**NON file data: */
$nombreMedico = strip_tags($_POST["nombre-medico"]);
$regionMedico = strip_tags($_POST["region-medico"]);
$comunaMedico = strip_tags($_POST["comuna-medico"]);
$celMedico = strip_tags($_POST["celular-medico"]);
$mailMedico = strip_tags($_POST["email-medico"]);
$twitMedico = strip_tags($_POST["twitter-medico"]);
$expMedico = strip_tags($_POST["experiencia-medico"]);

/**Create array with 'especialidades' */
$especialidadesMedico = array();
array_push($especialidadesMedico, $_POST["especialidades-medico"],$_POST["especialidades-medico-2"],
$_POST["especialidades-medico-3"],$_POST["especialidades-medico-4"],$_POST["especialidades-medico-5"]);
/**Delete the 'especialidades' left blanc */
$especialidadesMedico = \array_diff($especialidadesMedico,["sin-especialidad"]);

/**Get files: */
$nameDir = str_replace(' ', '', $nombreMedico);
$cd = getcwd();
if (!file_exists($cd . "/media//". $nameDir)){
    mkdir($cd . "/media//". $nameDir, "0777");
}
//One directory for each doc
$target_dir =  "media/". $nameDir. "/";

$allowed_image_extension = array("jpg","png","jpeg", "gif");

$fotos = array();
$msg = array();
$uploadOkArray = array();
foreach($_FILES as $file){
    if ($file["name"] == NULL){
    break;
    }
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    array_push($fotos,basename($file["name"]));
    $uploadOk = 1;
    if(isset($_POST["submitDoc"])){
        $check = getimagesize($file["tmp_name"]);
        if($check == false){
            $msg[] =  "Archivo ". basename( $file["name"]). " no es una imagen.";
            $uploadOk = 0;
        }else{
            $uploadOk = 1;
        }
    }
    if(file_exists($target_file)){
        $msg[] = "Imagen ". basename( $file["name"]). " ya existe";
        $uploadOk = 0;
    }
    if (!in_array($imageFileType,$allowed_image_extension)) {
        $msg[] = "Formato de la imagen ". basename( $file["name"]). " no permitido.";
        $uploadOk = 0;
    }
    if ($file["size"] > 5000000) {
        $msg[] = "Imagen ". basename( $file["name"]). " excede tamano permitido (5 [mb]).";
        $uploadOk = 0;
    }

    if(!$uploadOk){
        $msg[] = "Imagen ". basename( $file["name"]). " no fue subida.";
        $uploadOkArray[] = 0;
    } else{
        if (move_uploaded_file($file["tmp_name"], $target_file)){
            $msg[] = "Imagen ". basename( $file["name"]). " subida correctamente.";
            $uploadOkArray[] = 1;
        } else{
            $msg[] = "Imagen ". basename( $file["name"]). " no fue subida.";
            $uploadOkArray[] = 0;
        }
    }
}

$db = DbConfig::getConnection();
if(isset($_POST["submitDoc"])){

    insertDoc($db, $nombreMedico, $regionMedico, $comunaMedico, $celMedico, 
    $mailMedico, $twitMedico, $expMedico, $especialidadesMedico, $msg, $uploadOkArray, $fotos, $target_dir);

}
$db->close();


function insertDoc($db, $nombre,$region, $comuna, $cel, $mail, $twit, $exp, $esp, $msg, $okArray, $fotos, $target_dir){
    $find_comuna="SELECT id FROM comuna WHERE nombre LIKE '$comuna'";
    $resultado = $db->query($find_comuna);
    $id_comuna = mysqli_fetch_array($resultado)["id"];

    $sql=$db->prepare("INSERT INTO medico (nombre, experiencia, comuna_id, twitter, email, celular) 
    VALUES ('$nombre', '$exp', '$id_comuna', '$twit', '$mail', '$cel') ");
    
    $sql->execute();

    $id_medico = $db->insert_id;
    foreach ($esp as $especialidad){
        if ($especialidad != NULL){
            $find_especialidad = "SELECT id FROM especialidad WHERE descripcion LIKE '$especialidad'";
            $resultado = $db->query($find_especialidad);
            $id_especialidad = mysqli_fetch_array($resultado)["id"];

            $sql2=$db->prepare("INSERT INTO especialidad_medico (medico_id,especialidad_id) VALUES ('$id_medico','$id_especialidad')");
            $sql2->execute();
        }
    }

    foreach($fotos as $key => $value){
        //Insertamos solo si se cargo en el directorio media. 
        if($okArray[$key]){
            $sql3=$db->prepare("INSERT INTO foto_medico (ruta_archivo,nombre_archivo,medico_id) VALUES ('$target_dir','$value','$id_medico')");
		    $sql3->execute();
        }
    }


    echo "<h1>Registro de medico completado.</h1><h2>Redireccionando a pagina inicial.</h2>";

    foreach($msg as $key => $value){
        echo $value, '<br>';
    }
    echo "<td><button onclick = \"location.href = 'Inicio.html';\">Volver a pagina inicial</button></td>";
}
?>

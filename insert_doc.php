<?php
require_once('db_config.php');


/** Create 'media' directory: */
$cd = getcwd();
if (!file_exists($cd . "/media")){
    mkdir($cd . "/media", "0777");
}


$nombreMedico = $_POST['nombre-medico'];
$regionMedico = $_POST["region-medico"];
$comunaMedico = $_POST["comuna-medico"];
$celMedico = $_POST["celular-medico"];
$mailMedico = $_POST["email-medico"];
$twitMedico = $_POST["twitter-medico"];
$expMedico = $_POST["experiencia-medico"];

/**Create array with 'especialidades' */
$especialidadesMedico = array();
array_push($especialidadesMedico, $_POST["especialidades-medico"],$_POST["especialidades-medico-2"],
$_POST["especialidades-medico-3"],$_POST["especialidades-medico-4"],$_POST["especialidades-medico-5"]);
/**Delete the 'especialidades' left blanc */
$especialidadesMedico = \array_diff($especialidadesMedico,["sin-especialidad"]);

$db = DbConfig::getConnection();
if(isset($_POST["submitDoc"])){
    $encontrar_comuna="SELECT id FROM comuna WHERE nombre LIKE '$comunaMedico'";
    $resultado = $db->query($encontrar_comuna);
    $id_comuna = mysqli_fetch_array($resultado)["id"];
            
    $sql=$db->prepare("INSERT INTO medico (nombre, experiencia, comuna_id, twitter, email, celular) 
    VALUES ('$nombreMedico', '$expMedico', '$id_comuna', '$twitMedico', '$mailMedico', '$celMedico') ");

    $sql->execute();

    $id_medico = $db->insert_id;
}
$db->close();


?>
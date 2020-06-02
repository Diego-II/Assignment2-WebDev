<?php 
require_once('db_config.php');
require_once('ver_sol_aux.php');

$db = DbConfig::getConnection();


//$sol = getOneSol($db, 5);


$sql="SELECT MAX(id) FROM solicitud_atencion";
$resultado=$db->query($sql);
$ultimo_id = mysqli_fetch_array($resultado)["MAX(id)"];

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = 1 + ($page-1) * 5;


$n_pages = ceil($ultimo_id/5);

function getSols($db, $inicio_id, $ultimo_id){
    if($inicio_id + 4 <= $ultimo_id){
        $sol_array = getNSol($db, $inicio_id, $inicio_id + 4);
    } else{
        $sol_array = getNsol($db, $inicio_id, $ultimo_id);
    }
    return $sol_array;
}
$sol_array = getSols($db, $start_from, $ultimo_id);
$db -> close();


?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ver Solicitudes</title>
        <link rel="stylesheet" href="estilos_docs.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="ver_medicos.js"></script>
    </head>
<body>

<?php
for ($i=1; $i<=$n_pages; $i++) {  // print links for all pages
    echo "<a href='index.php?page=".$i."'";
    if ($i==$page)  echo " class='curPage'";
    echo ">".$i."</a> "; 
}; 
?>
    <!-- Navbar -->
<div class="w3-top">
    <div class="w3-bar w3-red w3-card w3-left-align w3-large">
      <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-red" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>
      <a href="Inicio.html" class="w3-bar-item w3-button w3-padding-large w3-white">Home</a>
      <a href="ver_medicos.php?page=1" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Ver Medicos</a>
      <a href="agregar_medicos.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Agregar Medicos</a>
      <a href="agregar_solicitud.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Agregar Solicitud</a>
      <a href="ver_solicitudes.php?page=1" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Ver Solicitudes</a>
    </div>
  
    <!-- Navbar on small screens -->
    <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
      <a href="ver_medicos.php?page=1" class="w3-bar-item w3-button w3-padding-large">Ver Medicos</a>
      <a href="agregar_medicos.html" class="w3-bar-item w3-button w3-padding-large">Agregar Medicos</a>
      <a href="agregar_solicitud.html" class="w3-bar-item w3-button w3-padding-large">Agregar Solicitud</a>
      <a href="ver_solicitudes.php?page=1" class="w3-bar-item w3-button w3-padding-large">Ver Solicitudes</a>
    </div>
  </div>
  <div class="w3-row-padding w3-padding-64 w3-container">
    <div class="w3-content">
        <h1>Ver Solicitudes</h1>

        <table>
        <thead>
            <tr>
                <th>Nombre Solicitante</th>
                <th>Especialidades</th>
                <th>Comuna</th>
                <th>Datos contacto</th>
            </tr>
        </thead>
        <?php
        foreach($sol_array as $k => $sol){
        echo "
        <!--- non hidden rows -->
        <tr class='text-field-l' onclick=mostrarDosInfo(\"info0".$k."\",\"info1".$k."\");>
        <td>".$sol["nombre-solicitante"]."</td>
        <td>".$sol["especialidad-solicitante"]."</td>
        <td>".$sol["comuna-solicitante"]."</td>
        <td>
        <p>
            Tel: <mark class='bold'>".$sol["celular-solicitante"]."</mark> 
            <br>
            Mail: <mark class='bold'>".$sol["email-solicitante"]."</mark>
            <br>
            Twitter: <mark class='bold'>".$sol["twitter-solicitante"]."</mark>
        </p>
        </td>
        </tr>
        <!--- Hidden Row: -->
        <tr class='text-field-l'>
            <td colspan='1'>
                <p  id='info0".$k."' style='display: none;'>
                Nombre: <mark class='bold'>".$sol["nombre-solicitante"]."</mark> 
                <br>
                Region: <mark class='bold'>".$sol["region-solicitante"]."</mark>
                <br>
                Comuna: <mark class='bold'>".$sol["comuna-solicitante"]."</mark>
                <br>
                Especialidades: <mark class='bold'>".$sol["especialidad-solicitante"]."</mark>
                <br>
                Tel: <mark class='bold'>".$sol["celular-solicitante"]."</mark>
                <br>
                Mail: <mark class='bold'>".$sol["email-solicitante"]."</mark>
                <br>
                Twitter: <mark class='bold'>".$sol["twitter-solicitante"]."</mark>
                </p>
            </td>
            <td colspan='3'>
                <p id='info1".$k."' style='display: none;'>
                    Informacion Adicional: 
                        <mark class='bold'>".$sol["sintomas-solicitante"]."</mark>
                    <br><br>
                    Archivos adicionales (Para descargar): 
                    <mark class='bold'><br>".
                    getFilesNames($sol["files-path"], $sol["files-name"], $sol["files-mime"])
                    ."</mark>
                </p>
            </td>
            </tr>  

        ";}
        ?>
        </table>

<table>
    <tr>
        <td>
            <a href = <?php echo getPrevPage()?>  style="text-align:left"> &lt;&lt;&lt; </a> 
        </td>
        <td  style="text-align:center"> <?php echo "Pagina ".$_GET["page"]."/".$n_pages;?> </td> 
        <td>
            <a href = <?php echo getNextPage($n_pages)?>  style="text-align:right"> >>> </a> 
        </td>
    <tr>
  </table>
  </div>
  </div>
</body>
</html>

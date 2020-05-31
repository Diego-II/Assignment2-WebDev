<?php
require_once('db_config.php');
//require_once('insert_doc.php');
require_once('ver_docs_aux.php');

$db = DbConfig::getConnection();

//$sql="SELECT MAX(id) FROM medico";
//$resultado=$db->query($sql);
//$ultimo_id = mysqli_fetch_array($resultado)["MAX(id)"];

//$doc = getOneDoc($db, $ultimo_id);

/**
 * Get n docs with 5 tops:
 */
//$ultimo_id = mysqli_fetch_array($resultado)["MAX(id)"];
//Cantidad de paginas:
//$n_pages = ceil($ultimo_id/5);

$doc_array = getNDocs($db, 1, 3);
$db->close();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ver Medicos</title>
        <link rel="stylesheet" href="estilos_docs.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="ver_medicos.js"></script>
    </head>
<body>

    <!-- Navbar -->
<div class="w3-top">
    <div class="w3-bar w3-red w3-card w3-left-align w3-large">
      <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-red" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa fa-bars"></i></a>
      <a href="Inicio.html" class="w3-bar-item w3-button w3-padding-large w3-white">Home</a>
      <a href="ver_medicos.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Ver Medicos</a>
      <a href="agregar_medicos.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Agregar Medicos</a>
      <a href="agregar_solicitud.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Agregar Solicitud</a>
      <a href="ver_solicitudes.html" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white">Ver Solicitudes</a>
    </div>
  
    <!-- Navbar on small screens -->
    <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium w3-large">
      <a href="ver_medicos.html" class="w3-bar-item w3-button w3-padding-large">Ver Medicos</a>
      <a href="agregar_medicos.html" class="w3-bar-item w3-button w3-padding-large">Agregar Medicos</a>
      <a href="agregar_solicitud.html" class="w3-bar-item w3-button w3-padding-large">Agregar Solicitud</a>
      <a href="ver_solicitudes.html" class="w3-bar-item w3-button w3-padding-large">Ver Solicitudes</a>
    </div>
  </div>
  <!---<?php require_once("ver_medicos.php");?> -->

  <div class="w3-row-padding w3-padding-64 w3-container">
    <div class="w3-content">
        <h1>Ver Medicos</h1>

    <table>
        <thead>
            <tr>
                <th>Nombre Medico</th>
                <th>Especialidades</th>
                <th>Comuna</th>
                <th>Datos contacto</th>
            </tr>
        </thead>
        <?php
        //Iterete over doctors:
        foreach($doc_array as $k => $doc){
        echo "
        <tr class='text-field-l'  onclick=onClickAction('mySlides".$k."','info1".$k."','info2".$k."')>        
        <td>".$doc["nombre-medico"]."</td>
            <td>"
                .implode(', ',$doc["especialidad-medico"])."
            </td>
            <td>".$doc["comuna-medico"]."</td>
            <td>
                <p>
                Tel: <mark class='bold'>".$doc["celular-medico"]."</mark>
                <br>
                Mail: <mark class='bold'>".$doc["email-medico"]."</mark>
                <br>
                Twitter: <mark class='bold'>".$doc["twitter-medico"]."</mark>
                </p>
        </td>
        </tr>
        <tr class='text-field-l'>
                <td >
                    <div class='w3-content w3-display-container'  id='info1".$k."' style='display: none;'>"
                    .getFotosSlides($k, $doc["nombre-medico"], $doc["dir-fotos"], $doc["fotos-medico"]).
                    "<button class='w3-button w3-black w3-display-left' onclick=plusDivs(-1,'mySlides".$k."')>&#10094;</button>
                    <button class='w3-button w3-black w3-display-right' onclick=plusDivs(1,'mySlides".$k."')>&#10095;</button>
                    </div>
                </td>
                <td colspan='3'>
                <p  id='info2".$k."' style='display: none;'>
                    Nombre: <mark class='bold'>".$doc["nombre-medico"]."</mark> 
                    <br>
                    Region: <mark class='bold'>".$doc["region-medico"]."</mark>
                    <br>
                    Comuna: <mark class='bold'>".$doc["comuna-medico"]."</mark>
                    <br>
                    Especialidades: <mark class='bold'>".implode(', ',$doc["especialidad-medico"])."</mark>
                    <br>
                    Tel: <mark class='bold'>".$doc["celular-medico"]."</mark>
                    <br>
                    Mail: <mark class='bold'>".$doc["email-medico"]."</mark>
                    <br>
                    Twitter: <mark class='bold'>".$doc["twitter-medico"]."</mark>
                </p>
            </td>
        </tr>";
        }
        ?>   
    </table>
</div>
</div>

</body>
</html>
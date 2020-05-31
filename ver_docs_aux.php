<?php 

function getOneDoc($db, $doc_id){

    //Get de la info del doc. 
    $find_doc ="SELECT id, nombre, experiencia, comuna_id, twitter, email, celular FROM medico WHERE id='$doc_id'";
    $doc_data = $db->query($find_doc);
    $doc = mysqli_fetch_array($doc_data);


    //Get Especialidades
    $find_esp = "SELECT especialidad_id FROM especialidad_medico WHERE medico_id='$doc_id'";
    $especialidades_raw = $db->query($find_esp);

    $array_especialidades=[];
    for ($i = 1; $i <= 5; $i++) {
        $row = mysqli_fetch_row($especialidades_raw);
        if ($row !== NULL){
            $array_especialidades[]=$row[0];
        }
    }

    $especialidades_medico=[];
    foreach($array_especialidades as $especialidad){
        $especialidad_tabla="SELECT descripcion FROM especialidad WHERE id='$especialidad'";
        $resultado=$db->query($especialidad_tabla);
        $especialidad_medico = mysqli_fetch_array($resultado);
        $especialidades_medico[] = $especialidad_medico[0];
    }
    
    

    //Query fotos
    $find_fotos="SELECT ruta_archivo,nombre_archivo FROM foto_medico WHERE medico_id='$doc_id'";
    $doc_fotos = $db->query($find_fotos);
    //Get fotos
    $arr_fotos = array();
    $arr_dir = array();
    for ($i = 1; $i <= 5; $i++) {
		$temp2=mysqli_fetch_row($doc_fotos);
	    if ($temp2 !== NULL){
			$arr_fotos[]=$temp2[1];
			$arr_dir[]=$temp2[0];
	    }
    }
    
    if($doc != NULL){
        $id_doc = $doc["id"];
	    $nombre_doc = $doc["nombre"];
	    $experiencia_doc = $doc["experiencia"];
	    $comuna_id_doc = $doc["comuna_id"];
	    $twitter_doc = $doc["twitter"];
	    $email_doc = $doc["email"];
        $celular_doc = $doc["celular"];

        $encontrar_comuna="SELECT nombre FROM comuna WHERE id='$comuna_id_doc'";
        $resultado_comuna=$db->query($encontrar_comuna);
        $comuna_doc_r=mysqli_fetch_array($resultado_comuna);
        $comuna_doc=$comuna_doc_r[0];

        $find_region = "SELECT nombre FROM region WHERE id =(SELECT region_id FROM comuna WHERE id = '$comuna_id_doc')";
        $resultado_region = $db->query($find_region);
        $region_doc_r = mysqli_fetch_array($resultado_region);
        $region_doc = $region_doc_r[0];
        
        return array(
            "id-medico" => $id_doc,
            "nombre-medico"  => $nombre_doc,
            "experiencia-medico" => $experiencia_doc,
            "comuna-medico" => $comuna_doc,
            "region-medico" => $region_doc,
            "twitter-medico" => $twitter_doc,
            "email-medico" => $email_doc,
            "celular-medico" => $celular_doc,
            "especialidad-medico" => $especialidades_medico,
            "fotos-medico" => $arr_fotos,
            "dir-fotos" => $arr_dir
        );
    } else{
        return false;
    }
}

function getNDocs($db, $id_inicial, $n_docs){
    $id_final = $id_inicial + $n_docs;
    $doc_array = array();
    if($n_docs > 1){
        for($doc_id = $id_inicial; $doc_id <= $id_final; $doc_id++){
            if(getOneDoc($db, $doc_id) !== false){
                array_push($doc_array, getOneDoc($db, $doc_id)); 
            } else{
                break;
            }
        }
    } else{
        array_push($doc_array,getOneDoc($db, $id_inicial));
    }
    return $doc_array;
}

?>
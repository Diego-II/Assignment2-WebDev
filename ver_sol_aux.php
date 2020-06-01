<?php

//Get one atention sol:
function getOneSol($db, $sol_id){
    
    //Get sol info:
    $find_info = "SELECT id, nombre_solicitante, especialidad_id, sintomas, twitter, email, celular, comuna_id FROM solicitud_atencion WHERE id='$sol_id'";
    $sol_info_raw = $db->query($find_info);
    $sol_info = mysqli_fetch_array($sol_info_raw);

    //Get Especialidad description:
    $esp_id = $sol_info["especialidad_id"];
    $find_esp = "SELECT descripcion FROM especialidad WHERE id='$esp_id'";
    $esp_raw = $db->query($find_esp);
    $esp_sol = mysqli_fetch_row($esp_raw)[0];

    //Get Comuna 
    $comuna_id = $sol_info["comuna_id"];

    $encontrar_comuna="SELECT nombre FROM comuna WHERE id='$comuna_id'";
    $comuna_raw=$db->query($encontrar_comuna);
    $comuna_sol = mysqli_fetch_array($comuna_raw)[0];

    $find_region = "SELECT nombre FROM region WHERE id =(SELECT region_id FROM comuna WHERE id = '$comuna_id')";
    $region_raw = $db->query($find_region);
    $region_sol = mysqli_fetch_array($region_raw)[0];

    //Get files:
    $find_files = "SELECT id, ruta_archivo, nombre_archivo, mimetype, solicitud_atencion_id FROM `archivo_solicitud` WHERE solicitud_atencion_id = '$sol_id'";
    $files_raw = $db -> query($find_files);
    //$files_sol = mysqli_fetch_array($files_raw);
    
    //Get dirs and mimetype:
    $arr_mime = array();
    $arr_name = array();
    $arr_path = array();
    for ($i = 1; $i <= 5; $i++) {
		$temp=mysqli_fetch_row($files_raw);
	    if ($temp !== NULL){
            $arr_mime[] = $temp[3];
            $arr_name[] = $temp[2];
            $arr_path[] = $temp[1];
	    }
    }


    

    if($sol_info !== NULL){
        return array(
            "id-solicitud" => $sol_info["id"],
            "nombre-solicitante" => $sol_info["nombre_solicitante"],
            "especialidad-solicitante" => $esp_sol,
            "sintomas-solicitante" => $sol_info["sintomas"],
            "twitter-solicitante" => $sol_info["twitter"],
            "email-solicitante" => $sol_info["email"],
            "celular-solicitante" => $sol_info["celular"],
            "region-solicitante" => $region_sol,
            "comuna-solicitante" => $comuna_sol,
            "files-mime" => $arr_mime,
            "files-name" => $arr_name,
            "files-path" => $arr_path
        );
    } else{
        return false;
    }    
}

function getNSol($db, $id_inicial, $n_sol){
    $id_final = $id_inicial + $n_sol;
    $sol_array = array();
    if($n_sol > 1){
        for($sol_id = $id_inicial; $sol_id <= $id_final-1; $sol_id++){
            if(getOneSol($db, $sol_id) !== false){
                array_push($sol_array, getOneSol($db, $sol_id)); 
            } else{
                break;
            }
        }
    } else{
        array_push($sol_array,getOneSol($db, $id_inicial));
    }
    return $sol_array;
}

function getFilesNames($files_path, $files_name, $files_mime){
    $len = count($files_mime);
    $res = array();
    if($len > 0){
        for($i = 0; $i<$len; $i++){
            $res[] = "<a href='".$files_path[$i]."' download='".$files_name[$i]."'>".$files_name[$i]."</a> (".$files_mime[$i].")";
        }
        return implode('<br>',$res);
    } else{
        return "Solicitante no deja archivos adicionales";
    }
}

?>
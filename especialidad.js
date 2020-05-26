var especialidades ={
    "especialidades": [
        {"especialidad": "Cardiología"},
        {"especialidad":"Gastroenterología"},
        {"especialidad":"Endocrinología"},
        {"especialidad":"Epidemiología"},
        {"especialidad":"Geriatría"},
        {"especialidad":"Hematología"},
        {"especialidad":"Infectología"},
        {"especialidad":"Medicina del deporte"},
        {"especialidad":"Medicina de urgencias"}, 
        {"especialidad":"Medicina interna"},
        {"especialidad":"Nefrología"},
        {"especialidad":"Neumología"},
        {"especialidad":"Neurología"},
        {"especialidad":"Nutriología"},
        {"especialidad":"Oncología"},
        {"especialidad":"Pediatría"},
        {"especialidad":"Psiquiatría"},
        {"especialidad":"Reumatología"},
        {"especialidad":"Toxicología"},
        {"especialidad":"Dermatología"},
        {"especialidad":"Ginecología"},
        {"especialidad":"Oftalmología"},
        {"especialidad":"Otorrinolaringología"},
        {"especialidad":"Urología"},
        {"especialidad":"Traumatología"}
    ]
}

jQuery(document).ready(function (){

    var iEspecialidad = 0;
    var htmlEspecialidad = '<option value="sin-especialidad">Seleccione una especialidad</option><option value="sin-especialidad">--</option>';

    jQuery.each(especialidades.especialidades, function(){
        htmlEspecialidad = htmlEspecialidad + '<option value="' + especialidades.especialidades[iEspecialidad].especialidad + '">'
        + especialidades.especialidades[iEspecialidad].especialidad + '</option>';
        iEspecialidad++;
    });

    jQuery('#especialidad').html(htmlEspecialidad);
    jQuery('#especialidad').change(function(){
        if(jQuery(this).val() == 'sin-especialidad'){
            alert('Selecciones especialidad');
        }
    });
    jQuery('#especialidad2').html(htmlEspecialidad);
    jQuery('#especialidad2').change(function(){
        if(jQuery(this).val() == 'sin-especialidad'){
            alert('Selecciones especialidad');
        }
    });
    jQuery('#especialidad3').html(htmlEspecialidad);
    jQuery('#especialidad3').change(function(){
        if(jQuery(this).val() == 'sin-especialidad'){
            alert('Selecciones especialidad');
        }
    });
    jQuery('#especialidad4').html(htmlEspecialidad);
    jQuery('#especialidad4').change(function(){
        if(jQuery(this).val() == 'sin-especialidad'){
            alert('Selecciones especialidad');
        }
    });
    jQuery('#especialidad5').html(htmlEspecialidad);
    jQuery('#especialidad5').change(function(){
        if(jQuery(this).val() == 'sin-especialidad'){
            alert('Selecciones especialidad');
        }
    });
});


function sigespec(id){
    document.getElementById(id).style = 'display = block;';
}

function sigbr(id){
    document.getElementById(id).style = 'display = block;';
}
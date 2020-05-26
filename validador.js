/**
 * Validador de los formularios
 */


function docValidator() {
    /** 
     * Solo se valida si todos son correctos:
     * -Nombre no vacio,
     * -Al menos una especialidad,
     * -Al menos una foto,
     * -Si hay twitter, que sea correcto,
     * -Mail no vacio y direccion correcta,
     * -Si hay numero de telefono, que cumpla con el formato.
     */

    /**Extraemos todos los contenidos del formulario: */

    const doc = document.getElementById("form_doc");

    const nombreMedico = doc[0].value;
    const emailMedico = doc[4].value;
    //Primera especialidad seleccionada. 
    const especialidadesMedico = doc[7].value;
    const fotosMedico = doc[12].value;
    const twitterMedico = doc[5].value;
    const celularMedico = doc[3].value;
    const comunaMedico = doc[2].value;
    const regionMedico = doc[1].value;

    
    if(nombreMedico == "" || nombreMedico == undefined){
        alert("Ingrese nombre");
        return false;
    }

    if (regionMedico == "sin-region"){
        alert("Seleccione region");
        return false;
    }

    if (comunaMedico == "sin-comuna"){
        alert("Seleccione comuna");
        return false;
    }

    if (celularMedico != ""){
        if (celularMedico.match(/\d/g).length===9 == false){
            alert("Ingrese un número de celular válido");
            return false;
        } else{
            return true;
        }
    }

    const emailRegExp = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if(!emailRegExp.test(emailMedico)){
        alert("Mail no valido");
        return false;
    }

    const twitterRegExp = /^@?(\w){1,15}$/;
    var twit = new Boolean(true);
    if(twitterMedico == ""){
        twit = false;
    }

    if (twit && !twitterRegExp.test(twitterMedico)){
        alert("Ingrese una cuenta de twitter válida");
        return false;
    }

    if (especialidadesMedico == "sin-especialidad"){
        alert("Debe elegir una especialidad");
        return false;
    }

    if (fotosMedico == ""){
        alert("Debe adjuntar una imagen ");
        return false;
    }

    else{
        return true;
    }    
}

/**
 * Validador informacion solicitudes.
 */
function solValidator() {
    /** Solo se valida si todos son correctos:
     * -Nombre no vacio,
     * -Al menos una especialidad,
     * -Al menos una foto,
     * -Si hay twitter, que sea correcto,
     * -Mail no vacio y direccion correcta,
     * -Si hay numero de telefono, que cumpla con el formato.
     */

    const doc = document.getElementById("form_sol");

    const nombreSol = doc[0].value;
    const especialidadesSol = doc[1].value;
    const sintomasSol = doc[2].value;
    //File 1 si se agregan. va de 3 - 7
    const fileSol = doc[3].value;
    // Doc[9] = button 'Archivo Adicional'
    const twitterSol = doc[9].value;
    const emailSol = doc[10].value;    
    const phoneSol = doc[11].value; 
    const regionSol = doc[12].value;
    const comunaSol = doc[13].value;


    
    if(nombreSol == "" || nombreSol == undefined){
        alert("Ingrese nombre");
        return false;
    }

    if (especialidadesSol == "sin-especialidad"){
        alert("Debe elegir una especialidad");
        return false;
    }


    const twitterRegExp = /^@?(\w){1,15}$/;
    var twit = new Boolean(true);
    if(twitterSol == ""){
        twit = false;
    }

    if (twit && !twitterRegExp.test(twitterSol)){
        alert("Ingrese una cuenta de twitter válida");
        return false;
    }

    const emailRegExp = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if(!emailRegExp.test(emailSol)){
        alert("Mail no valido");
        return false;
    }

    if (celularSol != ""){
        if (celularSol.match(/\d/g).length===9 == false){
            alert("Ingrese un número de celular válido");
            return false;
        } else{
            return true;
        }
    }

    if (regionSol == "sin-region"){
        alert("Seleccione region");
        return false;
    }

    if (comunaSol == "sin-comuna"){
        alert("Seleccione comuna");
        return false;
    }

    else{
        return true;
    }
}
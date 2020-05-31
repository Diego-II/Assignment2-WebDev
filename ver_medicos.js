/**
 * Slides show para imagenes
 * obtenido de:
 * https://www.w3schools.com/w3css/tryit.asp?filename=tryw3css_slideshow_self 
 */

var slideIndex = 1;
showDivs(slideIndex,name);

function plusDivs(n,name) {
  showDivs(slideIndex += n,name);
}



function showDivs(n,name) {
    var i;
    const fotos = document.getElementsByClassName(name);    

    if (n > fotos.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = fotos.length;
    }
    for (i = 0; i < fotos.length; i++) {
    fotos[i].style.display = "none";
    }
    
    try{
        fotos[slideIndex-1].style.display = "block";
    } catch(TypeError){}
}


function mostrarInfo(id){
    var x = document.getElementById(id);

    if (x.style.display == "none") {
        x.style.display = "block";
    } 
        else {
        x.style.display = "none";
    }
    
}
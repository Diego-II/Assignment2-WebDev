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
    var x = document.getElementsByClassName(name);

    if (n > x.length) {
        slideIndex = 1
    }
    if (n < 1) {
        slideIndex = x.length
    }
    for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
    }

    x[slideIndex-1].style.display = "block";  
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
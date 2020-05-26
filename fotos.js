function fotos(id){
    document.getElementById(id).style = "display = 'block';";
}

var i = 2;
function botonfoto(){
    var realId = 'foto' + (i);
    if(i<6){
        document.getElementById(realId).style = "display = 'block;";
        i++; 
    }
}

var j = 2;
function botonarc(){
    var realId = 'arc' + (j);
    if(j<6){
        document.getElementById(realId).style = "display = 'block;";
        j++; 
    }
}
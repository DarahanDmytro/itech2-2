function getKey(elem){
    var res = "";
    var sibs = elem.parent().children();
    for(let sib of sibs){
        res += sib.value + "|";
    }
    return res;
}

function registerKey(){
   var key = sessionStorage.getItem("currentKey");
   var item = $("#report").html();
   sessionStorage.setItem(key, item);
}


$(document).ready(function() {
	$('input[type="submit"]').click(function(event) {
        var elem = $(event.target);
        var key = getKey($(elem));
        sessionStorage.setItem("currentKey",key);
        if(sessionStorage.getItem(key)){
        	var data = sessionStorage.getItem(key);
        	$("#report").html(data);
        	alert("Дані взято зі сховища");
        	event.preventDefault();
        }
	});
});
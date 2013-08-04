var count = 1;

function allowDrop(event)
{
	event.preventDefault();
}

function drag(event)
{
	event.dataTransfer.setData("Text",event.target.id);
}

function dropInstrument(event)
{
	var div = event.target;
	
	//Div n'a pas d'enfant au départ, si on en trouve un on le supprime
	if (div.hasChildNodes()) {
		div.removeChild(div.firstChild);
	}
	
	//On récupère le drag et son contenue texte
	var data = event.dataTransfer.getData("Text");  
	var contenu = document.getElementById(data).innerHTML;
	
	//On crée un new element p
	var resu = document.createElement("p");
	resu.setAttribute("id", data + "ibis" + count.toString());
	resu.setAttribute("class", "pDroper");
	//resu.setAttribute("draggable", "true");
	//resu.setAttribute("ondragstart", "drag(event)");
	resu.innerHTML = contenu;
	
	//On incrémente l'id
	count ++;
	
	//On insère le p
	event.target.appendChild(resu);

	//On change la value de l'input
	var formulaire = div.parentNode;
	var input = document.getElementById("idInstrument");
	input.removeAttribute("value");
	var num_id = data.slice(1, data.length);
	input.setAttribute("value", num_id);

	//Options
	event.stopPropagation();
	event.preventDefault();
	return false;
}


function dropUser(event)
{

}



/*
function removeDrop(event)
{
	//On récupère le drag
	var data = event.dataTransfer.getData("Text");
	var remove = document.getElementById(data);
	
	//Si l'id commence par "ibis" on rememet son père div droppable et on supprime le drag
	var exp = new RegExp("ibis");
	
	if (exp.test(data)) {
		//On remmet div droppable
		var div = remove.parentNode;
		div.setAttribute("ondrop", "drop(event)");
		div.setAttribute("ondragover", "allowDrop(event)");
		
		//On supprime le drag
		remove.parentNode.removeChild(remove);
	}
	
	//Options
	event.stopPropagation();
	return false;
}
*/


function init_drag_and_drop()
{
	$("#listeCentre li.elem").addClass("cursormove").draggable({
		appendTo: $("#bd").parent(),
		cursor: "move",
		cursorAt: { top: 0, left: 0 },
		helper: function() { return $("<div class=\"ui-widget ui-widget-content ui-corner-all elemDrag\" ></div>").css("z-index",105).html($(this).text());}
	});

	$("#dragInstrument").droppable({
		accept: $("#listeCentre li.elem"),
		activeClass: "ui-state-hover",
		hoverClass: "ui-state-active",
		drop: function( event, ui ) 
		{
			var id = ui.draggable.find("span").attr("id");

			var nom = $(this).parent().find("#nom");
			var nomDrag = $(this).parent().find("#dragInstrument").find("span");
			if(nom && nomDrag && nom.val().toLowerCase() == nomDrag.html().toLowerCase())
			{
				nom.val(ui.draggable.find("span").html());				
			}
			$(this).find("span").html(ui.draggable.find("span").html());
			$(this).parent().find("#idInstrument").val(id.substring(1,id.length));
			majUser(id,$("#idUser"));
			
		}
	});
	
	$("#poubelle").unbind("click").click(function()
	{

		var id = $("#idInstrument",$("#bd")).val();
		var nom = $("#listeCentre").find("#i"+id).html();
		var inputNom = $("#nom",$("#bd"));
		if(nom && inputNom && nom.toLowerCase() == inputNom.val().toLowerCase())
		{			
			inputNom.val("");
		}
		
		$("#idInstrument").val(0);
		$("#dragInstrument").find("span").html("");
		majUser(-1,$("#idUser"));
	});
}
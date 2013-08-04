<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire catégorie",0);

if(isset($_POST['idNoeud']) and isset($_POST['mode']) )
{
	
	$idNoeud  = $_POST['idNoeud'];
	$mode  = $_POST['mode'];
	
	if( $idNoeud != "" and !preg_match("#^([\d-]+)$#i",$idNoeud) )
		$f->ajaxErreur("Format invalide","Le format de l'identifiant est invalide");

	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
		
		
	$nomInstrument = "";
	$nomBrutInstrument = "";
	$nomUser = "";
	$idUser = 0;
	$idInstrument = 0;
	if($idNoeud != "" and $mode > 1)
	{
		$res = $f->query("SELECT nom as nomInstrument,
								 user as idUser,
								 instrument as idInstrument
							FROM userinstrumentconcert 
							WHERE userinstrumentconcert = '".e($idNoeud)."' ;");
	
		if($row = $res->fetch())
		{
			$nomInstrument = $row["nomInstrument"];
			$idUser = $row["idUser"];
			$idInstrument = $row["idInstrument"];
			
			if($idInstrument != 0)
			{
				$res2 = $f->query("SELECT nom
									FROM instrument 
									WHERE instrument = '".e($idInstrument)."' ;");
									
				if($row2 = $res2->fetch())
					$nomBrutInstrument = $row2["nom"];
			}
		}
		
		
	}
	
	$f->retourAjax["boutton"] = ($mode == 1 ? "Ajouter" : "Modifier");

	

	$form = "<div id=\"feedback\"></div><br />";
// <div id=\"dragInstrument\" class=\"ui-widget ui-widget-content ui-corner-all panier ui-state-default\" ondrop=\"dropInstrument(event)\" ondragover=\"allowDrop(event)\"></div>
	$form .= "<form action=\"#\" method=\"post\" onSubmit=\"return actionUserInstrumentConcert($(this));\">
				<br /><label for=\"nom\">Nom : </label>
				<input id=\"nom\" type=\"text\" value=\"".s($nomInstrument)."\" name=\"nom\" />
				<input id=\"idInstrument\" type=\"hidden\" value=\"".s($idInstrument)."\" name=\"idInstrument\" />			

				<br /><br /><label for=\"dragInstrument\" class=\"labelPanier\">Instrument : </label>
				<div id=\"dragInstrument\" class=\"ui-widget ui-widget-content ui-corner-all panier ui-state-default\" ><span>".s($nomBrutInstrument)."</span></div>
				<img src=\"../img/poubelle.png\" id=\"poubelle\" class=\"cursorpointer\" title=\"Supprimer l'instrument et l'utilisateur\"/>
				
				<br /><label for=\"idUser\" >Utilisateur : </label>
				<select id=\"idUser\" name=\"idUser\"><option value=\"0\">Choisir utilisateur</option>";
				$res = $f->query("SELECT user,CONCAT(UPPER(nom),' ',prenom) as nomUser
										FROM user WHERE user IN (
											SELECT user FROM competence WHERE instrument = '".e($idInstrument)."'
										);");
				while($row = $res->fetch())
					$form .= "<option value=\"".s($row["user"])."\" ".($idUser == $row["user"] ? "selected=\"selected\"":"").">".s($row["nomUser"])."</option>";
				$form .= "</select>
				
				<input type=\"hidden\" value=\"".$mode."\" name=\"mode\" />
				<input type=\"hidden\" value=\"".$idNoeud."\" name=\"idPere\" />
			  </form>";
	
	$f->retourAjax["erreur"] = false;
	$f->retourAjax["msg"] = $form;
}

$f->endAjax();
?>


<!-- <div  id=\"dragInstrument\" class=\"ui-widget ui-widget-content ui-corner-all panier\"></div> -->
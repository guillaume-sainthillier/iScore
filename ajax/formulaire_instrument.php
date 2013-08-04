<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire concert",0);

if(isset($_POST['mode']) and isset($_POST['idCat']) and isset($_POST['idInstrument']) and isset($_POST["nomInstrument"]))
{
	$mode			= $_POST['mode'];
	$idCat 			= $_POST['idCat'];
	$idInstrument 	= $_POST['idInstrument'];
	$nomInstrument 	= $_POST['nomInstrument'];
	
	if( !in_array($mode,array(1,2,3,4,5,6)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	if( ($idCat			!= ""	and !preg_match("#^i([\d-]+)$#i",$idCat))   or
		($idInstrument 	!= "" 	and !preg_match("#^i([\d-]+)$#i",$idInstrument))   )
			$f->ajaxErreur("Format invalide","Le format de l'identifiant est invalide");

	if($idInstrument != "") $idInstrument = substr($idInstrument,1,strlen($idInstrument)); // Ne vaut forcément qu'un nombre

	
	if($idCat == "") $idCat = "i-1";
	
	$idCat = substr($idCat,1,strlen($idCat)); // Ne vaut forcément qu'un nombre
	

	if($mode <= 3) {
		$la = "l'";
		$dun = "d'un";
		$nom = "instrument";
	}else { 
		$la = "la ";
		$dun = "d'une";
		$nom = "catégorie";
	}
	
	if($mode % 3 == 1){		$header = "Ajout $dun $nom"; 			$nomAction = "Ajouter";		$disable = "";	 					}
	elseif($mode % 3 == 2){	$header = "Modification $dun $nom"; 	$nomAction = "Modifier"; 	$disable = "";						}
	else{ 					$header = "Suppression $dun $nom"; 		$nomAction = "Supprimer"; 	$disable = "disabled=\"disabled\""; }
	

	
	$f->retourAjax["boutton"] = $nomAction." ".$la.$nom;
	$f->retourAjax["header"] = $header;
	
	if($mode == 1) $header = "Ajout d'un instrument";
	
	$form = "<div id=\"feedback\"></div><br />";
	if($mode == 6) //Suppression d'une catégorie
		$form .= BMInfo("La supression d'une catégorie entraîne la suppression de tous les instruments associés à cette catégorie ");
		
	$form .= "<form action=\"#\" method=\"post\" onSubmit=\"return actionInstrument($(this));\">
				<br /><label for=\"nomInstrument\">Nom de $la$nom :</label>
				<input id=\"nomInstrument\" type=\"text\" value=\"".s($nomInstrument)."\" name=\"nomInstrument\" $disable/><br /><br />";
	
	if($mode > 3)
		$form .= "<input type=\"hidden\" name=\"idCat\" value=\"0\" />";
		
	if($mode <= 3)
	{
		$res = $f->query("SELECT instrument,nom FROM instrument WHERE categorie = '0' ORDER BY nom; ");
		$form .= "<label for=\"idCat\">Catégorie :</label><select id=\"idCat\" name=\"idCat\" $disable>";
		if($res->numRows() == 0)
		{
			$form .= "<option value=\"-1\" >Aucune catégorie</option>";
		}else
		{
			$form .= "<option value=\"-1\">Choisir catégorie</option>
					  <option value=\"-1\">Divers</option>";
			while($row = $res->fetch())
				$form .= "<option value=\"".$row["instrument"]."\" ".($idCat == $row["instrument"] ? "selected=\"selected\"":"").">".ucfirst($row["nom"])."</option>";
		}
	}elseif($mode == 6)
	{
		$form .= "<label for=\"idCat\" >Instruments associés :</label> ";
		$res = $f->query("SELECT instrument,nom FROM instrument WHERE categorie = '".$idCat."'; ");
		if($res->numRows() == 0)
			$form .= "Aucun";
		else
		{
			$form .= "<textarea rows=\"3\" cols=\"30\" disabled=\"disabled\">";
			while($row = $res->fetch())
				$form .= $row["nom"]."\n";
				
			$form .= "</textarea>";
		}
	}
	
	
	$form .= "<input type=\"hidden\" name=\"mode\" value=\"".$mode."\" />
			<input type=\"hidden\" name=\"idInstrument\" value=\"".$idInstrument."\" />
	</form>";
	
	$f->retourAjax["erreur"] = false;
	$f->retourAjax["msg"] = $form;
}

$f->endAjax();
?>
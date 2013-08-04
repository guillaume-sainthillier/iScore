<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire concert",0);

if(isset($_POST['mode']) and isset($_POST['idConcert']) and isset($_POST['dateConcert']) and isset($_POST["nomConcert"]) and isset($_POST['isTemplate']))
{

	$mode			= $_POST['mode'];
	$idConcert 		= $_POST['idConcert'];
	$dateConcert 	= ($mode == 1 ? date("d/m/Y") : $_POST['dateConcert']);
	$nomConcert 	= $_POST['nomConcert'];
	$isTemplate 	= ($_POST['isTemplate'] == "true" ? true : false);
	
	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	if( ($idConcert		!= ""	and !preg_match("#^([\d-]+)$#i",$idConcert)))
		$f->ajaxErreur("Format invalide","Le format de l'identifiant est invalide");

	$disable = "";
	
	if($mode == 1)
	{
		$header = "Ajout d'un concert";
		$bouton = "Ajouter le concert";
	}elseif($mode == 2)
	{
		$header = "Modification d'un concert";
		$bouton = "Modifier le concert";
	}elseif($mode == 3)
	{
		$header = "Suppression d'un concert";
		$bouton = "Supprimer le concert";
		$disable = "disabled=\"disabled\"";
	}
	$form = "<div id=\"feedback2\"></div><br />".
			($mode == 3? BMInfo("La supression du concert entraîne la suppression de toute la configuration associée à ce concert") : "").
			"<form action=\"#\" method=\"post\" onSubmit=\"return actionConcert($(this));\">
				<br /><label for=\"nomConcert\">Nom du concert :</label>
				<input id=\"nomConcert\" type=\"text\" size=\"30\" value=\"".s($nomConcert)."\" name=\"nomConcert\" $disable />
				<br />
				<br /><label for=\"dateConcert\">Date du concert :</label>
				<input id=\"dateConcert\"  class=\"datepicker\"  size=\"12\" type=\"text\" value=\"".s($dateConcert)."\" name=\"dateConcert\" $disable />
				<br /><label for=\"isTemplate\">Concert de configuration :</label>
				<input id=\"isTemplate\" type=\"checkbox\" class=\"formcheckbox\" value=\"\" name=\"isTemplate\" $disable ".($isTemplate? "checked=\"checked\"":"")."/>
				<br /><br />";
				if($mode == 1) //Ajout
				{
					$res = $f->query("SELECT racineUserInstrumentConcert,nom  FROM concert WHERE isTemplate = '1';");
					
					$form .= "<label for=\"templateConcert\">Créer à partir de :</label>
					<select name=\"templateConcert\" id=\"templateConcert\"><option value=\"\">Rien</option>";
					while($row = $res->fetch())
					{
						$form .= "<option value=\"".$row["racineUserInstrumentConcert"]."\">".s($row["nom"])."</option>";
					}
					$form .= "</select><br />";				
				}
			$form .="<input type=\"hidden\" name=\"mode\" value=\"".$mode."\" />
				<input type=\"hidden\" name=\"idConcert\" value=\"".$idConcert."\" />
			</form>";

	
	$f->retourAjax["boutton"] = $bouton;
	$f->retourAjax["header"] = $header;	
	$f->retourAjax["erreur"] = false;
	$f->retourAjax["msg"] = $form;
}

$f->endAjax();
?>
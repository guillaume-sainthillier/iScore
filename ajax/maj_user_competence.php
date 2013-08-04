<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("",0);

if(isset($_POST['idInstrument']))
{
	$idInstrument = preg_replace("/[^\d]/i","",$_POST["idInstrument"]);
	$select = "<option value=\"0\">Choisir utilisateur</option>";
	$res = $f->query("SELECT user,CONCAT(UPPER(nom),' ',prenom) as nomUser
							FROM user WHERE user IN (
								SELECT user FROM competence WHERE instrument = '".e($idInstrument)."'
							) ORDER BY nomUser;");
	while($row = $res->fetch())
		$select .= "<option value=\"".s($row["user"])."\" >".s($row["nomUser"])."</option>";

	$f->ajaxOK("","");
	$f->retourAjax["select"] = $select;
}

$f->endAjax();
?>
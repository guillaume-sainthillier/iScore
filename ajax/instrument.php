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
	
	if( ($idCat			!= ""	and !preg_match("#^([\d-]+)$#",$idCat))   or
		($idInstrument 	!= "" 	and !preg_match("#^([\d-]+)$#",$idInstrument))   )
			$f->ajaxErreur("Format invalide","Le format de l'identifiant est invalide");

	
	if($mode <= 3) {
		$dun = "de l'";
		$nom = "instrument";
	}else { 
		$dun = "de la ";
		$nom = "catégorie";
	}
	
	if($mode % 3 == 1){			$nomAction = "L'ajout $dun$nom <b>&laquo; $nomInstrument &raquo;</b> s'est déroulé avec succès"; 				}
	elseif($mode % 3 == 2){		$nomAction = "La modification $dun$nom <b>&laquo; $nomInstrument &raquo;</b> s'est déroulée avec succès"; 		}
	else{						$nomAction = "La suppression $dun$nom <b>&laquo; $nomInstrument &raquo;</b> s'est déroulée avec succès"; 		}

	if($mode % 3 != 0) //Ajout & Modif : vérification de la disponibilité du nom de l'instrument
	{
		$isCat = ($mode > 3);
		$sql = "SELECT nom,instrument FROM instrument WHERE LOWER(nom) = '".e(strtolower($nomInstrument))."' AND categorie ".($isCat ? "=":"!=")." '0' ;";
		$res = $f->query($sql);
		if($res->numRows() > 0)
		{
			$row = $res->fetch();
			$vraiNom = $row["nom"];
			$idIns = $row["instrument"];
			if($idIns != $idInstrument)
				$f->ajaxErreur(	($isCat ? "Catégorie déjà existante":"Instrument déjà existant" ),
							($isCat ? "La catégorie ":"L'instrument ")."<b>&laquo; $vraiNom &raquo;</b> existe déjà");
		}
	}
	
	
	if($mode % 3 == 1) //Ajout
	{
		if(trim($nomInstrument) == "")
			$f->ajaxErreur("Nom $dun$nom vide","Le nom $dun$nom ne doit pas être vide");
		
		$sql = "INSERT INTO instrument(nom,categorie) VALUES('".e($nomInstrument)."','".$idCat."');";
	}elseif($mode % 3 == 2) //Modif
	{			
		$sql = "UPDATE instrument SET nom = '".e($nomInstrument)."', categorie = '".$idCat."' 
				WHERE instrument = '".$idInstrument."' ;";
	}elseif($mode % 3 == 0) //Supp
	{
		$res = $f->query("SELECT instrument 
					FROM instrument 
					WHERE instrument = '".$idInstrument."' OR categorie = '".$idInstrument."' ;");
		$tabId = array();
		while($row = $res->fetch())
			$tabId[] = $row["instrument"];
		$sql = "DELETE FROM instrument 
				WHERE instrument IN ('".implode("','",$tabId)."'); ";
	}
	
	$f->query($sql);
	if($mode % 3 == 1)// Ajout
		$idInstrument = $f->db->lastId();
		
	$f->retourAjax["mode"] = $mode;
	$f->retourAjax["idInstrument"] = $idInstrument;
	$f->retourAjax["idCat"] = $idCat;
	$f->retourAjax["nomInstrument"] = ucfirst($nomInstrument);

	$f->ajaxOK("Action effectuée",$nomAction,false);
		
}

$f->endAjax();
?>
<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire concert",0);

if(isset($_POST['mode']) and isset($_POST['idPere']) and isset($_POST['nom']) and isset($_POST['idInstrument']) and isset($_POST['idUser']))
{
	$mode			= $_POST['mode'];
	$idInstrument 	= $_POST['idPere'];
	$nomInstrument 	= $_POST['nom'];
	$instrument 	= $_POST['idInstrument'];
	$user 			= $_POST['idUser'];
	
	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	if( !preg_match("#^([\d-]+)$#",$idInstrument))
		$f->ajaxErreur("Format invalide","Le format de l'identifiant est invalide");

	$f->retourAjax["mode"] = $mode;
	if($mode == 1) //Ajout
	{
		$res = $f->query("SELECT lft, rght,role,
									(SELECT concert_id 
											FROM userinstrumentconcert 
											WHERE lft <= u.lft 
												AND rght >= u.rght 
												AND parent_id = '0'
									) as 'concert_id'
								FROM userinstrumentconcert u WHERE userInstrumentConcert = '".e($idInstrument)."' ;");
		if(! $row = $res->fetch())
			$f->ajaxErreur("Erreur","L'ajout de l'instrument a échoué");
		
		$f->query("UPDATE userinstrumentconcert SET rght = rght+2 WHERE rght >= '".$row['rght']."' ;");
		$f->query("UPDATE userinstrumentconcert SET lft = lft+2 WHERE lft > '".$row['rght']."' ;");
		$f->query("INSERT INTO userinstrumentconcert(lft,rght,parent_id,instrument,user,role,nom,concert_id)
					VALUES('".($row['rght'])."','".($row['rght']+1)."','".e($idInstrument)."','".e($instrument)."','".e($user)."','".($row['role'] + 1)."','".e($nomInstrument)."','".$row['concert_id']."');");		
		$idNewInstrument =  $f->db->lastId();			
	
		$f->retourAjax["idPere"] 		= $idInstrument;
		$f->retourAjax["idInstrument"] 	= $idNewInstrument;
		$f->retourAjax["nomInstrument"] = $nomInstrument;
		$f->ajaxOK("Action effectuée","Instrument ajouté");
		
	}elseif($mode == 3)//Suppression
	{
		$res = $f->query("SELECT lft, rght, parent_id
								FROM userinstrumentconcert 
								WHERE userInstrumentConcert = '".e($idInstrument)."' ;");
		if(! $row = $res->fetch() or $row['parent_id'] == 0)
			$f->ajaxErreur("Erreur","La supression de l'instrument a échoué");
		
		$f->query("DELETE FROM userinstrumentconcert WHERE lft>= '".$row['lft']."' AND rght <= '".$row['rght']."' ;");
		$nb = 1+ $row["rght"] - $row["lft"] ;
		$f->query("UPDATE userinstrumentconcert SET rght = rght-".$nb." WHERE rght >= '".$row['rght']."' ;");
		$f->query("UPDATE userinstrumentconcert SET lft = lft-".$nb." WHERE lft > '".$row['rght']."' ;");
		
	
		$f->retourAjax["idInstrument"] = $idInstrument;
		$f->ajaxOK("Action effectuée","Instrument supprimé");
	}elseif($mode == 2)//Modif
	{
		$f->query("UPDATE userinstrumentconcert SET
						instrument = '".e($instrument)."', 
						user = '".e($user)."',
						nom = '".e($nomInstrument)."' 
					WHERE userInstrumentConcert = '".e($idInstrument)."' ;");
		$f->retourAjax["idInstrument"] = $idInstrument;
		$f->retourAjax["nomInstrument"] = $nomInstrument;
		$f->ajaxOK("Action effectuée","Instrument modifié");
	}
}

$f->endAjax();
?>
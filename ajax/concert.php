<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire concert",0);

if(isset($_POST['mode']) and isset($_POST['nomConcert']) and isset($_POST['dateConcert']))
{
	$mode			= $_POST['mode'];
	$nomConcert 	= $_POST['nomConcert'];
	$dateConcert 	= $_POST['dateConcert'];
	$isTemplate		= isset($_POST['isTemplate']);
	$idConcert		= $_POST['idConcert'];
	
	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	$f->retourAjax["mode"] = $mode;
	
	if($mode == 1) //Ajout
	{
		if( stringtodate($dateConcert) == "0000-00-00")
			$f->ajaxErreur("Format invalide","Le format de la date est invalide");
		
		
		$res = $f->query("SELECT MAX(rght) as 'rght'
								FROM userinstrumentconcert;");
		if(! $row = $res->fetch())
			$rght = 1;
		else
			$rght = $row['rght'] +1;
			
		$f->query("INSERT INTO concert(dateConcert,nom,isTemplate)
							VALUES('".stringtodate($dateConcert)."','".e($nomConcert)."','".($isTemplate ? "1":"0")."');");			
							
		$idConcert = $f->db->lastId();
		
		$f->query("INSERT INTO userinstrumentconcert(lft,rght,parent_id,concert_id,instrument,user,role,nom)
							VALUES('".$rght."','".($rght +1)."','0','".$idConcert."','0','0','1','Chef d\'orchestre');");
		
		$idRacine = $f->db->lastId();
		
		$f->query("UPDATE concert SET racineUserInstrumentConcert = '".$idRacine."' WHERE concert = '".$idConcert."' ;");
		
		if(isset($_POST['templateConcert'])) //Chargement à partir du template
		{
			$idRacineTemplate = $_POST["templateConcert"];
			
			$res = $f->query("SELECT lft,rght
								FROM userinstrumentconcert
								WHERE userInstrumentConcert = '".e($idRacineTemplate)."' ;");
			if($row = $res->fetch())
			{
				$lftNewConcert = $rght;
				$rghtNewConcert = $rght +1;
				
				$lftTemplate   = $row["lft"];
				$rghtTemplate  = $row["rght"];
				$base = e($lftNewConcert - $lftTemplate);
				
				$f->query("INSERT INTO userinstrumentconcert (lft,rght,parent_id,concert_id,instrument,user,role,nom)
							SELECT lft + $base ,rght + $base,parent_id,$idConcert,instrument,user,role,nom 
								FROM userinstrumentconcert WHERE lft > '".$lftTemplate."' AND rght < '".$rghtTemplate."' ");
				

					
				$rghtNewConcert += $base;
				$f->query("UPDATE userinstrumentconcert SET rght = '".$rghtNewConcert."' WHERE lft = '".$lftNewConcert."' ;");

				$f->query("CREATE TEMPORARY TABLE temp 
						(SELECT userInstrumentConcert, lft, rght , 
								( SELECT userInstrumentConcert 
								FROM userinstrumentconcert uic2 
								WHERE uic2.lft < uic1.lft  
								AND uic2.rght > uic1.rght 
								ORDER  BY uic2.lft DESC 
								LIMIT 0,1) as parent_id
						FROM userinstrumentconcert uic1
						WHERE uic1.lft > '".$lftNewConcert."' 
						AND uic1.rght < '".$rghtNewConcert."');");
						
				$f->query("UPDATE userinstrumentconcert uic 
							JOIN temp t 
							ON t.userInstrumentConcert = uic.userInstrumentConcert
							SET uic.parent_id = t.parent_id							
							");
			}
		}
		
		
		$f->retourAjax["idInstrument"]	= $idRacine;
		$f->retourAjax["idConcert"]		= $idConcert;
		$f->retourAjax["nomConcert"]	= $nomConcert;
		$f->retourAjax["dateConcert"]	= $dateConcert;
		$f->retourAjax["isTemplate"]	= $isTemplate ? "true": "false";
		$f->ajaxOK("Action effectuée","Le concert <b>&laquo; $nomConcert &raquo;</b> a bien été ajouté",false);
	}elseif($mode == 2) //Modif
	{
		if( stringtodate($dateConcert) == "0000-00-00")
			$f->ajaxErreur("Format invalide","Le format de la date est invalide");
		
		$f->query("UPDATE concert 
					SET dateConcert = '".e(stringtodate($dateConcert))."' ,
					nom = '".e($nomConcert)."', 
					isTemplate = '".($isTemplate ? "1":"0")."'
					WHERE concert = '".e($idConcert)."' ;");
					
		$f->retourAjax["idConcert"]		= $idConcert;
		$f->retourAjax["nomConcert"]	= $nomConcert;
		$f->retourAjax["dateConcert"]	= $dateConcert;
		$f->retourAjax["isTemplate"]	= $isTemplate ? "true": "false";
		$f->ajaxOK("Action effectuée","Le concert <b>&laquo; $nomConcert &raquo;</b> a bien été modifié",false);
	}elseif($mode == 3) //Suppression
	{
		$res = $f->query("SELECT racineUserInstrumentConcert
							FROM concert
							WHERE concert = '".e($idConcert)."'; ");
							
		if(!$row = $res->fetch())
			$f->ajaxErreur("Concert introuvable","Le concert demandé est introuvable");
		
		$res = $f->query("SELECT lft,rght 
							FROM userinstrumentconcert 
							WHERE userInstrumentConcert = '".e($row["racineUserInstrumentConcert"])."' ;");
							
		if($row = $res->fetch())
		{
			$f->query("DELETE FROM userinstrumentconcert
							WHERE lft >= '".$row["lft"]."' AND rght <= '".$row["rght"]."' ;");
		}
		
		$f->query("DELETE FROM concert
							WHERE concert = '".e($idConcert)."'  ;");
		
		$f->retourAjax["idConcert"]		= $idConcert;
		$f->retourAjax["nomConcert"]	= $nomConcert;
		$f->retourAjax["dateConcert"]	= $dateConcert;
		$f->retourAjax["isTemplate"]	= $isTemplate ? "true": "false";
		$f->ajaxOK("Action effectuée","Le concert <b>&laquo; $nomConcert &raquo;</b> a bien été supprimé",false);
	}
}

$f->endAjax();
?>
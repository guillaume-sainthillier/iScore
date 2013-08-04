<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire User",0);
if(isset($_POST['mode']) and isset($_POST['idUser']) and isset($_POST['loginUser']))
{
	
	$mode			= $_POST['mode'];
	$idUser 	= $_POST['idUser'];
	$pwd="";
	if(isset($_POST['pwd'])){
		$pwd 	= $_POST['pwd'];
	}
	$pwd2 ="";
	if(isset($_POST['pwd2'])){
		$pwd2	= $_POST['pwd2'];
	}
	$loginUser 	= $_POST['loginUser'];
	
	if(isset($_POST['nomUser']) and isset($_POST['prenomUser'])){
		$nomUser 	= $_POST['nomUser'];
		$prenomUser 	= $_POST['prenomUser'];
	}
	
	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	$f->retourAjax["mode"] = $mode;
	
	if($mode == 1) //Ajout
	{
		if(isset($_POST['instrument']))
			$instrument=$_POST['instrument'];
		
		if(""==($loginUser) or ""==($pwd) or ""==($pwd2) or ""==$instrument)
			$f->ajaxErreur("ERREUR","Veuillez remplir tout les champs");
			
		if(($_POST['pwd'])!=($_POST['pwd2']))
			$f->ajaxErreur("ERREUR","La confirmation de mot de passe ne correspond pas");
			
		$f->query("INSERT INTO user(login,nom,prenom,rangAdmin,password)
							VALUES('".e($loginUser)."','".e($nomUser)."','".e($prenomUser)."',1,'".e($pwd)."');");			
							
		$idUser = $f->db->lastId();
		if($instrument!="Aucun"){
			$f->query("INSERT INTO competence(user,instrument)
							VALUES('".e($idUser)."','".e($instrument)."');");	
		}
		

		$f->retourAjax["idUser"]		= $idUser;
		$f->retourAjax["nomUser"]	= $nomUser;
		$f->retourAjax["prenomUser"]	= $prenomUser;
		$f->ajaxOK("Action effectuée","Le User <b>&laquo; $nomUser &raquo;</b> a bien été ajouté",false);
	}elseif($mode == 2) //Modif
	{
		
		$f->query("UPDATE user 
					SET 
					nom = '".e($nomUser)."', 
					prenom = '".e($prenomUser)."',
					password = '".e($pwd)."'
					WHERE user = '".e($idUser)."' ;");
					
		$f->retourAjax["idUser"]		= $idUser;
		$f->retourAjax["mode"]		= $mode;
		$f->retourAjax["nomUser"]	= $nomUser;
		$f->retourAjax["prenomUser"]	= $prenomUser;
		$f->ajaxOK("Action effectuée","Le User <b>&laquo; $loginUser &raquo;</b> a bien été modifié",false);
	}elseif($mode == 3) //Suppression
	{
		$res = $f->query("SELECT user
							FROM user
							WHERE user = '".e($idUser)."'; ");
							
		if(!$row = $res->fetch()){
			$f->ajaxErreur("User introuvable","Le User demandé est introuvable");
		}
		else
		{
			$f->query("DELETE FROM user
							WHERE user='".e($idUser)."';");
			$f->query("DELETE FROM competence WHERE user='".e($idUser)."';");
		}
		
		$f->retourAjax["idUser"]	= $idUser;
		$f->retourAjax["loginUser"]	= $loginUser;
		$f->ajaxOK("Action effectuée","Le User <b>&laquo; $loginUser &raquo;</b> a bien été supprimé",false);
	}
}

$f->endAjax();
?>
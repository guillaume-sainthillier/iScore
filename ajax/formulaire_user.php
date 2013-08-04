<?php
require_once "../class/fenajax.class.php";

$f = new fenAjax("Formulaire user",0);

if(isset($_POST['mode']) and isset($_POST['idUser']) and isset($_POST['loginUser']) and isset($_POST['prenomUser']) and isset($_POST['nomUser']))
{

	$mode			= $_POST['mode'];
	$idUser 		= $_POST['idUser'];
	$loginUser 		= $_POST['loginUser'];
	$prenomUser  	= $_POST['prenomUser'];
	$nomUser 		= $_POST['nomUser'];
	
	$idUser 		= $_POST['idUser'];
	
	if( !in_array($mode,array(1,2,3)))
		$f->ajaxErreur("Fonction non supportée","Ce mode n'est pas supporté pour ce module");
	
	if( $loginUser	== "" && $mode!=1)
		$f->ajaxErreur("Problème login","Le login est vide");

	$disable = "";
	
	if($mode == 1)
	{
		$header = "Ajout d'un utilisateur";
		$bouton = "Ajouter l'utilisateur";
	}elseif($mode == 2)
	{
		$disable="disabled='disabled'";
		$res = $f->query("SELECT password
							FROM user
							WHERE user = '".e($idUser)."'; ");
		$tab_pwd=$res->fetch();
		$pwd=$tab_pwd['password'];
		$header = "Modification d'un utilisateur";
		$bouton = "Modifier l'utilisateur";
	}elseif($mode == 3)
	{
		$disable="disabled='disabled'";
		$header = "Suppression d'un utilisateur";
		$bouton = "Supprimer l'utilisateur";
		$disable = "disabled=\"disabled\"";
	}
	$form = "<div id=\"feedback2\"></div><br />".
			($mode == 3? BMInfo("La supression du utilisateur entraîne la suppression de toute les données associée à ce dernier") : "").
			"<form action=\"#\" method=\"post\" onSubmit=\"return actionUser($(this));\">
				<br /><label for=\"loginUser\">Login :</label>
				<input id=\"loginUser\" type=\"text\" size=\"30\" value=\"".s($loginUser)."\" name=\"loginUser\" $disable />
				<br />";
				if($mode !=3){
					$res = $f->query("SELECT * FROM instrument ORDER BY categorie ASC, nom ASC;"); // Garder ORDER BY categorie ASC

					$instrumentsTries = array("-1" => array("nom" => "Divers", "fils" => array()));
				}
				if($mode == 1) //Ajout
				{
					$form.="<br /><label for=\"nomUser\">Nom :</label>
					<input id=\"nomUser\" type=\"text\" size=\"30\" name=\"nomUser\" />
					<br /><label for=\"prenomUser\">Prénom :</label>
					<input id=\"prenomUser\" type=\"text\" size=\"30\"  name=\"prenomUser\" />
					<br /><label for=\"pwd\">Mot de passe :</label>
					<input id=\"pwd\" type=\"text\" size=\"30\"  name=\"pwd\" />
					<br /><label for=\"pwd2\">Confirmation mot de passe :</label>
					<input id=\"pwd2\" type=\"text\" size=\"30\"  name=\"pwd2\" />
					<br /><label for=\"instrument\">Instrument joué :</label>
					<select id='instrument' name='instrument'><option value='Aucun'>Aucun</option>";;
					while($row = $res->fetch())
					{
					
						if($row['categorie']==0){
							$cat=$row['instrument'];
							$form.="<optgroup label=\"".$row['nom']."\"'>";
							$res2 = $f->query("SELECT * FROM instrument WHERE categorie='$cat' ORDER BY categorie ASC, nom ASC;");
							while($row2=$res2->fetch()){
								$form.="<option value='".$row2['instrument']."'>".$row2['nom']."</option>";
							}
							$form.="</optgroup>";
						}
					}
					$form.="</select>";
								
				}else if($mode ==2){
					$form.="<br /><label for=\"nomUser\">Nom :</label>
					<input id=\"nomUser\" type=\"text\" size=\"30\" value=\"".s($nomUser)."\" name=\"nomUser\" />
					<br /><label for=\"prenomUser\">Prénom :</label>
					<input id=\"prenomUser\" type=\"text\" size=\"30\" value=\"".s($prenomUser)."\" name=\"prenomUser\" />
					<br /><label for=\"pwd\">Mot de passe :</label>
					<input id=\"pwd\" type=\"text\" size=\"30\" value=\"".s($pwd)."\" name=\"pwd\" />";
				}
			$form .="<input type=\"hidden\" name=\"mode\" value=\"".$mode."\" />
				<input type=\"hidden\" name=\"idUser\" value=\"".$idUser."\" />
			</form>";

	
	$f->retourAjax["boutton"] = $bouton;
	$f->retourAjax["header"] = $header;	
	$f->retourAjax["erreur"] = false;
	$f->retourAjax["msg"] = $form;
}

$f->endAjax();
?>
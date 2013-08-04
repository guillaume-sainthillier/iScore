<?php
require_once "../class/fenajax.class.php";

$f = newFen(__FILE__,"Concerts",0);

if(! isset($_GET['i'])) // Si pas de concert choisi on charge le plus récent
{
	$res = $f->query("SELECT racineUserInstrumentConcert,nom,dateConcert FROM concert ORDER BY dateConcert DESC LIMIT 0,1;");
	if($row = $res->fetch())
	{
		$idConcert = $row["racineUserInstrumentConcert"];
		$f->titre = "Concert ".$row["nom"]." du ".datetostring($row["dateConcert"]);
	}else
		$idConcert = "";
}else
{
	$idConcert = $_GET['i'];
	$res = $f->query("SELECT nom,dateConcert FROM concert WHERE racineUserInstrumentConcert = '".e($idConcert)."' ;");
	if(!$row = $res->fetch())
		$f->_die("Chef d'orchestre introuvable","Le concert demandé ne possède pas de chef d'orchestre");
	else
		$f->titre = "Concert ".$row["nom"]." du ".datetostring($row["dateConcert"]);
}
	
$f->displayHeader();

echo "<div class=\"center\">
	".BMInfo("Note: Choisissez votre instrument et créer votre concert par glisser déposer",false)."
</div>";


//Gestion des instruments
$res = $f->query("SELECT * FROM instrument ORDER BY categorie ASC, nom ASC;"); // Garder ORDER BY categorie ASC
$instrumentsTries = array("-1" => array("nom" => "Divers", "fils" => array()));
while($row = $res->fetch())
{
	if($row["categorie"] == 0)
	{
		if(!isset($instrumentsTries[$row["instrument"]] )) $instrumentsTries[$row["instrument"]] = array();
		$instrumentsTries[$row["instrument"]]["nom"] = $row["nom"];
		$instrumentsTries[$row["instrument"]]["fils"] = array();
	}else
	{
		$instrumentsTries[$row["categorie"]]["fils"][$row["instrument"]] = $row["nom"];
	}
}

echo "<div><br /><h3 class=\"center\">Liste des instruments</h3><ul id=\"listeCentre\">";
foreach( $instrumentsTries as $idParent => $nomEtFils)
{
	$isCat = true;
	// echo "<li><span id=\"i".$idParent."\" class=\"".($isCat? "folder":"file")."\">".ucfirst($nomEtFils["nom"])."</span>";
	
	// if($isCat)
	// {
		// echo "<ul>";
		// foreach($nomEtFils["fils"] as $idFils => $nomFils)
		// {		
			// echo "<li><span id=\"i".$idFils."\" draggable=\"true\" ondragstart=\"drag(event)\" class=\"file\" >".ucfirst($nomFils)."</span></li>";		
		// }
		// echo "</ul>";
	// }
	// echo "</li>";
	echo "\t<li class=\"rubrik\">\n
						\t\t<h3><a href=\"#\">".ucfirst($nomEtFils["nom"])."</a></h3>\n";         
				if (count($nomEtFils["fils"]) != 0) 
				{                    
					echo "\t\t<div class=\"rubrik\">\n
							\t\t\t<ul class=\"rubrik\">\n";
							foreach($nomEtFils["fils"] as $idFils => $nomFils)
							{
								// echo "\t\t\t\t<li class=\"elem\"><span  id=\"i".$idFils."\" draggable=\"true\" ondragstart=\"drag(event)\" >$nomFils</span></li>\n";
								echo "\t\t\t\t<li class=\"elem\"><span  id=\"i".$idFils."\" >$nomFils</span></li>\n";
							}
						echo "\t\t\t</ul>\n
					\t\t</div>\n";
				}
			echo "\t</li>\n";
}
echo "</ul></div>";


//Gestion du concert courant
$tab = array("1" => array());

$tabNoms = array();
$idRacine = false;
$tab = array();
$res = $f->query("SELECT lft,rght,userInstrumentConcert,nom FROM userinstrumentconcert WHERE userInstrumentConcert = '".e($idConcert)."' ;");
if(!$row = $res->fetch()) // Si aucun concert n'est trouvé
{
	echo "Il n'y a aucun concert disponible<br />
		<a href=\"#historique\" class=\"ui-state-default ui-corner-all button\" id=\"newConcert\"><img src=\"../img/ajouter-32.png\" />Créer un nouveau concert</a>
		<br />
		<br />";
}else
{
	
	$res2 = $f->query("SELECT racineUserInstrumentConcert,nom,concert FROM concert ORDER BY nom ASC;");
	echo "<br /><div class=\"center\">
				Concerts : <select id=\"concertCourant\" ><option value=\"\">Aucun</option>";
				while($row2 = $res2->fetch())
					echo "<option value=\"".$row2["racineUserInstrumentConcert"]."\" ".($row2["racineUserInstrumentConcert"] == $idConcert ? "selected=\"selected\"": "").">".s($row2["nom"])."</option>";
				echo "</select> <input type=\"button\" value=\"Charger\" id=\"loadConcert\"/><br />
				<br />Orientation : <select id=\"changeOrientation\" >
										<option value=\"paysage\">Paysage</option>
										<option value=\"portrait\">Portrait</option>
									</select>
				</div>";
	
	
	$tabNoms[$row['userInstrumentConcert']] = "Chef d'orchestre";
	$res = $f->query("SELECT * FROM userinstrumentconcert WHERE lft >= ".$row['lft']." AND rght <= ".$row['rght']." ORDER BY lft ASC;");
	
	while($row = $res->fetch())
	{
		$tabNoms[$row['userInstrumentConcert']] = $row['nom'];
		if($row['parent_id'] == 0)
			$idRacine = $row['userInstrumentConcert'];
		if(!isset($tab[$row['parent_id']]))
			$tab[$row['parent_id']] = array();
		
		$tab[$row['userInstrumentConcert']] = array();
		$tab[$row['parent_id']][] = $row['userInstrumentConcert'];
	}
}

if(isset($tab[0])) unset($tab[0]);

echo "<br /><div id=\"feedback2\"></div><br /><div id=\"arbreConcert\">".
			construireArbre("",$tab,$idRacine).
		"</div>
		<script type=\"text/javascript\">
			init_concert({noms : ".json_encode($tabNoms)."});
	</script>";


function construireArbre($str, $tab,$idPere)
{
	if(!$idPere) return $str;
	if( count($tab[$idPere]) == 0)
		return $str. "<li id=\"".$idPere."\"></li>";

	$str .= "<ul id=\"".$idPere."\">";
	for($i = 0; $i < count($tab[$idPere]); $i++)
	{
		$str .= construireArbre("",$tab, $tab[$idPere][$i]);
	}
	return $str."</ul>";
}

?>

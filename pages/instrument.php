<?php
require_once "../class/fenajax.class.php";

$f = newFen(__FILE__,"Liste des instruments",0);

$f->displayHeader();	

echo "<script type=\"text/javascript\">
	init_instruments();
</script>
<div class=\"center\">
	".BMInfo("Note: Utilisez le clic droit sur les instruments pour y faire les actions disponibles",false)."<br /><br /><br />
	
	<a href=\"#\" class=\"ui-state-default ui-corner-all button\" id=\"newCat\"><img src=\"../img/ajouter-32.png\" />Créer une nouvelle catégorie</a>
	<a href=\"#\" class=\"ui-state-default ui-corner-all button\" id=\"newInstrument\"><img src=\"../img/ajouter-32.png\" />Créer un nouvel instrument</a>
	<br /><br /><div id=\"retourAction\" ></div>
</div>";


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



echo "<ul id=\"instruments\" class=\"filetree\">";
foreach( $instrumentsTries as $idParent => $nomEtFils)
{
	$isCat = true;
	echo "<li><span id=\"i".$idParent."\" class=\"".($isCat? "folder":"file")."\">".ucfirst($nomEtFils["nom"])."</span>";
	
	if($isCat)
	{
		echo "<ul>";
		foreach($nomEtFils["fils"] as $idFils => $nomFils)
		{
			echo "<li><span id=\"i".$idFils."\" class=\"file\">".ucfirst($nomFils)."</span></li>";
		}
		echo "</ul>";
	}
	echo "</li>";
}
echo "</ul>";


echo "<ul id=\"menuContextuelCategorie\" class=\"contextMenu\">
			<li class=\"ui-state-default\"><span class=\"fleft ui-icon ui-icon-plusthick\">		</span><a href=\"#ajouter\">Ajouter un instrument à cette catégorie</a></li>
			<li class=\"ui-state-default\"><span class=\"fleft ui-icon ui-icon-wrench\">		</span><a href=\"#modifierCat\">Modifier la catégorie</a></li>
			<li class=\"ui-state-default\"><span class=\"fleft ui-icon ui-icon-closethick\">	</span><a href=\"#supprimerCat\">Supprimer la catégorie</a></li>
	</ul>
	<ul id=\"menuContextuelInstrument\" class=\"contextMenu\">
		<li class=\"ui-state-default\"><span class=\"fleft ui-icon ui-icon-wrench\">			</span><a href=\"#modifier\">Modifier l'instrument</a></li>
		<li class=\"ui-state-default\"><span class=\"fleft ui-icon ui-icon-closethick\">		</span><a href=\"#supprimer\">Supprimer l'instrument</a></li>
	</ul>";

	
$f->displayFooter();	
?>
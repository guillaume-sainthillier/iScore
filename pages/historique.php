<?php
require_once "../class/fenajax.class.php";

$f = newFen(__FILE__,"Historiques des concerts",0);

$f->displayHeader();

echo "<script type=\"text/javascript\">
	init_historique();
</script>";


$res = $f->query("SELECT * FROM concert;");

$tableau = "<div class=\"center\" id=\"feedback\"></div><br />
			<table class=\"tab-tab\">
				<tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">
					<th>
						<a href =\"#ajouter\">
							<img src=\"../img/ajouter-32.png\"  title = \"Créer un nouveau concert\" id=\"newConcert\" class=\"pointeur\" />
						</a>
					</th>
					<th>Nom</th>
					<th>Date</th>
					<th>Concert de configuration</th>
				</tr>";
				$odd = true;

				while($row = $res->fetch())
				{
						
					$tableau.="<tr class=\"tab-data ".($odd ? "odd" : "even")."\" >
									<td>
										<a class=\"editConcert\" id=\"m".$row["concert"]."\" href =\"#modifier\"><img src=\"../img/configure-32.png\"  alt=\"Modifier\" title=\"Modifier le concert\"/></a>
										<a class=\"delConcert\" id=\"s".$row["concert"]."\" href =\"#supprimer\"><img src=\"../img/supprimer-32.png\"  alt=\"Supprimer\" title=\"Supprimer le concert\"/></a>
									</td>
									<td>
										<a title=\"Accéder au concert\" class=\"load\" href=\"#concert?i=".$row["racineUserInstrumentConcert"]."\">".$row["nom"]."</a>
									</td> 
								    <td>".datetostring($row["dateConcert"])."</td>
									<td>".($row["isTemplate"] ? "Oui" : "Non")."</td>
							   </tr>";
					$odd = !$odd;
				}

$tableau .= "</table>";

echo $tableau;

$f->displayFooter();
?>

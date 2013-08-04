<?php
require_once "../class/fenajax.class.php";

$f = newFen(__FILE__,"Utilisateurs",0);

$f->displayHeader();
echo "<script type=\"text/javascript\">
	init_users();
</script>";


$res = $f->query("SELECT * FROM user ORDER BY login;");


$tableau = "<table class=\"tab-tab\">
				<tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">
					<th>
						<a class=\"addUser\" href =\"#ajouter\"><img src=\"../img/ajouter.png\"  title = \"Ajouter un utilisateur\" id=\"ajouter\"  /></a>
					</th>
					<th>Login</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Instruments</th>
				</tr>";
				$odd = true;

				while($row = $res->fetch())
				{	
					$user=$row['user'];
					$res2 = $f->query("SELECT * FROM competence WHERE user='$user';");
					$row2= $res2->fetch();
					$instru=$row2["instrument"];
					$res2 = $f->query("SELECT * FROM instrument WHERE instrument='$instru';");
					$row2= $res2->fetch();
					$tableau.="<tr class=\"tab-data ".($odd ? "odd" : "even")."\" >
									<td>
										<a class=\"editUser\" id=\"m".$row["user"]."\" href =\"#modifier\"><img src=\"../img/modifier.png\"  alt=\"Modifier\" title=\"Modifier cet utilisateur\"/></a>
										<a class=\"delUser\" id=\"m".$row["user"]."\" href =\"#supprimer\"><img src=\"../img/supprimer.png\"  alt=\"Supprimer\" title=\"Supprimer cet utilisateur\"/></a> 
									<td>".$row["login"]."</td> 
								    <td>".$row["nom"]."</td>
									<td>".$row["prenom"]."</td>
									<td>".$row2["nom"]."</td>
							   </tr>";
							   //<a class=\"editInstruUser\" id=\"m".$row["user"]."\" href =\"#instru\"><img src=\"../img/icone_oeil.gif\"  alt=\"Modifier\" title=\"Gérer les instruments de cet utilisateur\"/></a>
					$odd = !$odd;
				}

$tableau .= "</table>";

echo $tableau;


$f->displayFooter();
?>






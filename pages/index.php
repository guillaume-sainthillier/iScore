<?php
require_once "../class/fenajax.class.php";

$f = newFen(__FILE__,"Bienvenue sur iScore",0);

$f->displayHeader();


echo "<div class=\"center\">".BMInfo(ucfirst(utf8_encode(strftime("%A %d %B %Y"))),false)."</div><br /><br />";
?>

<h3>Bienvenue sur iScore</h3><br />

<br />
Sur cette interface, vous pouvez gérer les <a class="load" href="#user">utilisateurs</a>. <br /><br />
Mais aussi accéder aux <a class="load" href="#historique">concerts crées</a>, ou en créer de nouveau.<br />
<br /> Vous pouvez également gérer les <a class="load" href="#instrument">instruments de musiques</a>.

<?php

$f->displayFooter();

?>
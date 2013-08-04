<?php

require_once "../class/fenloader.class.php";

$f = new fenLoader(0);

if(isset($_POST['page']))
{
	$page = $_POST['page'];
	$f->inclureFichier($page);
}

$f->endAjax();
?>
<?php

require_once "../class/fenajax.class.php";

class fenLoader extends fenAjax
{

	/**
		fenAjax : Initialise un objet ouvrant une page PHP
			- rangAdmin	: rang d'administration minimum pour accéder à cette page
	**/
	public function __construct($rangAdmin = 0)
	{
		parent::__construct("FenLoader",$rangAdmin);
	}
	
	/**
		inclureFichier	: Effectue les vérifications et inclut le fichier $page dans un contexte de communication Ajax
			- page		: page à inclure ( Ex: page.php?foo=bar&boo=ba )
	**/
	public function inclureFichier($page)
	{
		if(!preg_match("/^#([\d\w]{0,})/",$page))
			$this->ajaxErreur("Format invalide","Le format $page de la page que vous demandez est invalide");
		else
		{
			$page = substr($page,1,strlen($page));
			if(trim($page) == "")
				$page = "index";
				
				
			$params = preg_split("/(\?|&)/i",$page);
			if(count($params) > 1)
			{
				$page = $params[0];
				for($i = 1;$i < count($params);$i++)
				{
					$post = preg_split("/=/i",$params[$i]);
					if(count($post) > 1)
					{
						$_GET[$post[0]] = $post[1];
					}
				}
			}
			
			$fichier = "../pages/".$page.".php";
			if(!file_exists($fichier) or !is_file($fichier))
			{
				$this->ajaxErreur("Erreur 404","L'URL que vous demandez n'est pas ou plus disponible");
			}else
			{
				$html = ob_get_contents();
				ob_end_clean();
				ob_start();
				include $fichier;
				$contenuFichier = ob_get_contents();
				ob_end_clean();
				$this->retourAjax["erreur"] = false;
				$this->retourAjax["msg"] = $html.$contenuFichier;
				if(isset($f))
					$this->retourAjax["header"] = $f->titre;
			}
		}
	}
}
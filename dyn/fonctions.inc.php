<?php

	/**
		BMOK : Retourne le code HTML d'un message de confirmation de bon fonctionnement
						- message	: Contenu du message
						- fullWidth	: Vrai par d�faut, n'affiche pas le message d'erreur sur toute sa longueur si faux
	**/
	function BMOK($message, $fullWidth = true)
	{
		return "<div class=\"message ui-widget ui-corner-all ui-state-highlight ui-state-valid bordervert ".(!$fullWidth ? "displayib": "")."\" >
		<span class=\"ui-icon ui-icon-circle-check\"></span>
			<span class=\"text\">".$message."</span>
		</div>";
	}

	/**
		BMInfo : Retourne le code HTML d'un message d'information
						- message	: Contenu du message
						- fullWidth	: Vrai par d�faut, n'affiche pas le message d'erreur sur toute sa longueur si faux
	**/
	function BMInfo($message, $fullWidth = true)
	{
		return "<div class=\"message ui-widget ui-corner-all ui-state-default ui-state-valid ".(!$fullWidth ? "displayib": "")."\" >
		<span class=\"ui-icon ui-icon-info\"></span>
			<span class=\"text\">".$message."</span>
		</div>";
	}
	
	/**
		BMErreur : Retourne le code HTML d'un message d'erreur
						- message : Contenu du message
						- fullWidth : Vrai par d�faut, n'affiche pas le message d'erreur sur toute sa longueur si faux
	**/
	function BMErreur($message, $fullWidth = true)
	{
		return "<div class=\"message ui-widget ui-corner-all ui-state-error ".(!$fullWidth ? "displayib": "")."\" >
		<span class=\"ui-icon ui-icon-alert\" ></span>
			<span class=\"text\">".$message."</span>
		</div>";
	}
	
	/**
		e : Retourne un string s�curis� pour une requ�te en base de donn�e
				- string : Variable � prot�ger (type entier, string)
	**/
	function e($string)
	{
		// On regarde si le type de string est un nombre entier (int)
		if(ctype_digit($string))
		{
			$string = intval($string);
		}
		// Pour tous les autres types
		else
		{
			$string = trim($string);
			$string = mysql_real_escape_string($string);
		}		
		return $string;
	}
	
	/**
		s : Retourne un string format� pour l'affichage HTML 
				- string : Variable � formater
	**/
	function s($string)
	{
		$string = htmlspecialchars_decode($string);
		return str_replace('\_','_',htmlspecialchars($string));

	}

	/**
		datetostring : Retourne une date au format anglais
							- texte : Date au format fran�ais (DD/MM/YYYY)
	**/
	function datetostring($texte)
	{
		if($texte == "0000-00-00") return "";
		return preg_replace('#([0-9]{4})-([0-9]{2})-([0-9]{2})#isU','$3/$2/$1',$texte);
	}
	
	/**
		stringtodate : Retourne une date au format fran�ais
							- texte : Date au format anglais (YYYY-MM-DD)
	**/
	function stringtodate($texte)
	{
		if($texte == "") return "0000-00-00";
		return preg_replace('#([0-9]{2})/([0-9]{2})/([0-9]{4})#isU','$3-$2-$1',$texte);
	}
	
	
	/**
		newFen : retourne un objet fen�tre correspondant au contexte demand�
						- fichier: Chemin du fichier appelant cette m�thode
						- titre  		: Titre de la fen�tre
						- randMinAdmin	: rang d'administration minimum pour acc�der � cette page
	
	**/
	function newFen($fichier, $titre, $randMinAdmin = 0)
	{
		$buf = get_included_files();
		$isInclut = ($buf[0] == $fichier);
		
		if(!$isInclut)
			return new fenAjax($titre,$randMinAdmin);
		else
			return new fen($titre,$randMinAdmin);
			
	}

?>
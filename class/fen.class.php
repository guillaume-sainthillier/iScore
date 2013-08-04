<?php

require_once "../class/db.class.php";
require_once "../dyn/fonctions.inc.php";

class fen
{
	protected $charset; //Format d'encodage de la page
	protected $mode; // Mode de développement (PRODUCTION, VERBOSE, DEBUG)
	protected $rangMinAdmin;// Rang minimum d'administration pour consulter la page
	protected $theme; // Theme CSS JQuery UI
	
	public $titre;// Titre de la page
	public $db; //Objet base de données
	public $nomSite; // Nom du site
	
	
	/**
		fen : Initialise un objet fenêtre 
			- titre: Titre de la page
			- rangAdmin: rang d'administration minimum pour accéder à cette page
	**/
	public function __construct($titre, $rangAdmin = 0)
	{
		ob_start();
		setlocale(LC_TIME, 'fr_FR','fra');
		$this->titre = $titre;
		$this->charset = "utf-8";
		$this->mode = "DEBUG";
		$this->nomSite = "iScore";
		$this->theme = "redmond";
		$this->rangMinAdmin = $rangAdmin;
		require_once "../dyn/session.inc.php";

		$this->_initFlux();
		$this->_gestionBD();
	}


/**


	Fonctions d'affichage


**/


	/**
		displayHeader() : Vérifie les droits d'authentification et affiche le contenu HTML de l'entête si flag à html
	**/
	public function displayHeader()
	{
		if(! $this->verifDroits() )
			$this->_die("Droits insuffisants","Vous ne possédez pas les droits nécéssaires pour effectuer cette action");

		$html = ob_get_contents();
		if($html != "")
		{
			ob_end_clean();
			ob_start();
			$this->_die("Une erreur est survenue",$html);
		}
		
		echo "<!DOCTYPE html 
		PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" 
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n
		<html xmlns=\"http://www.w3.org/1999/xhtml\">\n

		<head>\n
			\t<meta http-equiv=\"Content-Type\" 
			content=\"text/html;charset=".$this->charset."\" />\n
			<meta name=\"description\" content=\"".$this->nomSite." est un logiciel de fou\" />\n
			<meta name=\"keywords\" content=\"".$this->nomSite.", musique, logiciel, instrument, classique, gratuit\" />\n
			<meta http-equiv=\"Content-Language\" content=\"fr\" />\n
				\t<title>".($this->nomSite)." ~ ".($this->titre)."</title>\n
				\t<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"../favicon.ico\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/".$this->theme."/jquery-ui-1.10.0.min.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/jquery.treeview.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/jquery.contextMenu.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/jquery.arbre.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/jquery.multiselect.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/jquery.multiselect.filter.css\" />\n
				\t<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"../css/style.css\" />\n
				\t<script type=\"text/javascript\" src=\"../js/jquery-1.8.3.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery-ui-1.10.0.min.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/json.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.ui.datepicker-fr.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.treeview.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.contextMenu.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.svg.min.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.svganim.min.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.arbre.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.multiselect.min.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/jquery.multiselect.filter.min.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/scripts.js\" ></script>\n
				\t<script type=\"text/javascript\" src=\"../js/concert.js\" ></script>\n							
		</head>\n
		<body>\n";
		
		$this->displayTopMenu();
		$this->displayMenuGauche();
		
		
		echo "<div id=\"contenu\" class=\"ui-widget ui-widget-content ui-corner-all\" >\n
				\t<div class=\"titre center ui-state-active ui-corner-all\">\n
					\t<img src=\"../img/zoom-plus.png\" class=\"imageZoom plus\" alt=\"Image de zoom\" title=\"Passer en mode plein écran\" id=\"zoom\"/>\n
					\t<span id=\"headerTitre\">".$this->titre."</span>\n
				\t</div>\n<br />
				<div id=\"corpsContenu\">";
		
	}
	
	/**
		displayTopMenu: Affiche le tableau de bord nord
	**/
	public function displayTopMenu()
	{
		echo "<div id=\"topMenu\" class=\"ui-widget ui-widget-header ui-corner-all center\" >
			Interface Administrateur
		</div>";
	}
	
	/**
		displayMenuGauche: Affiche le menu gauche
	**/
	public function displayMenuGauche()
	{
		$menu = array();
		$menu["Menu"] = array("<a href=\"#\" class=\"load accueil-32\" >Accueil</a>",
								"<a href=\"#concert\" class=\"load concert-32\" >Concerts</a>",
								"<a href=\"#historique\" class=\"load sablier-32\" >Historique des concerts</a>",
								"<a href=\"#instrument\" class=\"load instrument-32\" >Instruments</a>",
								"<a href=\"#user\" class=\"load users-32\" >Utilisateurs</a>",
								"<a href=\"http://www.google.fr\" class=\"deconnexion-32\" >Se déconnecter</a>"
								);
					
		
		echo "<ul id=\"menuGauche\">\n";		
			foreach ($menu as $nom => $rubrique)
			{                
				echo "\t<li class=\"rubrik\">\n
						\t\t<h3><a href=\"#\">".$nom."</a></h3>\n";         
				if (count($rubrique) != 0) 
				{                    
					echo "\t\t<div class=\"rubrik\">\n
							\t\t\t<ul class=\"rubrik\">\n";
							foreach ($rubrique as $lien) 
							{
								echo "\t\t\t\t<li class=\"elem\">$lien</li>\n";
							}
						echo "\t\t\t</ul>\n
					\t\t</div>\n";
				}
				echo "\t</li>\n";
			}
		echo "</ul>\n";
	}
	
	/**
		displayFooter : Affiche le pied de page
	**/
	public function displayFooter()
	{
			echo "</div>
				</div>
				<div id=\"footer\" class=\"ui-widget center\">
					<span class=\"ui-corner-all ui-widget-content displayib\">
							<img src=\"../img/w3c.png\" alt=\"Valid XHTML 1.0 Transitional\" />
							<span class=\"texteFooter\">".(date("Y"))." - ".$this->nomSite." version 1.0</span>
					</span>
				</div>
			</body>\n
			</html>";
	}


/**


	Fonctions d'ajax


**/


	/**
		initAjax : Démarre la tamporisation et initialise le tableau de communication Ajax avec 3 valeurs par défaut : 
						erreur : Booléen representant l'état de la transaction ( si erreur ou non )
						msg : Message à retourner au client
						header: Entête à retourner au client
	**/
	public function initAjax()
	{ 
		ob_start();
		$this->retourAjax = array("erreur" => false,
									"msg" => "",
									"header" => $this->titre
		);
	}

/**


	Fonctions générales


**/


	/**
		verifDroits : Retourne vrai si l'utilisateur peut consulter la page, faux sinon
	**/
	public function verifDroits()
	{
		return $this->rangMinAdmin <= $_SESSION['admin'];
	}

	/**
		query : Effectue une requête SQL(MySQL) et fait la gestion des erreurs
				- sql : Requête SQL à traiter
				- afficherRequete : Faux par défaut, affiche la requête passée en paramètre si vrai
	**/
	public function query($sql, $afficherRequete = false)
	{
		if($afficherRequete or $this->mode == "VERBOSE")
			echo BMInfo("La requête <b>".$sql."</b> est exécutée",false)."<br />";
		$res = $this->db->query($sql);
		if(!$res)
		{
			$this->_die("Erreur SQL",($this->mode != "PROD" ? BMInfo("Requête : $sql",false)." <br />":"" ).$this->db->lastError);
		}
		
		return $res;
	}

/**


	Fonctions internes


**/


	/**
		initFlux : Initialise la gestion des erreurs
	**/
	private function _initFlux()
	{
		if($this->mode == "PROD")
			error_reporting(0);
		else
			error_reporting(E_ALL);
	}
	
	/**
		gestionBD : Initialise la connection à la base de données
	**/
	private function _gestionBD()
	{
		try
		{
			$this->db = new DB();
		}catch ( Exception $e ) 
		{
			$this->_die("Erreur de connexion à la base de données",$e->getMessage());
		}
	}
	
	
	/**
		_die : Arrête tout traitement et affiche la page avec un message d'erreur et une entête
				- entente : Titre de l'erreur
				- msg : Détail de l'erreur
	**/
	public function _die($entete, $msg)
	{
		ob_end_clean();
		$this->titre = $entete;
		$this->displayHeader();
		echo $msg;
		$this->displayFooter();
		die();
	}
	
	/**
		_rewriteURL : Remplace tous les liens internes de la page en accord avec le .htaccess
	**/
	protected function _rewriteURL() // TODO 
	{
		$html = ob_get_contents();
		ob_end_clean();

		echo $html;
	}
}

?>
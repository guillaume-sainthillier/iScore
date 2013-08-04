

/**

	Fonctions appelées systématiquement
	
**/

	
	base = "iScore ~ ";
	
	$(document).ready(function()
	{	
		$("#menuGauche").accordion({
			autoHeight: false,
			collapsible: true
		});
		init_composants();
		$("#zoom").click(function(e) {  
			zoom($(this));
			return false;
		});
		var url = document.URL;
		if( url.match(/#\w([\w\d]{1,})/) )
		{
			var pages = url.split("#");
			var page = "#"+pages[pages.length-1];
			load_contenu(page);
		}
	});

	/*
		Adapte les boutons et liens à jQuery UI
		Initialise également le chargement des pages sur les liens "<a>" de classe "load"
	*/
	function init_composants()
	{
		$(document).ready(function()
		{	
			$("button, input:button, input:submit").button();
			$("a.load").unbind("click").click(function(e)
			{
				load_contenu($(this).attr("href"),$(this));
				return true;
				e.preventDefault();
			});
			
			$("a.button").unbind("hover").hover(function() {
					$( this ).addClass( "ui-state-hover" );
				},function() {
					$( this ).removeClass( "ui-state-hover" );
			});
			
			$("#menuConcert").remove();
		});
	}
	
	
/**

	Fonctions de la page utilisateur

**/
	
	
	/*
		TODO
	*/
	function init_users()
	{
		$(document).ready(function()
		{
			$("#ajouter").click(function() { 
				formulaireUser(1);
				return false;
			});
		});
		maj_boutons_user();
	}
	
	/*
		maj_boutons_concert : Initialise les actions des boutons modifier/supprimer (permet d'en ajouter à la volée également)
									-selecteurModif : Selecteur jQuery du/des boutons modifier
									-selecteurSupp  : Selecteur jQuery du/des boutons supprimer
	*/
	function maj_boutons_user(selecteurModif, selecteurSupp)
	{
		$(selecteurModif || ".editUser").unbind().click(function(e)
		{
			var id = $(this).attr("id");
			id = id.substring(1,id.length);
			formulaireUser(2,id,$(this).parent().next().html(),$(this).parent().next().next().html(),$(this).parent().next().next().next().html());
			return false;
		});
		
		$(selecteurSupp || ".delUser").unbind().click(function(e)
		{
			var id = $(this).attr("id");
			id = id.substring(1,id.length);
			formulaireUser(3,id,$(this).parent().next().html(),$(this).parent().next().next().html(),$(this).parent().next().next().next().html());
			return false;
		});
		
		$(selecteurSupp || ".editInstruUser").unbind().click(function(e)
		{
			window.location.reload();
			return false;
		});
		
	}
	
	/*
		FormulaireUser : ouvre une boîte de dialogue contenant le formulaire de CRUD d'un User
		- mode : Action voulue (1 : ajouter un User, 2 : modifier un User, 3 : supprimer un User )
		- loginUser 	: False par défaut, Nom du User 	("" si aucun)

	*/
	function formulaireUser(mode,idUser,loginUser,nomUser,prenomUser)
	{
		var bd = creerBD("Chargement en cours...", "<img src=\"../img/loading.gif\" /> Veuillez patienter",null,"500px");
		$.post("../ajax/formulaire_user.php",
				{"mode" : mode, "idUser" : (idUser || ""), "loginUser": (loginUser || ""),"nomUser": (nomUser || ""),"prenomUser": (prenomUser || "")}
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			$("#bd").dialog("option","title",retour["header"]);
			$("#bd").html(retour["msg"]);
			$(".datepicker",$("#bd")).datepicker();
			if(!retour["erreur"])
			{
				var bouttons = {};
				bouttons[retour["boutton"]] =  function() {
					$("#bd form").submit();
				};
				bouttons["Annuler"] = function() {
					$("#bd").dialog("close");
					$("#bd").dialog("close");
				};
				
				$("#bd").dialog("option","buttons",bouttons);
				
			}
		});
	}
	
	/*
		actionInstrument : Appelée après formulaireConcert, envoie le formulaire pour traitement au serveur et met à jour le tableau des concerts au client
		- form : Objet jQuery contenant le formulaire 
	*/
	function actionUser(form)
	{
		$("#feedback2").html("<img src=\"../img/loading.gif\" alt=\"\" />Veuillez patienter ... ");
		$(".ui-dialog-buttonset button").button("disable");
		
		var disabled = form.find(':disabled').removeAttr('disabled');
		var datas = form.serialize();
		disabled.attr('disabled','disabled');
		
		$.post("../ajax/user.php",
				datas
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			if(retour["erreur"])
			{
				$("#bd").dialog("option","title",retour["header"]);
				$("#feedback2").html(retour["msg"]);
				$(".ui-dialog-buttonset button").button("enable");
			}else
			{
				$("#bd").dialog("destroy");
				$("#bd").remove();
				
				$("#feedback").hide().html(retour["msg"]);
				$("#feedback").slideDown("slow");
				if(retour["mode"] == 1)//Ajout
				{
					window.location.reload();
				}else if(retour["mode"] == 2) //Modif
				{
					var tr = $("#m"+retour["idUser"]).parent().parent();		
					var tds = tr.find("td");
					$(tds[1]).find("a").html(retour["loginUser"]);
					$(tds[2]).html(retour["nomUser"]);
					$(tds[3]).html(retour["prenomUser"]);
				}else if(retour["mode"] == 3) //Suppression
				{
					var tr = $("#m"+retour["idUser"]).parent().parent();		
					tr.remove();
					window.location.reload();
				}
			}
		});
		return false;
	}
	
	
/**

	Fonctions de la page instrument

**/

	
	/*
		init_instrument:  Initialise les menu contextuels ainsi que l'arbre des instruments
	*/
	function init_instruments()
	{
		$(document).ready(function()
		{
			menuContextuel("menuContextuelCategorie","#instruments span.folder");
			menuContextuel("menuContextuelInstrument","#instruments span.file");
			$("#instruments").treeview();
			$("#newCat").click(function(e){formulaireInstrument(4,"","","");return false;});
			$("#newInstrument").click(function(e){formulaireInstrument(1,"","","");return false;});
		});
	}
	
	/*
		FormulaireInstrument : ouvre une boîte de dialogue contenant le formulaire de CRUD d'un instrument/catégorie
		- mode : Action voulue (1 : ajouter un instrument, 2 : modifier un instrument, 3 : supprimer un instrument, ... )
		- idInstrument: ID de l'instrument à manipuler
		- idCat : ID de la catégorie de l'instrument (0 si aucune)
		- nomInstrument: Nom de l'instrument
	*/
	function formulaireInstrument(mode, idInstrument, idCat, nomInstrument)
	{
		var bd = creerBD("Chargement en cours...", "<img src=\"../img/loading.gif\" /> Veuillez patienter",null,"500px");
		$.post("../ajax/formulaire_instrument.php",
				{"mode" : mode, "idCat": idCat, "idInstrument": idInstrument, "nomInstrument" : nomInstrument}
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			$("#bd").dialog("option","title",retour["header"]);
			$("#bd").html(retour["msg"]);
			
			if(!retour["erreur"])
			{
				var bouttons = {};
				bouttons[retour["boutton"]] =  function() {
					$("#bd form").submit();
				};
				bouttons["Annuler"] = function() {
					$("#bd").dialog("close");
				};
				
				$("#bd").dialog("option","buttons",bouttons);
				
			}
		});
	}
	
	

	/*
		actionInstrument : Appelée après formulaireInstrument, envoie le formulaire pour traitement au serveur et met à jour l'arbre des instruments du client
		- form : Objet jQuery contenant le formulaire 
	*/
	function actionInstrument(form)
	{
		$("#feedback").html("<img src=\"../img/loading.gif\" alt=\"\" />Veuillez patienter ... ");
		$(".ui-dialog-buttonset button").button("disable");
		
		var disabled = form.find(':disabled').removeAttr('disabled');
		var datas = form.serialize();
		disabled.attr('disabled','disabled');
		
		$.post("../ajax/instrument.php",
				datas
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			if(retour["erreur"])
			{
				$("#bd").dialog("option","title",retour["header"]);
				$("#feedback").html(retour["msg"]);
				$(".ui-dialog-buttonset button").button("enable");
			}else
			{
				$("#bd").dialog("destroy");
				$("#bd").remove();
				$("#retourAction").hide().html(retour["msg"]);
				$("#retourAction").slideDown("slow");
				
				if(retour["mode"] % 3 == 1)
					ajouter_instrument_treeview(retour["idInstrument"],retour["idCat"],retour["nomInstrument"]);
				else if(retour["mode"] % 3 == 2)
					modifier_instrument_treeview(retour["idInstrument"],retour["idCat"],retour["nomInstrument"]);
				else if(retour["mode"] % 3 == 0)
					supprimer_instrument_treeview(retour["idInstrument"],retour["idCat"]);
			
				menuContextuel("menuContextuelCategorie","#instruments span.folder");
				menuContextuel("menuContextuelInstrument","#instruments span.file");
			}
		});
		return false;
	}

	/*
		modifier_instrument_treeview : Modifie un instrument/catégorie dans l'arbre treeview jQuery
		- idInstrument: ID de l'instrument à modifier
		- idCat : ID de la catégorie de l'instrument (0 si aucune)
		- nomInstrument: Nom de l'instrument
	*/
	function modifier_instrument_treeview(idInstrument,idCat,nomInstrument)
	{
		idCat = parseInt(idCat,10);
		if(idCat == 0)
		{
			$("#i"+idInstrument).html(nomInstrument);
		}else
		{
			if($("#i"+idInstrument).parent().parent().parent().find(".folder").attr("id") != ("i"+idCat))
			{
				supprimer_instrument_treeview(idInstrument,"0");
				ajouter_instrument_treeview(idInstrument,idCat,nomInstrument);
			}else
			{
				$("#i"+idInstrument).html(nomInstrument);
			}
		}
	}

	/*
		ajouter_instrument_treeview : Ajoute un instrument/catégorie dans l'arbre treeview jQuery
		- idInstrument: ID de l'instrument à ajouter
		- idCat : ID de la catégorie de l'instrument (0 si aucune)
		- nomInstrument: Nom de l'instrument
	*/
	function ajouter_instrument_treeview(idInstrument,idCat,nomInstrument)
	{
		idCat = parseInt(idCat,10);
		if(idCat == 0)
		{
			var lien = $("<li><div class=\"hitarea collapsable-hitarea\"></div><span id=\"i"+idInstrument+"\" class=\"folder\" >"+nomInstrument+"</span><ul></ul></li>").prependTo($("#instruments"));
			$("#instruments").treeview({ add: lien });
		}else
		{
			if($("#i"+idCat) && $("#i"+idCat).next().length)
			{
				$("#i"+idCat).next().find(".last").removeClass("last");
				$("#i"+idCat).next().append("<li class=\"last\"><span class=\"file\" id=\"i"+idInstrument+"\">"+nomInstrument+"</span></li>");
			}
		}
	}

	/*
		supprimer_instrument_treeview : Supprime un instrument/catégorie dans l'arbre treeview jQuery
		- idInstrument: ID de l'instrument à supprimer
		- idCat : ID de la catégorie de l'instrument (0 si aucune),  supprime également tous les instruments associés 
		- nomInstrument: Nom de l'instrument
	*/
	function supprimer_instrument_treeview(idInstrument,idCat)
	{
		idCat = parseInt(idCat,10);
		idInstrument = parseInt(idInstrument,10);
		if(idCat == 0)
		{
			if(idInstrument == -1)
				$("#i"+idInstrument).next().html("");
			else
				$("#i"+idInstrument).parent().remove();
		}else
		{
			var ul = $("#i"+idInstrument).parent().parent();
				$("#i"+idInstrument).parent().remove();
				if(! ul.find(".last").length)
					ul.find("li:last-child").addClass("last");
		}
	}


/**

	Fonctions de la page historique
	
**/

	/*
		init_historique : Initialise les boutons ajouter/modifier/supprimer du tableau de l'historique des concert
	*/
	function init_historique()
	{
		$(document).ready(function()
		{			
			$("#newConcert").click(function(e)
			{
				formulaireConcert(1,false,false,false,false);
				return false;
			});
			maj_boutons_concert();
		});
	}
	
	/*
		maj_boutons_concert : Initialise les actions des boutons modifier/supprimer (permet d'en ajouter à la volée également)
									-selecteurModif : Selecteur jQuery du/des boutons modifier
									-selecteurSupp  : Selecteur jQuery du/des boutons supprimer
	*/
	function maj_boutons_concert(selecteurModif, selecteurSupp)
	{
		$(selecteurModif || ".editConcert").unbind().click(function(e)
		{
			var id = $(this).attr("id");
			id = id.substring(1,id.length);
			formulaireConcert(2,id,
							$(this).parent().next().find("a").text(),
							$(this).parent().next().next().html(),
							($(this).parent().next().next().next().html().toLowerCase() == "oui"));
			return false;
		});
		
		$(selecteurSupp || ".delConcert").unbind().click(function(e)
		{
			var id = $(this).attr("id");
			id = id.substring(1,id.length);
			formulaireConcert(3,id,
							$(this).parent().next().find("a").text(),
							$(this).parent().next().next().html(),
							($(this).parent().next().next().next().html().toLowerCase() == "oui"));
			return false;
		});
	}
	
	/*
		FormulaireConcert : ouvre une boîte de dialogue contenant le formulaire de CRUD d'un concert
		- mode : Action voulue (1 : ajouter un concert, 2 : modifier un concert, 3 : supprimer un concert )
		- idConcert		: False par défaut, ID du concert à manipuler ("" si aucun)
		- nomConcert 	: False par défaut, Nom du concert 	("" si aucun)
		- dateConcert 	: False par défaut, Date du concert ("" si aucun)
		- nomConcert 	: False par défaut, Nom du concert 	("" si aucun)
		- isTemplate 	: False par défaut, booléen 		(false si aucun)

	*/
	function formulaireConcert(mode, idConcert, nomConcert, dateConcert, isTemplate)
	{
		var isTemplate = !(typeof isTemplate=="null" || (typeof isTemplate!="null" && isTemplate == false));
		var bd = creerBD("Chargement en cours...", "<img src=\"../img/loading.gif\" /> Veuillez patienter",null,"500px");
		$.post("../ajax/formulaire_concert.php",
				{"mode" : mode, "idConcert": (idConcert || ""), "dateConcert": (dateConcert || ""), "nomConcert" : (nomConcert || ""), "isTemplate" : isTemplate || "false"}
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			$("#bd").dialog("option","title",retour["header"]);
			$("#bd").html(retour["msg"]);
			$(".datepicker",$("#bd")).datepicker();
			if(!retour["erreur"])
			{
				var bouttons = {};
				bouttons[retour["boutton"]] =  function() {
					$("#bd form").submit();
				};
				bouttons["Annuler"] = function() {
					$("#bd").dialog("close");
				};
				
				$("#bd").dialog("option","buttons",bouttons);
			}
		});
	}
	
	
	/*
		actionInstrument : Appelée après formulaireConcert, envoie le formulaire pour traitement au serveur et met à jour le tableau des concerts au client
		- form : Objet jQuery contenant le formulaire 
	*/
	function actionConcert(form)
	{
		$("#feedback2").html("<img src=\"../img/loading.gif\" alt=\"\" />Veuillez patienter ... ");
		$(".ui-dialog-buttonset button").button("disable");
		
		var disabled = form.find(':disabled').removeAttr('disabled');
		var datas = form.serialize();
		disabled.attr('disabled','disabled');
		
		$.post("../ajax/concert.php",
				datas
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			if(retour["erreur"])
			{
				$("#bd").dialog("option","title",retour["header"]);
				$("#feedback2").html(retour["msg"]);
				$(".ui-dialog-buttonset button").button("enable");
			}else
			{
				$("#bd").dialog("destroy");
				$("#bd").remove();
				
				$("#feedback").hide().html(retour["msg"]);
				$("#feedback").slideDown("slow");
				if(retour["mode"] == 1)//Ajout
				{
					var trs = $(".tab-tab").find("tr");
					var odd = ($(trs).length <= 1 || ($(trs).length > 1 && $(trs[1]).hasClass("even")));
					$(trs[0]).after($("<tr class=\"tab-data "+(odd ? "odd" : "even")+"\">"+
											"<td>"+
												"<a id=\"m"+retour["idConcert"]+"\" href=\"#modifier\"><img title=\"Modifier le concert\" alt=\"Modifier\" src=\"../img/configure-32.png\"></a>"+
												"<a id=\"s"+retour["idConcert"]+"\" href=\"#supprimer\"><img title=\"Supprimer le concert\" alt=\"Supprimer\" src=\"../img/supprimer-32.png\"></a>"+
											"</td>" + 
											"<td>" + 
												"<a title=\"Accéder au concert\" onClick=\"return load_contenu('#concert?i="+retour["idConcert"]+"',$(this));\">"+retour["nomConcert"]+"</a>"+
											"</td>" +
											"<td>" + 
												retour["dateConcert"]+
											"</td>" +	
											"<td>" + 
												(retour["isTemplate"] == "true" ? "Oui":"Non")+
											"</td>" +
										"</tr>"));
					maj_boutons_concert("#m"+retour["idConcert"],"#s"+retour["idConcert"]);
				}else if(retour["mode"] == 2) //Modif
				{
					var tr = $("#m"+retour["idConcert"]).parent().parent();		
					var tds = tr.find("td");
					$(tds[1]).find("a").html(retour["nomConcert"]);
					$(tds[2]).html(retour["dateConcert"]);
					$(tds[3]).html(retour["isTemplate"] == "true" ? "Oui": "Non");
				}else if(retour["mode"] == 3) //Modif
				{
					var tr = $("#m"+retour["idConcert"]).parent().parent();		
					tr.remove();
				}
			}
		});
		return false;
	}
	


/**

	Fonctions de la page concert
	
**/

	/*
		init_concert : Initialise la liste des instruments à drag&drop, ainsi que l'arbre SVG à traiter
	*/
	function init_concert(params)
	{
		$(document).ready(function()
		{
			$("#listeCentre").accordion({
			autoHeight: true,
			collapsible: true
			}).parent().appendTo($("#menuGauche"));

			
			$("#newConcert").click(function(e)
			{
				load_contenu("#historique",$(this),function()
				{
					formulaireConcert(1);
				});
				return true;
			});			
			$('#arbreConcert').arbre(params);
			
			$("#changeOrientation").change(function()
			{
				$('#arbreConcert').arbre("orientation",$(this).val());
			});
			
			$("#loadConcert").click(function()
			{
				var val = $("#concertCourant").val();
				if(val != "")
					load_contenu("#concert?i="+val,$(this));
			});
		});
	}
	
	/*
		formUserInstrumentConcert : ouvre une boîte de dialogue contenant le formulaire d'ajout/modification d'un bloc userInstrumentConcert
		- mode 			: Action voulue (1 : ajouter un instrument, 2 : modifier un instrument)
		- idNoeudPere	: ID de l'userInstrumentConcert à manipuler
		- e 			: élément jQuery qui a déclenché cette fonction
	*/
	function formUserInstrumentConcert(mode,idNoeudPere,e)
	{
		var bd = creerBD("Chargement en cours...", "<img src=\"../img/loading.gif\" /> Veuillez patienter",
							null,"500px",true,function() 
							{
								$("#listeCentre").parent().removeClass("toplevel");
								$(this).remove();
							}
						);
						
		deplacer_bd(bd,$("#rect-"+idNoeudPere),$("#listeCentre"));
		
		
		$.post("../ajax/formulaire_userInstrumentConcert.php",
				{"mode": mode, "idNoeud": (idNoeudPere || "") }
		).done(function( msg ) 
		{
			retour = jsonParse(msg);
			$("#bd").dialog("option","title",retour["header"]);
			$("#bd").html(retour["msg"]);
			if(!retour["erreur"])
			{
				var bouttons = {};
				bouttons[retour["boutton"]] =  function() {
					$("#bd form").submit();
				};
				bouttons["Annuler"] = function() {
					$("#bd").dialog("close");
				};
				
				$("#bd").dialog("option","buttons",bouttons);
				init_drag_and_drop();
			}
		});
		return false;
	}
	
	
	
	/*
		deplacer_bd : Déplace l'élément bd de l'élement from à l'élement to
						- bd 	: Objet jQuery
						- from	: Objet jQuery
						- to 	: Objet jQuery
	*/
	function deplacer_bd(bd,from,to)
	{	
		if(bd.length && from.length && to.length)
		{
			$(document).scrollTop(0);
			to.parent().addClass("toplevel");
			bd.parent().addClass("toplevel").css("position","fixed");
			bd.parent().css("top",from.offset().top).css("left",from.offset().left).css("opacity",0);
			var xDestination = 30 + parseInt(to.offset().left + parseInt(to.width(),10),10);
			var yDestination = to.offset().top ;
			bd.parent().animate({
				opacity: 1,
				left: xDestination,
				top:  yDestination,
			  }, 700);
		}
	}

	/*
		actionUserInstrumentConcert : Appelée après formUserInstrumentConcert, envoie le formulaire pour traitement au serveur et met à jour l'arbre SVG du client
		- form : Objet jQuery contenant le formulaire à envoyer
		- tab  : False par défaut, tableau format JSON contenant les données à envoyer 
	*/
	function actionUserInstrumentConcert(form, tab)
	{
		var deb = ($("#feedback").length == 0 ? $("#feedback2"): $("#feedback"));
		deb.html("<img src=\"../img/loading.gif\" alt=\"\" />Veuillez patienter ... ");
		$.post("../ajax/userInstrumentConcert.php", (tab || form.serialize())
		).done(function(msg)
		{
			retour = jsonParse(msg);
			
			if(!retour["erreur"])
			{
				$("#feedback2").html(retour["msg"]);
				$("#bd").dialog("close");
				if(retour["mode"] == 1) // Ajout
					$("#arbreConcert").arbre("ajouterNoeud",retour["idPere"], retour["idInstrument"],retour["nomInstrument"]);
				else if(retour["mode"] == 2)// Modif
					$("#arbreConcert").arbre("modifierNoeud",retour["idInstrument"],retour["nomInstrument"]);
				else if(retour["mode"] == 3)// Supp
					$("#arbreConcert").arbre("supprimerNoeud",retour["idInstrument"]);
			}else
			{
				$("#feedback").html(retour["msg"]);
			}
		});
		
		return false;
	}
	
	/*
		supprimerItemConcert : Supprime le userInstrumentConcert de l'arbre SVG ainsi qu'en BD
	*/
	function supprimerItemConcert(idNoeudPere,e)
	{
		if(confirm("Voulez-vous vraiment supprimer cette case ? La suppression est définitive"))
			return actionUserInstrumentConcert(null,{"mode" : 3, "idPere" : idNoeudPere, "nom": "", idInstrument: "", idUser : "" });
		else
			return false;
	}

	function majUser(idInstrument, select)
	{
		$.post("../ajax/maj_user_competence.php",
			{"idInstrument": idInstrument }
		).done(function(msg)
		{
			retour = jsonParse(msg);
			if(!retour["erreur"])
			{
				select.html(retour["select"]);
			}
		});
	}

/**

	Fonctions générales
	
**/


	/*
		supprimer_instrument_treeview : Masque/Affiche le menu, l'entête et le header de la page
		- img : Objet jQuery contenant l'image sur laquelle il faut cliquer
	*/	
	function zoom(img)
	{
		var vitesse = 'slow';
		if(img.hasClass("plus"))//Agrandir
		{
			$("#menuGauche, #footer").animate({width: 'toggle'},vitesse);
			$("#contenu").animate({ margin: '10px'},'slow',function(){
				$("#topMenu").slideUp(vitesse,function()
				{
					img.attr("src","../img/zoom-moins.png");
					img.attr("title","Quitter le mode plein écran");
					if($("#arbreConcert"))
						$("#arbreConcert").arbre();
				});
			});
			img.addClass("moins").removeClass("plus");
		}else //Réduire
		{
			$("#topMenu").slideDown(vitesse,function()
			{
				$("#contenu").animate({ marginLeft: '330px'},vitesse);
				$("#menuGauche, #footer").animate({width: 'toggle'},vitesse,function()
				{
					img.attr("src","../img/zoom-plus.png");
					img.attr("title","Passer en mode plein écran");
					if($("#arbreConcert"))
						$("#arbreConcert").arbre();
				});
			});
			
			img.addClass("plus").removeClass("moins");
		}
		
	}

	/*
		load_contenu : Charge le contenu d'une page php et l'affiche dans le conteneur principal
						- page: Nom de la page (format #nomPage)
						- lien: Objet jQuery contenant la balise "<a>" qui a provoqué l'appel de la fonction
						- callback : Null par défaut, fonction à appeler une fois le chargement terminé
	*/
	function load_contenu(page,lien,callback)
	{
		if(lien)
		{
			lien.append("<span class=\"loading\">&nbsp;&nbsp;<img src=\"../img/loading.gif\" title=\"Veuillez patienter...\"/></span>");
		}
		
		$("#corpsContenu").html("<div class=\"center\"><span class=\"loading\">Chargement en cours, veuillez patienter &nbsp;&nbsp;<img src=\"../img/loading.gif\" title=\"Veuillez patienter...\"/></span></div>");
		$.post("../ajax/loadPage.php",
				{"page" :page }).
		done(function( msg ) 
		{
			$(".loading").remove();
			retour = jsonParse(msg);

			$("#listeCentre").parent().remove();
			$("#corpsContenu").html(retour["msg"]);
			$("#headerTitre").html(retour["header"]);
			document.title = base + retour["header"];
			
			init_composants();
			if(typeof callback == "function")
				callback.call(arguments);
		});
	}
	
	/*
		menuContextuel : Remplace le menu contextuel du clic droit
							- idMenu : Selecteur jQuery pointant sur le menu à créer
							- selecteur : Selecteur jQuery pointant sur les éléments du DOM sur lesquels appliquer le menu				
	*/
	function menuContextuel(idMenu,selecteur)
	{
		$(selecteur).contextMenu({menu: idMenu},
			function(action, el, pos) {

				// formulaireInstrument(mode, idInstrument, idCat, nomInstrument)
				
				if(action == "ajouter")
					formulaireInstrument(1, "", $(el).attr("id"), "");
				else if(action == "modifier")
					formulaireInstrument(2, $(el).attr("id"), $(el).parent().parent().parent().find(".folder").attr("id"), $(el).html());
				else if(action == "supprimer")
					formulaireInstrument(3, $(el).attr("id"), $(el).parent().parent().parent().find(".folder").attr("id"), $(el).html());
				else if(action == "modifierCat")
					formulaireInstrument(5, $(el).attr("id"), "", $(el).html());
				else if(action == "supprimerCat")
					formulaireInstrument(6, $(el).attr("id"), $(el).attr("id"), $(el).html());
			});
	}
	
	/*
		creerBD : Ouvre une boîte de dialogue jQuery UI et retourne l'objet jQuery
					/!\ La boîte de dialogue possède un conteneur possédant l'id "feedback", pour le retour des communications ajax /!\
					
					- titre 		: Titre de la boîte de dialogue
					- corps 		: Contenu du texte de la boîte de dialogue
					- bouttons 		: Bouton OK par défaut, tableau associatif de type  { "nomBoutton" : function() { alert("nomBoutton cliqué"); } }
					- defautWidth 	: "Auto" par défaut, spécifie la largeur de la boîte de dialogue ( ex: "500px")
					- isModale 		: True par défaut, rend la boîte de dialogue non modale si passé à false
					- onClose 		: Null par défaut, fonction associée à l'évènement de fermeture de la fenêtre
	*/
	function creerBD(titre,corps,bouttons,defautWidth, isModale, onClose)
	{
		jQuery("<div>", 
		{
			id: "bd",
		}).appendTo("body");
		
		isModale = !(typeof isModale=="null" || (typeof isModale!="null" && isModale == false));

		var bd = $('#bd').html("<div id=\"feedback\" class=\"center\"></div>" + (corps || '')).dialog(
		{ 	autoOpen: false, 
			title: titre ||  'iScore',
			modal: isModale,
			width : defautWidth || "auto",
			buttons: bouttons || {
				"OK": function() {
				  $( this ).dialog( "close" );
				}
			},
			close: (onClose || function(event, ui) { $(this).remove(); } )
		}).dialog('open');
			
		return bd;
	}

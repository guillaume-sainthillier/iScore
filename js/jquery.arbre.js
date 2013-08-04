(function($)
{
		var defauts=
		{
			"noms" : {},
			"separateur": "rect-",
			"margeRect" : 15,
			"largeurRect" : 80,
			"hauteurRect" : 60,
			"paddingProfondeur" : 60,
			"orientation" : "paysage",
			"onAdd" : function(id,e) { return formUserInstrumentConcert(1,id,e); },
			"onEdit" : function(id,e) { return formUserInstrumentConcert(2,id,e); },
			"onDel" : function(id,e) { return supprimerItemConcert(id); }
		};  		
		
		var methods = 
		{
			
			ajouterNoeud : function(idNoeud,idNoeudFils,nomNoeud)
			{
				var noeud = $(this).find("#"+idNoeud);
				if(noeud)
				{
					params.noms[idNoeudFils] = nomNoeud;
					var fils = noeud.children();
					if(fils.length == 0)
						noeud.replaceWith("<ul id=\""+idNoeud+"\"><li id=\""+idNoeudFils+"\"></li></ul>");
					else
						noeud.append("<li id=\""+idNoeudFils+"\"></li>");
				}
				return methods.init.apply(this);
			},
			
			modifierNoeud : function(idNoeud,nomNoeud)
			{
				var noeud = $(this).find("#"+idNoeud);
				if(noeud)
				{
					params.noms[idNoeud] = nomNoeud;
				}
				return methods.init.apply(this);
			},
			
			supprimerNoeud : function(idNoeud)
			{
				var noeud = $(this).find("#"+idNoeud);
				if(noeud)
				{
					noeud.remove();
				}
				return methods.init.apply(this);
			},

			
			dessinerTextes: function(noeud)
			{
				var rect = $("#"+params.separateur+noeud.attr("id"),svg.root());
				if(rect)
				{
					var x = parseInt(rect.attr("x"),10)+ 5;
					var y = parseInt(rect.attr("y"),10)+ 20;
					var largeur = parseInt(rect.attr("width"),10);
					var hauteur = parseInt(rect.attr("height"),10);
					if(!isNaN(x) && !isNaN(y) && !isNaN(largeur) && !isNaN(hauteur) && params.noms[noeud.attr("id")])
					{
						var g = svg.group({fontWeight: 'bold', fontSize: '15', fill: 'black'}); 
						svg.text(g,x, parseInt(y-3,10), params.noms[noeud.attr("id")]);  
					}
				}
				var fils = $(noeud.children());
				for(var i =0; i < fils.length; i++)
					methods.dessinerTextes($(fils[i]));
			},
			
			orientation : function(mode)
			{
				params.orientation = mode;
				return methods.init.apply(this);
			},
				
			dessinerTraits: function (noeud, isRoot)
			{	 				
				var rect = $("#"+params.separateur+noeud.attr("id"),svg.root());
				var x = parseInt(rect.attr("x"),10);
				var y = parseInt(rect.attr("y"),10);
				var largeur = parseInt(rect.attr("width"),10);
				var hauteur = parseInt(rect.attr("height"),10);
				if(!isNaN(x) && !isNaN(y) && !isNaN(largeur) && !isNaN(hauteur))
				{
					var fils = $(noeud.children());
					for(var i =0; i < fils.length; i++)
					{
						var rectFils = $("#"+params.separateur+$(fils[i]).attr("id"),svg.root());
						var xFils = parseInt(rectFils.attr("x"),10);
						var yFils = parseInt(rectFils.attr("y"),10);
						var largeurFils = parseInt(rectFils.attr("width"),10);
						var hauteurFils = parseInt(rectFils.attr("height"),10);
						if(!isNaN(x) && !isNaN(y) && !isNaN(largeur) && !isNaN(hauteur))
						{
							if(params.orientation == "paysage")							
								svg.line(x + parseInt(largeur/2,10), y+hauteur, xFils + parseInt(largeurFils/2,10), yFils,{stroke: "black"});
							else
								svg.line(x + largeur, y+ parseInt(hauteur/2) , xFils , yFils + parseInt(hauteurFils/2,10),{stroke: "black"});
						}
						methods.dessinerTraits($(fils[i]));
					}
				}
				
			},
						
			dessinerRectangles: function (posX,posY,longueurTotale, noeud)
			{
				if(params.orientation == "paysage")
				{
					svg.rect( posX + parseInt((longueurTotale - params.margeRect - params.largeurRect)/2,10) , posY, params.largeurRect,params.hauteurRect,
							{id:params.separateur+ noeud.attr("id"), fill: "lightgrey", stroke: "black",
							});
				}else
				{
					svg.rect( posX, posY + parseInt((longueurTotale - params.margeRect - params.hauteurRect)/2,10) , params.largeurRect,params.hauteurRect,
							{id:params.separateur+ noeud.attr("id"), fill: "lightgrey", stroke: "black",
							});
				}
				
				var fils = $(noeud.children());
				if(fils.length > 0)
				{
					var lg = parseInt(longueurTotale / fils.length,10);
					for(var i = 0; i < fils.length ; i++)
					{
						if(params.orientation == "paysage")
							methods.dessinerRectangles(posX + (i*lg), posY + params.hauteurRect + params.paddingProfondeur, lg, $(fils[i]));
						else
							methods.dessinerRectangles(posX + params.hauteurRect + params.paddingProfondeur, posY + (i*lg), lg, $(fils[i]));
					}
				}
			},
			
			init: function(options)
			{    
				return this.each(function()
				{
					if($(this).html().length > 0)
					{
						if(! $(this).find(".datas").length)
							$(this).html($("<div class=\"arbre\"></div><br /><div class=\"datas\">"+$(this).html()+"</div>"));
						
						var html = $(this).find(".datas").hide().html();
						
						$(this).find(".arbre").svg().svg('destroy').svg();
						svg = $(this).find(".arbre").svg('get');
						
						if(params.orientation == "paysage")
							var width = parseInt($(svg.root()).attr("width"),10) -50;
						else
							var width = parseInt($(svg.root()).attr("height"),10);
							
						methods.dessinerRectangles(20,20,width,$(html));
						methods.dessinerTraits($(html),true);			
						methods.dessinerTextes($(html));			
			
						$("#menuConcert").remove();
								
						$("rect").each(function(i)
						{						
							$(this).hover(function()
							{
								$("#menuConcert").remove();
								
								var id = $(this).attr("id").split(params.separateur);
								var x = $(this).offset().left -10;
								var y = $(this).offset().top - 33 ;
								var lg = parseInt($(this).attr("width"),10)+20;
								var menu = $("<div id=\"menuConcert\" class=\"menuConcert\" >"+
									"<a href=\"#addNoeud\" class=\"fleft addNoeud\"  title=\"Ajouter une entité\"   ></a>"+
									"<a href=\"#editNoeud\" class=\"fleft editNoeud\"  title=\"Modifier une entité\"   ></a>"+
									(i != 0 ? "<a href=\"#delNoeud\" class=\"fleft delNoeud\" title=\"Supprimer une entité\" ></a>" : "")+
									"</div>").appendTo("body");
								menu.css({ width: lg+"px", top: y+"px", left: x+"px" });
								menu.find(".addNoeud").click(function(e){return params.onAdd(id[1], menu);}); 
								menu.find(".editNoeud").click(function(e){return params.onEdit(id[1], menu);}); 
								menu.find(".delNoeud").click(function(e){return params.onDel(id[1], menu);}); 
							});	
						});
					}
				});
			}
		};
		
		var params= null;
		var svg = null;
		$.fn.arbre = function( method )
		{
			if ( methods[method] ) {
				return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if ( typeof method === 'object' || ! method ) {
				params = $.extend(defauts,arguments[0]);
				return methods.init.apply( this, arguments );
			}
		};

})(jQuery);


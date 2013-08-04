iscore
======

iScore est l'interface d'administration d'un projet de gestion de concerts de musique.

Dans le cadre d'une collaboration avec des étudiants de 5ème année, l'application permet de gérer un parc d'instruments de musique et de musiciens, mais aussi de créer des configuration de concerts qui seront utilisés par les musiciens lors de leur répétition. 

[authors] ================================
Alexandre Bongard <alx.bongard@gmail.com>
Thomas Folgueral <thomas.folgueral@gmail.com>
Guillaume Sainthillier <guillaume.sainthillier@gmail.com>
==========================================

_____________________________________________________________________________
Pre requis :
============
Vous devez avoir installé:
- un serveur apache et php
- une base de données : mysql ou postgresql
-----------------------------------------------------------------------------
Prerequisite :
============
You must have the following services installed :
- Apache server with php extension
- database server : mysql or postgresql
_____________________________________________________________________________
En fait, reportez vous aux installations de :
- sous windows : wamp (http://www.wampserver.com/)
                 ou easyphp(http://easyphp.fr/)
- sous linux : lamp

Si vous utilisez postgre sql, voir install sur le site :
http://www.postgresqlfr.org/

Si vous debutez, il est plus simple de garder mysql qui est packagee avec
easyphp ou wamp.
-----------------------------------------------------------------------------
you should report to the installations of :
- for windows : wamp (http://www.wampserver.com/)
                or easyphp(http://easyphp.fr/)
- for Linux : lamp

In case you use postgresql, you also have to install postgresql
(http://www.postgresqlfr.org/)

If you're new to all that, it'd be easier to keep mysql that comes
included in the easyphp or wamp package.
_____________________________________________________________________________
* Installation de iScore 
   copier le repertoire iScore sur votre serveur
        wamp/www/iScore
        sous linux (debian) : /var/www/iScore
-----------------------------------------------------------------------------
Install iScore 
   copy the iScore folder on your server
        wamp/www/iScore
       for Linux (Debian) : /var/www/iScore
___________________________________________________________________________
* Initialisation de la base en MySQL ou Postgresql 
    creer la base iScore sur mysql 
    Ensuite, il faut créer la structure des tables de données via le fichier iscore_structure.sql présent à la racine du projet
  Si besoin, importer le fichier iscore_donnees.sql pour avoir un petit jeu de données 
____________________________________________________________________________

Configurez l'acccès au serveur via le fichier dyn/database.inc.php

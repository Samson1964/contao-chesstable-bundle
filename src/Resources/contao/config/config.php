<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   fen
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

/**
 * -------------------------------------------------------------------------
 * CONTENT ELEMENTS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CTE']['schach']['chesstable'] = 'Schachbulle\ContaoChesstableBundle\ContentElements\Chesstable';

/**
 * -------------------------------------------------------------------------
 * Voreinstellungen
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_CONFIG']['chesstable_blindfelder'] = 'x,xx,*,**,#';
$GLOBALS['TL_CONFIG']['chesstable_nationfelder'] = 'Flagge,Fahne,Land,Nation,Staat';
$GLOBALS['TL_CONFIG']['chesstable_platzfelder'] = 'Platz,Pl.,No.,Nr.,Br.';
$GLOBALS['TL_CONFIG']['chesstable_vereinfelder'] = 'Bundesland,Klub,Verein,Mannschaft,Verein/Ort,Ort,Ort/Verein';
$GLOBALS['TL_CONFIG']['chesstable_namenfelder'] = 'Spieler,Spielerin,Name,Teilnehmer,Teilnehmerin,Weiß,Weiss,Schwarz';
$GLOBALS['TL_CONFIG']['chesstable_punktefelder'] = 'Punkte,Pkt.,Pkt,Points,MP,BP';
$GLOBALS['TL_CONFIG']['chesstable_wertungfelder'] = 'BUW,BW,BUWE,Buchh.,SB,SBW,SoBe,SoBerg,DiVerg';
$GLOBALS['TL_CONFIG']['chesstable_ratingfelder'] = 'Elo,DWZ,BWZ,DWZ-Schnitt,Schnitt,Rating,TWZ,NWZ,Erg.';
$GLOBALS['TL_CONFIG']['chesstable_ergebnisfelder'] = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40';
$GLOBALS['TL_CONFIG']['chesstable_farbfelder'] = 'F';
$GLOBALS['TL_CONFIG']['chesstable_steuerfelder'] = 'Q';
$GLOBALS['TL_CONFIG']['chesstable_css'] = 1;

$chesstable_markColors = array
(
	array('intern' => 'up', 'name' => 'Aufsteiger', 'color' => 'abffa9'),
	array('intern' => 'down', 'name' => 'Absteiger', 'color' => 'ffb7b7'),
	array('intern' => 'high', 'name' => 'Sonstige Zeilen', 'color' => 'ffff00'),
);
$GLOBALS['TL_CONFIG']['chesstable_markColors'] = serialize($chesstable_markColors);

/**
 * -------------------------------------------------------------------------
 * Eigener inputType
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_FFL']['chesstableColors'] = 'Schachbulle\ContaoChesstableBundle\Widgets\ChesstableColors';

<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   chesstable
 * Version    1.0.0
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

class chesstable extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_chesstable';
	protected $strTemplateLightbox = 'ce_chesstable_lightbox';

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		//global $objPage,$objArticle;
		//print_r($GLOBALS);
		//echo "ID=".$objPage->id;

		// Parameter zuweisen
		$csv = $this->chesstable_csv;
		$file = $this->chesstable_file;
		$aufsteiger = explode(",",$this->chesstable_aufsteiger);
		$absteiger = explode(",",$this->chesstable_absteiger);
		$markieren = explode(",",$this->chesstable_markieren);
		$namendrehen = $this->chesstable_namendrehen;
		$lightbox = $this->chesstable_lightbox;
		$linktext = $this->chesstable_linktext;
		$flagge = $this->chesstable_flaggen;

		// Array-Werte wie z.B. "1-5" weiter auflösen in 1,2,3,4,5
		$aufsteiger = $this->ArrayAufloesen($aufsteiger);
		$absteiger = $this->ArrayAufloesen($absteiger);
		$markieren = $this->ArrayAufloesen($markieren);

		// Aktualisierungsdatum
		($this->chesstable_date) ? $aktdatum = $this->tstamp : $aktdatum = 0;
		
		if(!$linktext) $linktext = "Alternativtabelle";

		// Konfiguration der Tabellenköpfe einlesen (als Kleinschreibung)
		$blindfelder = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_blindfelder']));
		$spaltenkopf = array();
		$spaltenkopf[1] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_nationfelder']));
		$spaltenkopf[2] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_platzfelder']));
		$spaltenkopf[3] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_vereinfelder']));
		$spaltenkopf[4] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_namenfelder']));
		$spaltenkopf[5] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_punktefelder']));
		$spaltenkopf[6] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_wertungfelder']));
		$spaltenkopf[7] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_ratingfelder']));
		$spaltenkopf[8] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_ergebnisfelder']));
		$spaltenkopf[9] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_farbfelder']));
		$spaltenkopf[10] = array_map(array($this,"kleinschreibung"),explode(",",$GLOBALS['TL_CONFIG']['chesstable_steuerfelder']));
		$klassen = array("text","nation","place","club","name","points","rating","elo","result","color","control");

		// CSV-Daten in Tabellen-Array übertragen
		$steuerspalte = 0;
		$tabelle = array();
		$eigenklasse = array(); // Array, um die eigenen Klassennamen zu speichern
		$spaltenart = array();
		$zeile = explode("\n",$csv); // Zeilen trennen
		for($x=0;$x<count($zeile);$x++)
		{
			$spalte = explode(";",$zeile[$x]); // Spalten trennen
			for($y=0;$y<count($spalte);$y++)
			{
				$temp = trim($spalte[$y]); // Feldinhalt trimmen
				list($tabelle[$x][$y], $eigenklasse[$x][$y]) = $this->ExtractClass($temp); // Eigene Klasse finden
				// Wenn oberste Zeile, dann Spaltenart feststellen
				if($x == 0)
				{
					$spaltenart[$y+1] = 0; // Standardkopf
					for($z=1;$z<=count($spaltenkopf);$z++)
					{
						// Spaltentitel in definierten Spaltenköpfen suchen
						// Bsp. Suche nach "name" im Array("1","2","3")
						if(in_array(strtolower($tabelle[$x][$y]),$spaltenkopf[$z]))
						{
							$spaltenart[$y+1] = $z; // Anderen Spaltenkopf gefunden
							if($klassen[$spaltenart[$y+1]] == "control") $steuerspalte = $y+1; // Steuerspalte sichern
							break;
						}
					}
				}
			}
		}

		//echo "<pre>";
		//print_r($tabelle);
		//echo "<pre>";
		
		// Tabelle generieren
		$content = "<table class=\"chesstable\">\n";
		// Zuerst Zeilen durchlaufen
		for($x=0;$x<count($tabelle);$x++)
		{
			$ze = $x + 1; // Zeilennummer ab 1 statt 0

			// Wenn Steuerspalte den Wert "team" enthält, dann CSS-Klasse in Zeile eintragen
			($steuerspalte && $tabelle[$x][$steuerspalte-1] == "team") ? $trcss = "row$ze team" : $trcss = "row$ze";

			// Zeilen mit Auf- und Absteigern markieren
			if(in_array($ze, $aufsteiger)) $trcss .= " up";
			if(in_array($ze, $absteiger)) $trcss .= " down";
			if(in_array($ze, $markieren)) $trcss .= " high";

			// Ist ein Befehl in Spalte 1?
			if($tabelle[$x][0] == '~')
			{
				// Leerzeile erzeugen und nächste Zeile als Kopfzeile (th) formatieren
				$content .= "<tr class=\"leerzeile\">\n";
				$content .= "  <td colspan=\"" . $spaltenzahl . "\"></td>\n";
				$content .= "</tr>\n";   
				$kopfzeile = true;
				continue;
			}
			elseif($tabelle[$x][0] == '[TEXT]')
			{
				// Textzeile erzeugen
				$content .= "<tr class=\"textzeile\">\n";
				$content .= "  <td colspan=\"" . $spaltenzahl . "\">" . $tabelle[$x][1] . "</td>\n";
				$content .= "</tr>\n";   
				continue;
			}
			
			$content .= "<tr class=\"$trcss\">\n";

			// Jetzt Spalten durchlaufen
			for($y=0;$y<count($tabelle[$x]);$y++)
			{
				$sp = $y+1; // Spaltennummer ab 1 statt 0
				$wert = $tabelle[$x][$y]; // Wert aus Tabelle zuweisen
				$ownclass = $eigenklasse[$x][$y]; // Klasse aus Tabelle zuweisen
				$wert = \Controller::replaceInsertTags($wert); // Inserttags ersetzen
				
				// Zeilenart td oder th einstellen
				if($ze == 1 || $kopfzeile) 
				{
					$td = "th";
				}
				else $td = "td"; // th statt td in Zeile 1
				
				$klasse = $klassen[$spaltenart[$sp]]; // CSS-Klasse für Spaltenart
				// Name drehen, wenn gefordert
				if($namendrehen && $klasse == "name" && $ze > 1)
				{
					$wert = $this->NameDrehen($wert);
				}
				if(in_array(strtolower($wert),$blindfelder))
					$content .= "<$td class=\"row$ze col$sp blindfield $klasse$ownclass\">".$wert."</$td>\n";
				else if($klasse == "control") // Spalte 'control' nicht anzeigen
					$content .= "";
				else if($td == "td" && $klasse == "nation") // wenn Spalte 'nation'
				{
					if($flagge)
					{
						// Flagge anzeigen, wenn vorhanden
						$flaggenurl = "system/modules/chesstable/assets/images/flags/".strtolower($wert).".jpg";
						$flaggendatei = $_SERVER["DOCUMENT_ROOT"]."/".$flaggenurl;
						if(file_exists($flaggendatei))
							$content .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass\"><img src=\"".$flaggenurl."\" width=\"23\" height=\"15\" /></$td>\n";
						else
							$content .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass ".strtolower($wert)." \">".$wert."</$td>\n"; // Nationenname als title und class einfügen
					}
					else
					{
						// Länderkürzel oder Flagge mit CSS
						$content .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass ".strtolower($wert)."\">".$wert."</$td>\n"; // Nationenname als title und class einfügen
					}
				}
				else if($td == "th" && $klasse == "color") // wenn Spaltenkopf 'farbe'
					$content .= "<$td title=\"".$wert."\" class=\"row$ze $klasse$ownclass\">&nbsp;</$td>\n"; // Farbspalte ohne Inhalt in th
				else if($td == "td" && $klasse == "color") // wenn Spalte 'farbe'
				{
					// Farbe feststellen und CSS-Klasse entsprechend modifizieren
					if(strtolower($wert) == "w") $klasse .= "_w";
					if(strtolower($wert) == "b") $klasse .= "_b";
					$content .= "<$td title=\"".$wert."\" class=\"row$ze $klasse$ownclass\">&nbsp;</$td>\n"; // Farbspalte
				}
				else
					$content .= "<$td class=\"row$ze col$sp $klasse$ownclass\">".$wert."</$td>\n";
			}
			$content .= "</tr>\n";
			$kopfzeile = false; 
			$spaltenzahl = count($tabelle[$x]); // Anzahl der Spalten für nächsten Schleifenlauf merken

		}
		$content .= "</table>\n";

		// Lightbox-Modus?
		if($lightbox)
		{
			// Template ausgeben
			$this->Template = new \FrontendTemplate($this->strTemplateLightbox);
			$this->Template->id = $this->id;
			$this->Template->linktext = $linktext;
			$this->Template->class = "ce_chesstable";
			$this->Template->tabelle = $content;
			$this->Template->datum = $aktdatum;
			$this->Template->turnierende = $this->chesstable_ende;
			$this->Template->hinweis = $this->chesstable_note;
		}
		else
		{
			// Template ausgeben
			$this->Template = new \FrontendTemplate($this->strTemplate);
			$this->Template->class = "ce_chesstable";
			$this->Template->tabelle = $content;
			$this->Template->datum = $aktdatum;
			$this->Template->turnierende = $this->chesstable_ende;
			$this->Template->hinweis = $this->chesstable_note;
		}

		return;

	}

	/**
	 * Funktion ArrayAufloesen
	 *
	 * @param     array           Bsp.: array('1','3-7','8-9','34')
	 *
	 * @return    array           Bsp.: array('1','3','4','5','6','7','8','9','34')
	 */
	protected function ArrayAufloesen($array)
	{
		$newArray = array();
		foreach($array as $item)
		{
			if(ctype_digit($item))
			{
				// Integerzahl direkt übernehmen
				$newArray[] = $item;
			}
			else
			{
				// String in der Form "Zahl-Zahl" auflösen
				$temp = explode("-", $item);
				for($x = $temp[0]; $x <= $temp[1]; $x++)
				{
					$newArray[] = $x;
				}
			}
		}
		return $newArray;
	}

	protected function kleinschreibung($wert)
	{
		return strtolower($wert);
	}

	protected function NameDrehen($intext)
	{
		// Konvertiert Namen der Form Nachname,Vorname,Titel nach Titel Vorname Name
		$array = explode(",",$intext);
		$teile = count($array);
		$result = "";
		for($x=$teile-1;$x>=0;$x--)
		{
			$result .= " ".$array[$x];
		}
		return $result;
	}

	protected function ExtractClass($wert)
	{
		$pos1 = strpos($wert, '{');
		if($pos1 === false)
		{
			// Startzeichen nicht vorhanden
			return array($wert, '');
		}
		else
		{
			$pos2 = strpos($wert, '}');
			if($pos2 === false)
			{
				// Endezeichen nicht vorhanden
				return array($wert, '');
			}
			else
			{
				$pos2 = strpos($wert, '}');
				// Werte trennen
				$class = ' own_' . substr($wert, $pos1 + 1, $pos2 - $pos1 - 1);
				$value = substr($wert, 0, $pos1) . substr($wert, $pos2 + 1);
				return array($value, $class);
			}
		}
	}
}
?>
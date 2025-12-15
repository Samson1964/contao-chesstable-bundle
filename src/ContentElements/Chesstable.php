<?php

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

namespace Schachbulle\ContaoChesstableBundle\ContentElements;

class Chesstable extends \Contao\ContentElement
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

		// Symlink für das externe Bundle components/flag-icon-css erstellen, wenn noch nicht vorhanden
		if(!is_link(\Contao\System::getContainer()->getParameter('kernel.project_dir').'/public/bundles/flag-icon-css')) symlink(\Contao\System::getContainer()->getParameter('kernel.project_dir').'/vendor/components/flag-icon-css/', \Contao\System::getContainer()->getParameter('kernel.project_dir').'/public/bundles/flag-icon-css'); // Ziel, Name

		// Farben aus System-Einstellungen laden
		$markierung = array();
		$temp = unserialize($GLOBALS['TL_CONFIG']['chesstable_markColors']);
		foreach($temp as $item)
		{
			$markierung[$item['intern']] = array
			(
				'color'   => $item['color'] ? '#'.$item['color'] : false,
				'rows'    => array(),
				'flags'   => array(),
			);
		}

		// Parameter zuweisen
		$csv = $this->chesstable_csv;
		$file = $this->chesstable_file;
		$namendrehen = $this->chesstable_namendrehen;
		$lightbox = $this->chesstable_lightbox;
		$linktext = $this->chesstable_linktext;
		$flagge = $this->chesstable_flaggen;

		// Farbliche Zeilenmarkierungen auflösen
		$temp = (array)unserialize($this->chesstable_markierungen);
		foreach($temp as $item)
		{
			if(isset($item['rows']))
			{
				// Werte wie z.B. "1-5,7,10-12" weiter auflösen in "1,2,3,4,5,7,10,11,12"
				$markierung[$item['intern']]['rows'] = $this->ArrayAufloesen(explode(",", $item['rows']));
			}
			if(isset($item['flags']))  
			{
				$markierung[$item['intern']]['flags'] = explode(",", $item['flags']); // Länderkürzel z.B. "GER,USA" in Array umwandeln
			}
		}

		// Fett- und Kursivmarkierungen auflösen
		$markierungFett = $this->ArrayAufloesen(explode(",", $this->chesstable_markBold));
		$markierungKursiv = $this->ArrayAufloesen(explode(",", $this->chesstable_markItalic));
		
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
			$spalte = explode(';',html_entity_decode($zeile[$x])); // Spalten trennen, vorher HTML-Entities umwandeln
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
		
		// Nummer-Spalte vorn hinzufügen, wenn automatische Nummerierung aktiv ist
		if($this->chesstable_autoNumber)
		{
			$neutabelle = array();
			// Zuerst Zeilen durchlaufen
			for($row=0;$row<count($tabelle);$row++)
			{
				if($row==0)
				{
					// 1. Zeile, Text "Nr." als erste Spalte einfügen
					$neutabelle[$row][0] = 'Nr.';
				}
				for($col=0;$col<count($tabelle[$row]);$col++)
				{
					if($row>0 && $col == 0)
					{
						$neutabelle[$row][0] = $row; // Zeilennummer einfügen
					}
					$neutabelle[$row][$col+1] = $tabelle[$row][$col]; // Restliche Spalten um +1 verschieben
				}
			}
			$tabelle = $neutabelle;
		}
		
		// Tabelle generieren
		$content = "<table class=\"chesstable\">\n";
		$spaltenzahl = 0;
		// Zuerst Zeilen durchlaufen
		for($x=0;$x<count($tabelle);$x++)
		{
			$ze = $x + 1; // Zeilennummer ab 1 statt 0

			// Wenn Steuerspalte den Wert "team" enthält, dann CSS-Klasse in Zeile eintragen
			($steuerspalte && $tabelle[$x][$steuerspalte-1] == "team") ? $trcss = "row$ze team" : $trcss = "row$ze";
			$trstyle = ''; // CSS-Stil der Zeile zurücksetzen

			// Zeilen farblich markieren. Auf- und Absteiger sowie gehighligtete wird zusätzlich die CSS-Klasse mitgegeben
			foreach($markierung as $key => $value)
			{
				switch($key)
				{
					case 'up':
					case 'down':
					case 'high':
						if(in_array($ze, $markierung[$key]['rows']))
						{
							$trstyle = $markierung[$key]['color'] ? "background-color:".$markierung[$key]['color'].";" : '';
							$trcss .= " ".$key;
						}
						break;
					default:
						if(in_array($ze, $markierung[$key]['rows'])) 
						{
							$trstyle = $markierung[$key]['color'] ? "background-color:".$markierung[$key]['color'].";" : '';
						}
				}
			}

			//echo "<pre>";
			//print_r($tabelle[$x]);
			//echo count($tabelle[$x]);
			//echo "</pre>";      

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
			elseif($tabelle[$x][0] == '' && count($tabelle[$x]) == 1)
			{
				// Leerzeile gefunden
				$content .= "<tr class=\"leerzeile\">\n";   
				for($col = 1; $col <= $spaltenzahl; $col++)
				{
					$content .= "  <td style=\"text-align:center;\">...</td>\n";
				} 
				//$content .= "  <td colspan=\"" . $spaltenzahl . "\">...</td>\n";
				$content .= "</tr>\n";
				continue;
			}
			
			$strZeile = ''; // Neue Zeile initialisieren
			// Jetzt Spalten durchlaufen
			for($y=0;$y<count($tabelle[$x]);$y++)
			{
				$sp = $y+1; // Spaltennummer ab 1 statt 0
				$wert = $tabelle[$x][$y]; // Wert aus Tabelle zuweisen
				$ownclass = @$eigenklasse[$x][$y]; // Klasse aus Tabelle zuweisen    
				
				// Zeilenart td oder th einstellen
				if($ze == 1 || $kopfzeile) 
				{
					$td = "th";
				}
				else $td = "td"; // th statt td in Zeile 1
				
				if(isset($spaltenart[$sp])) $klasse =  $klassen[$spaltenart[$sp]]; // CSS-Klasse für Spaltenart
				if($klasse == 'place' && $sp > 1) $klasse = ''; // place-Klasse entfernen, wenn nicht Spalte 1
				
				// Name drehen, wenn gefordert
				if($namendrehen && $klasse == "name" && $ze > 1)
				{
					$wert = $this->NameDrehen($wert);
				}
				if(in_array(strtolower($wert),$blindfelder))
					$strZeile .= "<$td class=\"row$ze col$sp blindfield $klasse$ownclass\">".$wert."</$td>\n";
				elseif($klasse == "control") // Spalte 'control' nicht anzeigen
					$strZeile .= "";
				elseif($td == "td" && $klasse == "nation") // wenn Spalte 'nation'
				{
					if($flagge) // Flaggen anzeigen ist aktiviert
					{
						// Flaggen-CSS bestimmen
						$flag_css = self::Laendercode($wert);
						// Flagge anzeigen, wenn vorhanden
						if($flag_css)
							$strZeile .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass\"><span class=\"$flag_css\"></span></$td>\n";
						else
							$strZeile .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass\"><span class=\"ioc_code\">$wert</span></$td>\n";
					}
					else // Flaggen anzeigen ist nicht aktiviert
					{
						// Länderkürzel oder Flagge mit CSS
						$strZeile .= "<$td title=\"".$wert."\" class=\"row$ze col$sp $klasse$ownclass ".strtolower($wert)."\">".$wert."</$td>\n"; // Nationenname als title und class einfügen
					}

					// Ländermarkierungen übertragen, wenn Land und Farbe vorhanden
					foreach($markierung as $key => $value)
					{
						if(in_array($wert, $markierung[$key]['flags']) && $markierung[$key]['color']) 
						{
							// Ländermarkierung überschreibt Zeilenmarkierung!
							$trstyle = "background-color:".$markierung[$key]['color'].";";
						}
					}

				}
				else if($td == "th" && $klasse == "color") // wenn Spaltenkopf 'farbe'
				{
					$strZeile .= "<$td title=\"".$wert."\" class=\"row$ze $klasse$ownclass\">&nbsp;</$td>\n"; // Farbspalte ohne Inhalt in th
				}
				else if($td == "td" && $klasse == "color") // wenn Spalte 'farbe'
				{
					// Farbe feststellen und CSS-Klasse entsprechend modifizieren
					if(strtolower($wert) == "w") $klasse .= "_w";
					if(strtolower($wert) == "b") $klasse .= "_b";
					$strZeile .= "<$td title=\"".$wert."\" class=\"row$ze $klasse$ownclass\">&nbsp;</$td>\n"; // Farbspalte
				}
				else
				{
					// Bei Ergebnisspalten Weiß und Schwarz feststellen und CSS-Klasse festlegen
					$boardcolor = '';
					if($klasse == 'result')
					{
						if(stristr($wert, 'w') == true) $boardcolor = ' white';
						elseif(stristr($wert, 'b') == true || stristr($wert, 's') == true) $boardcolor = ' black';
						else $boardcolor = '';
					}
					$strZeile .= "<$td class=\"row$ze col$sp $klasse$ownclass$boardcolor\">".$wert."</$td>\n";
				}
				$wert = \Contao\System::getContainer()->get('contao.insert_tag.parser')->replace($wert); // Inserttags ersetzen
			}

			// Zeile fett?
			if(in_array($ze, $markierungFett)) $trstyle .= " font-weight:bold;";
			if(in_array($ze, $markierungKursiv)) $trstyle .= " font-style:italic;";
			
			// Zeile übernehmen
			$content .= "<tr class=\"$trcss\" style=\"$trstyle\">\n";
			$content .= $strZeile;
			$content .= "</tr>\n";

			$kopfzeile = false; 
			$spaltenzahl = count($tabelle[$x]); // Anzahl der Spalten für nächsten Schleifenlauf merken

		}
		$content .= "</table>\n";

		// Lightbox-Modus?
		if($lightbox)
		{
			// Template ausgeben
			$this->Template = new \Contao\FrontendTemplate($this->strTemplateLightbox);
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
			$this->Template = new \Contao\FrontendTemplate($this->strTemplate);
			$this->Template->class = "ce_chesstable";
			$this->Template->tabelle = $content;
			$this->Template->datum = $aktdatum;
			$this->Template->turnierende = $this->chesstable_ende;
			$this->Template->hinweis = $this->chesstable_note;
		}

		// CSS aktivieren
		if($GLOBALS['TL_CONFIG']['chesstable_css']) $GLOBALS['TL_CSS'][] = 'bundles/contaochesstable/default.css';

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
				if(count($temp) > 1)
				{
					for($x = $temp[0]; $x <= $temp[1]; $x++)
					{
						$newArray[] = $x;
					}
				}
			}
		}
		return (array)$newArray;
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
		$pos1 = strpos($wert, '[');
		if($pos1 === false)
		{
			// Startzeichen nicht vorhanden
			return array($wert, '');
		}
		else
		{
			$pos2 = strpos($wert, ']');
			if($pos2 === false)
			{
				// Endezeichen nicht vorhanden
				return array($wert, '');
			}
			else
			{
				$pos2 = strpos($wert, ']');
				// Werte trennen
				$class = ' own_' . substr($wert, $pos1 + 1, $pos2 - $pos1 - 1);
				$value = substr($wert, 0, $pos1) . substr($wert, $pos2 + 1);
				return array($value, $class);
			}
		}
	}
	
	/**
	 * Funktion Laendercode
	 * @param     string $ioc      dreistelliger IOC-Code
	 * @return    string           CSS-Klassen für Verwendung CSS-Datei des Bundles components/flag-icon-css
	 */
	protected function Laendercode($ioc)
	{
		$ioc = trim(strtoupper($ioc));
		if(!$ioc) return '';

		$flagge = '';
		// Flagge in IOC-Code suchen
		foreach($this->countries as $country)
		{
			if($country['ioc'] == $ioc)
			{
				$flagge = 'flag-icon flag-icon-'.strtolower($country['alpha2']);
			}
		}

		// Flagge in Alpha3-Code suchen, wenn in IOC-Code nicht gefunden
		if(!$flagge)
		{
			foreach($this->countries as $country)
			{
				if($country['alpha3'] == $ioc)
				{
					$flagge = 'flag-icon flag-icon-'.strtolower($country['alpha2']);
				}
			}
		}

		return $flagge;
	}

	private $countries = 
	[
		[ 'name' => 'Afghanistan', 'alpha2' => 'AF', 'alpha3' => 'AFG', 'numeric' => '004', 'tld' => '.af', 'ioc' => 'AFG' ],
		[ 'name' => 'Ägypten', 'alpha2' => 'EG', 'alpha3' => 'EGY', 'numeric' => '818', 'tld' => '.eg', 'ioc' => 'EGY' ],
		[ 'name' => 'Åland', 'alpha2' => 'AX', 'alpha3' => 'ALA', 'numeric' => '248', 'tld' => '.ax', 'ioc' => '' ],
		[ 'name' => 'Albanien', 'alpha2' => 'AL', 'alpha3' => 'ALB', 'numeric' => '008', 'tld' => '.al', 'ioc' => 'ALB' ],
		[ 'name' => 'Algerien', 'alpha2' => 'DZ', 'alpha3' => 'DZA', 'numeric' => '012', 'tld' => '.dz', 'ioc' => 'ALG' ],
		[ 'name' => 'Amerikanisch-Samoa', 'alpha2' => 'AS', 'alpha3' => 'ASM', 'numeric' => '016', 'tld' => '.as', 'ioc' => 'ASA' ],
		[ 'name' => 'Amerikanische Jungferninseln', 'alpha2' => 'VI', 'alpha3' => 'VIR', 'numeric' => '850', 'tld' => '.vi', 'ioc' => 'ISV' ],
		[ 'name' => 'Andorra', 'alpha2' => 'AD', 'alpha3' => 'AND', 'numeric' => '020', 'tld' => '.ad', 'ioc' => 'AND' ],
		[ 'name' => 'Angola', 'alpha2' => 'AO', 'alpha3' => 'AGO', 'numeric' => '024', 'tld' => '.ao', 'ioc' => 'ANG' ],
		[ 'name' => 'Anguilla', 'alpha2' => 'AI', 'alpha3' => 'AIA', 'numeric' => '660', 'tld' => '.ai', 'ioc' => '' ],
		[ 'name' => 'Antarktika', 'alpha2' => 'AQ', 'alpha3' => 'ATA', 'numeric' => '010', 'tld' => '.aq', 'ioc' => '' ],
		[ 'name' => 'Antigua und Barbuda', 'alpha2' => 'AG', 'alpha3' => 'ATG', 'numeric' => '028', 'tld' => '.ag', 'ioc' => 'ANT' ],
		[ 'name' => 'Äquatorialguinea', 'alpha2' => 'GQ', 'alpha3' => 'GNQ', 'numeric' => '226', 'tld' => '.gq', 'ioc' => 'GEQ' ],
		[ 'name' => 'Argentinien', 'alpha2' => 'AR', 'alpha3' => 'ARG', 'numeric' => '032', 'tld' => '.ar', 'ioc' => 'ARG' ],
		[ 'name' => 'Armenien', 'alpha2' => 'AM', 'alpha3' => 'ARM', 'numeric' => '051', 'tld' => '.am', 'ioc' => 'ARM' ],
		[ 'name' => 'Aruba', 'alpha2' => 'AW', 'alpha3' => 'ABW', 'numeric' => '533', 'tld' => '.aw', 'ioc' => 'ARU' ],
		[ 'name' => 'Ascension', 'alpha2' => 'AC', 'alpha3' => 'ASC', 'numeric' => '', 'tld' => '.ac', 'ioc' => '' ],
		[ 'name' => 'Aserbaidschan', 'alpha2' => 'AZ', 'alpha3' => 'AZE', 'numeric' => '031', 'tld' => '.az', 'ioc' => 'AZE' ],
		[ 'name' => 'Äthiopien', 'alpha2' => 'ET', 'alpha3' => 'ETH', 'numeric' => '231', 'tld' => '.et', 'ioc' => 'ETH' ],
		[ 'name' => 'Australien', 'alpha2' => 'AU', 'alpha3' => 'AUS', 'numeric' => '036', 'tld' => '.au', 'ioc' => 'AUS' ],
		[ 'name' => 'Bahamas', 'alpha2' => 'BS', 'alpha3' => 'BHS', 'numeric' => '044', 'tld' => '.bs', 'ioc' => 'BAH' ],
		[ 'name' => 'Bahrain', 'alpha2' => 'BH', 'alpha3' => 'BHR', 'numeric' => '048', 'tld' => '.bh', 'ioc' => 'BRN' ],
		[ 'name' => 'Bangladesch', 'alpha2' => 'BD', 'alpha3' => 'BGD', 'numeric' => '050', 'tld' => '.bd', 'ioc' => 'BAN' ],
		[ 'name' => 'Barbados', 'alpha2' => 'BB', 'alpha3' => 'BRB', 'numeric' => '052', 'tld' => '.bb', 'ioc' => 'BAR' ],
		[ 'name' => 'Weißrussland', 'alpha2' => 'BY', 'alpha3' => 'BLR', 'numeric' => '112', 'tld' => '.by', 'ioc' => 'BLR' ],
		[ 'name' => 'Belgien', 'alpha2' => 'BE', 'alpha3' => 'BEL', 'numeric' => '056', 'tld' => '.be', 'ioc' => 'BEL' ],
		[ 'name' => 'Belize', 'alpha2' => 'BZ', 'alpha3' => 'BLZ', 'numeric' => '084', 'tld' => '.bz', 'ioc' => 'BIZ' ],
		[ 'name' => 'Benin', 'alpha2' => 'BJ', 'alpha3' => 'BEN', 'numeric' => '204', 'tld' => '.bj', 'ioc' => 'BEN' ],
		[ 'name' => 'Bermuda', 'alpha2' => 'BM', 'alpha3' => 'BMU', 'numeric' => '060', 'tld' => '.bm', 'ioc' => 'BER' ],
		[ 'name' => 'Bhutan', 'alpha2' => 'BT', 'alpha3' => 'BTN', 'numeric' => '064', 'tld' => '.bt', 'ioc' => 'BHU' ],
		[ 'name' => 'Bolivien', 'alpha2' => 'BO', 'alpha3' => 'BOL', 'numeric' => '068', 'tld' => '.bo', 'ioc' => 'BOL' ],
		[ 'name' => 'Bonaire, Sint Eustatius und Saba', 'alpha2' => 'BQ', 'alpha3' => 'BES', 'numeric' => '535', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Bosnien und Herzegowina', 'alpha2' => 'BA', 'alpha3' => 'BIH', 'numeric' => '070', 'tld' => '.ba', 'ioc' => 'BIH' ],
		[ 'name' => 'Botswana', 'alpha2' => 'BW', 'alpha3' => 'BWA', 'numeric' => '072', 'tld' => '.bw', 'ioc' => 'BOT' ],
		[ 'name' => 'Bouvetinsel', 'alpha2' => 'BV', 'alpha3' => 'BVT', 'numeric' => '074', 'tld' => '.bv', 'ioc' => '' ],
		[ 'name' => 'Brasilien', 'alpha2' => 'BR', 'alpha3' => 'BRA', 'numeric' => '076', 'tld' => '.br', 'ioc' => 'BRA' ],
		[ 'name' => 'Britische Jungferninseln', 'alpha2' => 'VG', 'alpha3' => 'VGB', 'numeric' => '092', 'tld' => '.vg', 'ioc' => 'IVB' ],
		[ 'name' => 'Britisches Territorium im Indischen Ozean', 'alpha2' => 'IO', 'alpha3' => 'IOT', 'numeric' => '086', 'tld' => '.io', 'ioc' => '' ],
		[ 'name' => 'Brunei Darussalam', 'alpha2' => 'BN', 'alpha3' => 'BRN', 'numeric' => '096', 'tld' => '.bn', 'ioc' => 'BRU' ],
		[ 'name' => 'Bulgarien', 'alpha2' => 'BG', 'alpha3' => 'BGR', 'numeric' => '100', 'tld' => '.bg', 'ioc' => 'BUL' ],
		[ 'name' => 'Burkina Faso', 'alpha2' => 'BF', 'alpha3' => 'BFA', 'numeric' => '854', 'tld' => '.bf', 'ioc' => 'BUR' ],
		[ 'name' => 'Burma', 'alpha2' => 'BU', 'alpha3' => 'BUR', 'numeric' => '104', 'tld' => '.mm', 'ioc' => '' ],
		[ 'name' => 'Burundi', 'alpha2' => 'BI', 'alpha3' => 'BDI', 'numeric' => '108', 'tld' => '.bi', 'ioc' => 'BDI' ],
		[ 'name' => 'Ceuta, Melilla', 'alpha2' => 'EA', 'alpha3' => '', 'numeric' => '', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Chile', 'alpha2' => 'CL', 'alpha3' => 'CHL', 'numeric' => '152', 'tld' => '.cl', 'ioc' => 'CHI' ],
		[ 'name' => 'China', 'alpha2' => 'CN', 'alpha3' => 'CHN', 'numeric' => '156', 'tld' => '.cn', 'ioc' => 'CHN' ],
		[ 'name' => 'Clipperton', 'alpha2' => 'CP', 'alpha3' => 'CPT', 'numeric' => '', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Cookinseln', 'alpha2' => 'CK', 'alpha3' => 'COK', 'numeric' => '184', 'tld' => '.ck', 'ioc' => 'COK' ],
		[ 'name' => 'Costa Rica', 'alpha2' => 'CR', 'alpha3' => 'CRI', 'numeric' => '188', 'tld' => '.cr', 'ioc' => 'CRC' ],
		[ 'name' => 'Elfenbeinküste', 'alpha2' => 'CI', 'alpha3' => 'CIV', 'numeric' => '384', 'tld' => '.ci', 'ioc' => 'CIV' ],
		[ 'name' => 'Curaçao', 'alpha2' => 'CW', 'alpha3' => 'CUW', 'numeric' => '531', 'tld' => '.cw', 'ioc' => '' ],
		[ 'name' => 'Dänemark', 'alpha2' => 'DK', 'alpha3' => 'DNK', 'numeric' => '208', 'tld' => '.dk', 'ioc' => 'DEN' ],
		[ 'name' => 'DDR', 'alpha2' => 'DD', 'alpha3' => '', 'numeric' => '', 'tld' => '.dd', 'ioc' => 'GDR' ],
		[ 'name' => 'BRD', 'alpha2' => 'DE', 'alpha3' => 'DEU', 'numeric' => '276', 'tld' => '.de', 'ioc' => 'FRG' ],
		[ 'name' => 'Deutschland', 'alpha2' => 'DE', 'alpha3' => 'DEU', 'numeric' => '276', 'tld' => '.de', 'ioc' => 'GER' ],
		[ 'name' => 'Diego Garcia', 'alpha2' => 'DG', 'alpha3' => 'DGA', 'numeric' => '', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Dominica', 'alpha2' => 'DM', 'alpha3' => 'DMA', 'numeric' => '212', 'tld' => '.dm', 'ioc' => 'DMA' ],
		[ 'name' => 'Dominikanische Republik', 'alpha2' => 'DO', 'alpha3' => 'DOM', 'numeric' => '214', 'tld' => '.do', 'ioc' => 'DOM' ],
		[ 'name' => 'Dschibuti', 'alpha2' => 'DJ', 'alpha3' => 'DJI', 'numeric' => '262', 'tld' => '.dj', 'ioc' => 'DJI' ],
		[ 'name' => 'Ekuador', 'alpha2' => 'EC', 'alpha3' => 'ECU', 'numeric' => '218', 'tld' => '.ec', 'ioc' => 'ECU' ],
		[ 'name' => 'El Salvador', 'alpha2' => 'SV', 'alpha3' => 'SLV', 'numeric' => '222', 'tld' => '.sv', 'ioc' => 'ESA' ],
		[ 'name' => 'Eritrea', 'alpha2' => 'ER', 'alpha3' => 'ERI', 'numeric' => '232', 'tld' => '.er', 'ioc' => 'ERI' ],
		[ 'name' => 'Estland', 'alpha2' => 'EE', 'alpha3' => 'EST', 'numeric' => '233', 'tld' => '.ee', 'ioc' => 'EST' ],
		[ 'name' => 'Falklandinseln', 'alpha2' => 'FK', 'alpha3' => 'FLK', 'numeric' => '238', 'tld' => '.fk', 'ioc' => '' ],
		[ 'name' => 'Färöer', 'alpha2' => 'FO', 'alpha3' => 'FRO', 'numeric' => '234', 'tld' => '.fo', 'ioc' => 'FRO' ],
		[ 'name' => 'Fidschi', 'alpha2' => 'FJ', 'alpha3' => 'FJI', 'numeric' => '242', 'tld' => '.fj', 'ioc' => 'FIJ' ],
		[ 'name' => 'Finnland', 'alpha2' => 'FI', 'alpha3' => 'FIN', 'numeric' => '246', 'tld' => '.fi', 'ioc' => 'FIN' ],
		[ 'name' => 'Frankreich', 'alpha2' => 'FR', 'alpha3' => 'FRA', 'numeric' => '250', 'tld' => '.fr', 'ioc' => 'FRA' ],
		[ 'name' => 'Französisch-Guayana', 'alpha2' => 'GF', 'alpha3' => 'GUF', 'numeric' => '254', 'tld' => '.gf', 'ioc' => '' ],
		[ 'name' => 'Französisch-Polynesien', 'alpha2' => 'PF', 'alpha3' => 'PYF', 'numeric' => '258', 'tld' => '.pf', 'ioc' => '' ],
		[ 'name' => 'Französische Süd- und Antarktisgebiete', 'alpha2' => 'TF', 'alpha3' => 'ATF', 'numeric' => '260', 'tld' => '.tf', 'ioc' => '' ],
		[ 'name' => 'Gabun', 'alpha2' => 'GA', 'alpha3' => 'GAB', 'numeric' => '266', 'tld' => '.ga', 'ioc' => 'GAB' ],
		[ 'name' => 'Gambia', 'alpha2' => 'GM', 'alpha3' => 'GMB', 'numeric' => '270', 'tld' => '.gm', 'ioc' => 'GAM' ],
		[ 'name' => 'Georgien', 'alpha2' => 'GE', 'alpha3' => 'GEO', 'numeric' => '268', 'tld' => '.ge', 'ioc' => 'GEO' ],
		[ 'name' => 'Ghana', 'alpha2' => 'GH', 'alpha3' => 'GHA', 'numeric' => '288', 'tld' => '.gh', 'ioc' => 'GHA' ],
		[ 'name' => 'Gibraltar', 'alpha2' => 'GI', 'alpha3' => 'GIB', 'numeric' => '292', 'tld' => '.gi', 'ioc' => '' ],
		[ 'name' => 'Grenada', 'alpha2' => 'GD', 'alpha3' => 'GRD', 'numeric' => '308', 'tld' => '.gd', 'ioc' => 'GRN' ],
		[ 'name' => 'Griechenland', 'alpha2' => 'GR', 'alpha3' => 'GRC', 'numeric' => '300', 'tld' => '.gr', 'ioc' => 'GRE' ],
		[ 'name' => 'Grönland', 'alpha2' => 'GL', 'alpha3' => 'GRL', 'numeric' => '304', 'tld' => '.gl', 'ioc' => '' ],
		[ 'name' => 'Guadeloupe', 'alpha2' => 'GP', 'alpha3' => 'GLP', 'numeric' => '312', 'tld' => '.gp', 'ioc' => '' ],
		[ 'name' => 'Guam', 'alpha2' => 'GU', 'alpha3' => 'GUM', 'numeric' => '316', 'tld' => '.gu', 'ioc' => 'GUM' ],
		[ 'name' => 'Guatemala', 'alpha2' => 'GT', 'alpha3' => 'GTM', 'numeric' => '320', 'tld' => '.gt', 'ioc' => 'GUA' ],
		[ 'name' => 'Guernsey', 'alpha2' => 'GG', 'alpha3' => 'GGY', 'numeric' => '831', 'tld' => '.gg', 'ioc' => '' ],
		[ 'name' => 'Guinea', 'alpha2' => 'GN', 'alpha3' => 'GIN', 'numeric' => '324', 'tld' => '.gn', 'ioc' => 'GUI' ],
		[ 'name' => 'Guinea-Bissau', 'alpha2' => 'GW', 'alpha3' => 'GNB', 'numeric' => '624', 'tld' => '.gw', 'ioc' => 'GBS' ],
		[ 'name' => 'Guyana', 'alpha2' => 'GY', 'alpha3' => 'GUY', 'numeric' => '328', 'tld' => '.gy', 'ioc' => 'GUY' ],
		[ 'name' => 'Haiti', 'alpha2' => 'HT', 'alpha3' => 'HTI', 'numeric' => '332', 'tld' => '.ht', 'ioc' => 'HAI' ],
		[ 'name' => 'Heard und McDonaldinseln', 'alpha2' => 'HM', 'alpha3' => 'HMD', 'numeric' => '334', 'tld' => '.hm', 'ioc' => '' ],
		[ 'name' => 'Honduras', 'alpha2' => 'HN', 'alpha3' => 'HND', 'numeric' => '340', 'tld' => '.hn', 'ioc' => 'HON' ],
		[ 'name' => 'Hongkong', 'alpha2' => 'HK', 'alpha3' => 'HKG', 'numeric' => '344', 'tld' => '.hk', 'ioc' => 'HKG' ],
		[ 'name' => 'Indien', 'alpha2' => 'IN', 'alpha3' => 'IND', 'numeric' => '356', 'tld' => '.in', 'ioc' => 'IND' ],
		[ 'name' => 'Indonesien', 'alpha2' => 'ID', 'alpha3' => 'IDN', 'numeric' => '360', 'tld' => '.id', 'ioc' => 'INA' ],
		[ 'name' => 'Insel Man', 'alpha2' => 'IM', 'alpha3' => 'IMN', 'numeric' => '833', 'tld' => '.im', 'ioc' => '' ],
		[ 'name' => 'Irak', 'alpha2' => 'IQ', 'alpha3' => 'IRQ', 'numeric' => '368', 'tld' => '.iq', 'ioc' => 'IRQ' ],
		[ 'name' => 'Iran', 'alpha2' => 'IR', 'alpha3' => 'IRN', 'numeric' => '364', 'tld' => '.ir', 'ioc' => 'IRI' ],
		[ 'name' => 'Irland', 'alpha2' => 'IE', 'alpha3' => 'IRL', 'numeric' => '372', 'tld' => '.ie', 'ioc' => 'IRL' ],
		[ 'name' => 'Island', 'alpha2' => 'IS', 'alpha3' => 'ISL', 'numeric' => '352', 'tld' => '.is', 'ioc' => 'ISL' ],
		[ 'name' => 'Israel', 'alpha2' => 'IL', 'alpha3' => 'ISR', 'numeric' => '376', 'tld' => '.il', 'ioc' => 'ISR' ],
		[ 'name' => 'Italien', 'alpha2' => 'IT', 'alpha3' => 'ITA', 'numeric' => '380', 'tld' => '.it', 'ioc' => 'ITA' ],
		[ 'name' => 'Jamaika', 'alpha2' => 'JM', 'alpha3' => 'JAM', 'numeric' => '388', 'tld' => '.jm', 'ioc' => 'JAM' ],
		[ 'name' => 'Japan', 'alpha2' => 'JP', 'alpha3' => 'JPN', 'numeric' => '392', 'tld' => '.jp', 'ioc' => 'JPN' ],
		[ 'name' => 'Jemen', 'alpha2' => 'YE', 'alpha3' => 'YEM', 'numeric' => '887', 'tld' => '.ye', 'ioc' => 'YEM' ],
		[ 'name' => 'Jersey', 'alpha2' => 'JE', 'alpha3' => 'JEY', 'numeric' => '832', 'tld' => '.je', 'ioc' => '' ],
		[ 'name' => 'Jordanien', 'alpha2' => 'JO', 'alpha3' => 'JOR', 'numeric' => '400', 'tld' => '.jo', 'ioc' => 'JOR' ],
		[ 'name' => 'Jugoslawien', 'alpha2' => 'YU', 'alpha3' => 'YUG', 'numeric' => '891', 'tld' => '.yu', 'ioc' => 'YUG' ],
		[ 'name' => 'Kaimaninseln', 'alpha2' => 'KY', 'alpha3' => 'CYM', 'numeric' => '136', 'tld' => '.ky', 'ioc' => 'CAY' ],
		[ 'name' => 'Kambodscha', 'alpha2' => 'KH', 'alpha3' => 'KHM', 'numeric' => '116', 'tld' => '.kh', 'ioc' => 'CAM' ],
		[ 'name' => 'Kamerun', 'alpha2' => 'CM', 'alpha3' => 'CMR', 'numeric' => '120', 'tld' => '.cm', 'ioc' => 'CMR' ],
		[ 'name' => 'Kanada', 'alpha2' => 'CA', 'alpha3' => 'CAN', 'numeric' => '124', 'tld' => '.ca', 'ioc' => 'CAN' ],
		[ 'name' => 'Kanarische Inseln', 'alpha2' => 'IC', 'alpha3' => '', 'numeric' => '', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Kap Verde', 'alpha2' => 'CV', 'alpha3' => 'CPV', 'numeric' => '132', 'tld' => '.cv', 'ioc' => 'CPV' ],
		[ 'name' => 'Kasachstan', 'alpha2' => 'KZ', 'alpha3' => 'KAZ', 'numeric' => '398', 'tld' => '.kz', 'ioc' => 'KAZ' ],
		[ 'name' => 'Katar', 'alpha2' => 'QA', 'alpha3' => 'QAT', 'numeric' => '634', 'tld' => '.qa', 'ioc' => 'QAT' ],
		[ 'name' => 'Kenia', 'alpha2' => 'KE', 'alpha3' => 'KEN', 'numeric' => '404', 'tld' => '.ke', 'ioc' => 'KEN' ],
		[ 'name' => 'Kirgisistan', 'alpha2' => 'KG', 'alpha3' => 'KGZ', 'numeric' => '417', 'tld' => '.kg', 'ioc' => 'KGZ' ],
		[ 'name' => 'Kiribati', 'alpha2' => 'KI', 'alpha3' => 'KIR', 'numeric' => '296', 'tld' => '.ki', 'ioc' => 'KIR' ],
		[ 'name' => 'Kokosinseln', 'alpha2' => 'CC', 'alpha3' => 'CCK', 'numeric' => '166', 'tld' => '.cc', 'ioc' => '' ],
		[ 'name' => 'Kolumbien', 'alpha2' => 'CO', 'alpha3' => 'COL', 'numeric' => '170', 'tld' => '.co', 'ioc' => 'COL' ],
		[ 'name' => 'Komoren', 'alpha2' => 'KM', 'alpha3' => 'COM', 'numeric' => '174', 'tld' => '.km', 'ioc' => 'COM' ],
		[ 'name' => 'Kongo, Demokratische Republik', 'alpha2' => 'CD', 'alpha3' => 'COD', 'numeric' => '180', 'tld' => '.cd', 'ioc' => 'COD' ],
		[ 'name' => 'Kongo, Republik', 'alpha2' => 'CG', 'alpha3' => 'COG', 'numeric' => '178', 'tld' => '.cg', 'ioc' => 'CGO' ],
		[ 'name' => 'Nordkorea', 'alpha2' => 'KP', 'alpha3' => 'PRK', 'numeric' => '408', 'tld' => '.kp', 'ioc' => 'PRK' ],
		[ 'name' => 'Südkorea', 'alpha2' => 'KR', 'alpha3' => 'KOR', 'numeric' => '410', 'tld' => '.kr', 'ioc' => 'KOR' ],
		[ 'name' => 'Kosovo', 'alpha2' => 'XK', 'alpha3' => 'XKX', 'numeric' => '', 'tld' => '', 'ioc' => 'KOS' ],
		[ 'name' => 'Kroatien', 'alpha2' => 'HR', 'alpha3' => 'HRV', 'numeric' => '191', 'tld' => '.hr', 'ioc' => 'CRO' ],
		[ 'name' => 'Kuba', 'alpha2' => 'CU', 'alpha3' => 'CUB', 'numeric' => '192', 'tld' => '.cu', 'ioc' => 'CUB' ],
		[ 'name' => 'Kuwait', 'alpha2' => 'KW', 'alpha3' => 'KWT', 'numeric' => '414', 'tld' => '.kw', 'ioc' => 'KUW' ],
		[ 'name' => 'Laos', 'alpha2' => 'LA', 'alpha3' => 'LAO', 'numeric' => '418', 'tld' => '.la', 'ioc' => 'LAO' ],
		[ 'name' => 'Lesotho', 'alpha2' => 'LS', 'alpha3' => 'LSO', 'numeric' => '426', 'tld' => '.ls', 'ioc' => 'LES' ],
		[ 'name' => 'Lettland', 'alpha2' => 'LV', 'alpha3' => 'LVA', 'numeric' => '428', 'tld' => '.lv', 'ioc' => 'LAT' ],
		[ 'name' => 'Libanon', 'alpha2' => 'LB', 'alpha3' => 'LBN', 'numeric' => '422', 'tld' => '.lb', 'ioc' => 'LIB' ],
		[ 'name' => 'Liberia', 'alpha2' => 'LR', 'alpha3' => 'LBR', 'numeric' => '430', 'tld' => '.lr', 'ioc' => 'LBR' ],
		[ 'name' => 'Libyen', 'alpha2' => 'LY', 'alpha3' => 'LBY', 'numeric' => '434', 'tld' => '.ly', 'ioc' => 'LBA' ],
		[ 'name' => 'Liechtenstein', 'alpha2' => 'LI', 'alpha3' => 'LIE', 'numeric' => '438', 'tld' => '.li', 'ioc' => 'LIE' ],
		[ 'name' => 'Litauen', 'alpha2' => 'LT', 'alpha3' => 'LTU', 'numeric' => '440', 'tld' => '.lt', 'ioc' => 'LTU' ],
		[ 'name' => 'Luxemburg', 'alpha2' => 'LU', 'alpha3' => 'LUX', 'numeric' => '442', 'tld' => '.lu', 'ioc' => 'LUX' ],
		[ 'name' => 'Macau', 'alpha2' => 'MO', 'alpha3' => 'MAC', 'numeric' => '446', 'tld' => '.mo', 'ioc' => '' ],
		[ 'name' => 'Madagaskar', 'alpha2' => 'MG', 'alpha3' => 'MDG', 'numeric' => '450', 'tld' => '.mg', 'ioc' => 'MAD' ],
		[ 'name' => 'Malawi', 'alpha2' => 'MW', 'alpha3' => 'MWI', 'numeric' => '454', 'tld' => '.mw', 'ioc' => 'MAW' ],
		[ 'name' => 'Malaysia', 'alpha2' => 'MY', 'alpha3' => 'MYS', 'numeric' => '458', 'tld' => '.my', 'ioc' => 'MAS' ],
		[ 'name' => 'Malediven', 'alpha2' => 'MV', 'alpha3' => 'MDV', 'numeric' => '462', 'tld' => '.mv', 'ioc' => 'MDV' ],
		[ 'name' => 'Mali', 'alpha2' => 'ML', 'alpha3' => 'MLI', 'numeric' => '466', 'tld' => '.ml', 'ioc' => 'MLI' ],
		[ 'name' => 'Malta', 'alpha2' => 'MT', 'alpha3' => 'MLT', 'numeric' => '470', 'tld' => '.mt', 'ioc' => 'MLT' ],
		[ 'name' => 'Marokko', 'alpha2' => 'MA', 'alpha3' => 'MAR', 'numeric' => '504', 'tld' => '.ma', 'ioc' => 'MAR' ],
		[ 'name' => 'Marshallinseln', 'alpha2' => 'MH', 'alpha3' => 'MHL', 'numeric' => '584', 'tld' => '.mh', 'ioc' => 'MHL' ],
		[ 'name' => 'Martinique', 'alpha2' => 'MQ', 'alpha3' => 'MTQ', 'numeric' => '474', 'tld' => '.mq', 'ioc' => '' ],
		[ 'name' => 'Mauretanien', 'alpha2' => 'MR', 'alpha3' => 'MRT', 'numeric' => '478', 'tld' => '.mr', 'ioc' => 'MTN' ],
		[ 'name' => 'Mauritius', 'alpha2' => 'MU', 'alpha3' => 'MUS', 'numeric' => '480', 'tld' => '.mu', 'ioc' => 'MRI' ],
		[ 'name' => 'Mayotte', 'alpha2' => 'YT', 'alpha3' => 'MYT', 'numeric' => '175', 'tld' => '.yt', 'ioc' => '' ],
		[ 'name' => 'Mexiko', 'alpha2' => 'MX', 'alpha3' => 'MEX', 'numeric' => '484', 'tld' => '.mx', 'ioc' => 'MEX' ],
		[ 'name' => 'Mikronesien', 'alpha2' => 'FM', 'alpha3' => 'FSM', 'numeric' => '583', 'tld' => '.fm', 'ioc' => 'FSM' ],
		[ 'name' => 'Moldawien', 'alpha2' => 'MD', 'alpha3' => 'MDA', 'numeric' => '498', 'tld' => '.md', 'ioc' => 'MDA' ],
		[ 'name' => 'Monaco', 'alpha2' => 'MC', 'alpha3' => 'MCO', 'numeric' => '492', 'tld' => '.mc', 'ioc' => 'MON' ],
		[ 'name' => 'Mongolei', 'alpha2' => 'MN', 'alpha3' => 'MNG', 'numeric' => '496', 'tld' => '.mn', 'ioc' => 'MGL' ],
		[ 'name' => 'Montenegro', 'alpha2' => 'ME', 'alpha3' => 'MNE', 'numeric' => '499', 'tld' => '.me', 'ioc' => 'MNE' ],
		[ 'name' => 'Montserrat', 'alpha2' => 'MS', 'alpha3' => 'MSR', 'numeric' => '500', 'tld' => '.ms', 'ioc' => '' ],
		[ 'name' => 'Mosambik', 'alpha2' => 'MZ', 'alpha3' => 'MOZ', 'numeric' => '508', 'tld' => '.mz', 'ioc' => 'MOZ' ],
		[ 'name' => 'Myanmar', 'alpha2' => 'MM', 'alpha3' => 'MMR', 'numeric' => '104', 'tld' => '.mm', 'ioc' => 'MYA' ],
		[ 'name' => 'Namibia', 'alpha2' => 'NA', 'alpha3' => 'NAM', 'numeric' => '516', 'tld' => '.na', 'ioc' => 'NAM' ],
		[ 'name' => 'Nauru', 'alpha2' => 'NR', 'alpha3' => 'NRU', 'numeric' => '520', 'tld' => '.nr', 'ioc' => 'NRU' ],
		[ 'name' => 'Nepal', 'alpha2' => 'NP', 'alpha3' => 'NPL', 'numeric' => '524', 'tld' => '.np', 'ioc' => 'NEP' ],
		[ 'name' => 'Neukaledonien', 'alpha2' => 'NC', 'alpha3' => 'NCL', 'numeric' => '540', 'tld' => '.nc', 'ioc' => '' ],
		[ 'name' => 'Neuseeland', 'alpha2' => 'NZ', 'alpha3' => 'NZL', 'numeric' => '554', 'tld' => '.nz', 'ioc' => 'NZL' ],
		[ 'name' => 'Neutrale Zone', 'alpha2' => 'NT', 'alpha3' => 'NTZ', 'numeric' => '536', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Nicaragua', 'alpha2' => 'NI', 'alpha3' => 'NIC', 'numeric' => '558', 'tld' => '.ni', 'ioc' => 'NCA' ],
		[ 'name' => 'Niederlande', 'alpha2' => 'NL', 'alpha3' => 'NLD', 'numeric' => '528', 'tld' => '.nl', 'ioc' => 'NED' ],
		[ 'name' => 'Niederländische Antillen', 'alpha2' => 'AN', 'alpha3' => 'ANT', 'numeric' => '530', 'tld' => '.an', 'ioc' => 'AHO' ],
		[ 'name' => 'Niger', 'alpha2' => 'NE', 'alpha3' => 'NER', 'numeric' => '562', 'tld' => '.ne', 'ioc' => 'NIG' ],
		[ 'name' => 'Nigeria', 'alpha2' => 'NG', 'alpha3' => 'NGA', 'numeric' => '566', 'tld' => '.ng', 'ioc' => 'NGR' ],
		[ 'name' => 'Niue', 'alpha2' => 'NU', 'alpha3' => 'NIU', 'numeric' => '570', 'tld' => '.nu', 'ioc' => '' ],
		[ 'name' => 'Nördliche Marianen', 'alpha2' => 'MP', 'alpha3' => 'MNP', 'numeric' => '580', 'tld' => '.mp', 'ioc' => '' ],
		[ 'name' => 'Nordmazedonien', 'alpha2' => 'MK', 'alpha3' => 'MKD', 'numeric' => '807', 'tld' => '.mk', 'ioc' => 'MKD' ],
		[ 'name' => 'Norfolkinsel', 'alpha2' => 'NF', 'alpha3' => 'NFK', 'numeric' => '574', 'tld' => '.nf', 'ioc' => '' ],
		[ 'name' => 'Norwegen', 'alpha2' => 'NO', 'alpha3' => 'NOR', 'numeric' => '578', 'tld' => '.no', 'ioc' => 'NOR' ],
		[ 'name' => 'Oman', 'alpha2' => 'OM', 'alpha3' => 'OMN', 'numeric' => '512', 'tld' => '.om', 'ioc' => 'OMA' ],
		[ 'name' => 'Österreich', 'alpha2' => 'AT', 'alpha3' => 'AUT', 'numeric' => '040', 'tld' => '.at', 'ioc' => 'AUT' ],
		[ 'name' => 'Osttimor', 'alpha2' => 'TL    )', 'alpha3' => 'TLS', 'numeric' => '626', 'tld' => '.tl', 'ioc' => 'TLS' ],
		[ 'name' => 'Pakistan', 'alpha2' => 'PK', 'alpha3' => 'PAK', 'numeric' => '586', 'tld' => '.pk', 'ioc' => 'PAK' ],
		[ 'name' => 'Palästina', 'alpha2' => 'PS', 'alpha3' => 'PSE', 'numeric' => '275', 'tld' => '.ps', 'ioc' => 'PLE' ],
		[ 'name' => 'Palau', 'alpha2' => 'PW', 'alpha3' => 'PLW', 'numeric' => '585', 'tld' => '.pw', 'ioc' => 'PLW' ],
		[ 'name' => 'Panama', 'alpha2' => 'PA', 'alpha3' => 'PAN', 'numeric' => '591', 'tld' => '.pa', 'ioc' => 'PAN' ],
		[ 'name' => 'Papua-Neuguinea', 'alpha2' => 'PG', 'alpha3' => 'PNG', 'numeric' => '598', 'tld' => '.pg', 'ioc' => 'PNG' ],
		[ 'name' => 'Paraguay', 'alpha2' => 'PY', 'alpha3' => 'PRY', 'numeric' => '600', 'tld' => '.py', 'ioc' => 'PAR' ],
		[ 'name' => 'Peru', 'alpha2' => 'PE', 'alpha3' => 'PER', 'numeric' => '604', 'tld' => '.pe', 'ioc' => 'PER' ],
		[ 'name' => 'Philippinen', 'alpha2' => 'PH', 'alpha3' => 'PHL', 'numeric' => '608', 'tld' => '.ph', 'ioc' => 'PHI' ],
		[ 'name' => 'Pitcairninseln', 'alpha2' => 'PN', 'alpha3' => 'PCN', 'numeric' => '612', 'tld' => '.pn', 'ioc' => '' ],
		[ 'name' => 'Polen', 'alpha2' => 'PL', 'alpha3' => 'POL', 'numeric' => '616', 'tld' => '.pl', 'ioc' => 'POL' ],
		[ 'name' => 'Portugal', 'alpha2' => 'PT', 'alpha3' => 'PRT', 'numeric' => '620', 'tld' => '.pt', 'ioc' => 'POR' ],
		[ 'name' => 'Puerto Rico', 'alpha2' => 'PR', 'alpha3' => 'PRI', 'numeric' => '630', 'tld' => '.pr', 'ioc' => 'PUR' ],
		[ 'name' => 'Réunion', 'alpha2' => 'RE', 'alpha3' => 'REU', 'numeric' => '638', 'tld' => '.re', 'ioc' => '' ],
		[ 'name' => 'Ruanda', 'alpha2' => 'RW', 'alpha3' => 'RWA', 'numeric' => '646', 'tld' => '.rw', 'ioc' => 'RWA' ],
		[ 'name' => 'Rumänien', 'alpha2' => 'RO', 'alpha3' => 'ROU', 'numeric' => '642', 'tld' => '.ro', 'ioc' => 'ROU' ],
		[ 'name' => 'Russland', 'alpha2' => 'RU', 'alpha3' => 'RUS', 'numeric' => '643', 'tld' => '.ru', 'ioc' => 'RUS' ],
		[ 'name' => 'Salomonen', 'alpha2' => 'SB', 'alpha3' => 'SLB', 'numeric' => '090', 'tld' => '.sb', 'ioc' => 'SOL' ],
		[ 'name' => 'Saint-Barthélemy', 'alpha2' => 'BL', 'alpha3' => 'BLM', 'numeric' => '652', 'tld' => '.bl', 'ioc' => '' ],
		[ 'name' => 'Saint-Martin', 'alpha2' => 'MF', 'alpha3' => 'MAF', 'numeric' => '663', 'tld' => '.mf', 'ioc' => '' ],
		[ 'name' => 'Sambia', 'alpha2' => 'ZM', 'alpha3' => 'ZMB', 'numeric' => '894', 'tld' => '.zm', 'ioc' => 'ZAM' ],
		[ 'name' => 'Samoa', 'alpha2' => 'WS', 'alpha3' => 'WSM', 'numeric' => '882', 'tld' => '.ws', 'ioc' => 'SAM' ],
		[ 'name' => 'San Marino', 'alpha2' => 'SM', 'alpha3' => 'SMR', 'numeric' => '674', 'tld' => '.sm', 'ioc' => 'SMR' ],
		[ 'name' => 'São Tomé und Príncipe', 'alpha2' => 'ST', 'alpha3' => 'STP', 'numeric' => '678', 'tld' => '.st', 'ioc' => 'STP' ],
		[ 'name' => 'Saudi-Arabien', 'alpha2' => 'SA', 'alpha3' => 'SAU', 'numeric' => '682', 'tld' => '.sa', 'ioc' => 'KSA' ],
		[ 'name' => 'Schweden', 'alpha2' => 'SE', 'alpha3' => 'SWE', 'numeric' => '752', 'tld' => '.se', 'ioc' => 'SWE' ],
		[ 'name' => 'Schweiz', 'alpha2' => 'CH', 'alpha3' => 'CHE', 'numeric' => '756', 'tld' => '.ch', 'ioc' => 'SUI' ],
		[ 'name' => 'Senegal', 'alpha2' => 'SN', 'alpha3' => 'SEN', 'numeric' => '686', 'tld' => '.sn', 'ioc' => 'SEN' ],
		[ 'name' => 'Serbien', 'alpha2' => 'RS', 'alpha3' => 'SRB', 'numeric' => '688', 'tld' => '.rs', 'ioc' => 'SRB' ],
		[ 'name' => 'Serbien und Montenegro', 'alpha2' => 'CS', 'alpha3' => 'SCG', 'numeric' => '891', 'tld' => '.yu', 'ioc' => 'SCG' ],
		[ 'name' => 'Seychellen', 'alpha2' => 'SC', 'alpha3' => 'SYC', 'numeric' => '690', 'tld' => '.sc', 'ioc' => 'SEY' ],
		[ 'name' => 'Sierra Leone', 'alpha2' => 'SL', 'alpha3' => 'SLE', 'numeric' => '694', 'tld' => '.sl', 'ioc' => 'SLE' ],
		[ 'name' => 'Simbabwe', 'alpha2' => 'ZW', 'alpha3' => 'ZWE', 'numeric' => '716', 'tld' => '.zw', 'ioc' => 'ZIM' ],
		[ 'name' => 'Singapur', 'alpha2' => 'SG', 'alpha3' => 'SGP', 'numeric' => '702', 'tld' => '.sg', 'ioc' => 'SGP' ],
		[ 'name' => 'Sint Maarten', 'alpha2' => 'SX', 'alpha3' => 'SXM', 'numeric' => '534', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Slowakei', 'alpha2' => 'SK', 'alpha3' => 'SVK', 'numeric' => '703', 'tld' => '.sk', 'ioc' => 'SVK' ],
		[ 'name' => 'Slowenien', 'alpha2' => 'SI', 'alpha3' => 'SVN', 'numeric' => '705', 'tld' => '.si', 'ioc' => 'SLO' ],
		[ 'name' => 'Somalia', 'alpha2' => 'SO', 'alpha3' => 'SOM', 'numeric' => '706', 'tld' => '.so', 'ioc' => 'SOM' ],
		[ 'name' => 'Spanien', 'alpha2' => 'ES', 'alpha3' => 'ESP', 'numeric' => '724', 'tld' => '.es', 'ioc' => 'ESP' ],
		[ 'name' => 'Sri Lanka', 'alpha2' => 'LK', 'alpha3' => 'LKA', 'numeric' => '144', 'tld' => '.lk', 'ioc' => 'SRI' ],
		[ 'name' => 'St. Helena', 'alpha2' => 'SH', 'alpha3' => 'SHN', 'numeric' => '654', 'tld' => '.sh', 'ioc' => '' ],
		[ 'name' => 'St. Kitts und Nevis', 'alpha2' => 'KN', 'alpha3' => 'KNA', 'numeric' => '659', 'tld' => '.kn', 'ioc' => 'SKN' ],
		[ 'name' => 'St. Lucia', 'alpha2' => 'LC', 'alpha3' => 'LCA', 'numeric' => '662', 'tld' => '.lc', 'ioc' => 'LCA' ],
		[ 'name' => 'Saint-Pierre und Miquelon', 'alpha2' => 'PM', 'alpha3' => 'SPM', 'numeric' => '666', 'tld' => '.pm', 'ioc' => '' ],
		[ 'name' => 'St. Vincent und die Grenadinen', 'alpha2' => 'VC', 'alpha3' => 'VCT', 'numeric' => '670', 'tld' => '.vc', 'ioc' => 'VIN' ],
		[ 'name' => 'Südafrika', 'alpha2' => 'ZA', 'alpha3' => 'ZAF', 'numeric' => '710', 'tld' => '.za', 'ioc' => 'RSA' ],
		[ 'name' => 'Sudan', 'alpha2' => 'SD', 'alpha3' => 'SDN', 'numeric' => '729', 'tld' => '.sd', 'ioc' => 'SUD' ],
		[ 'name' => 'Südgeorgien und die Südlichen Sandwichinseln', 'alpha2' => 'GS', 'alpha3' => 'SGS', 'numeric' => '239', 'tld' => '.gs', 'ioc' => '' ],
		[ 'name' => 'Südsudan', 'alpha2' => 'SS', 'alpha3' => 'SSD', 'numeric' => '728', 'tld' => '.ss', 'ioc' => 'SSD' ],
		[ 'name' => 'Suriname', 'alpha2' => 'SR', 'alpha3' => 'SUR', 'numeric' => '740', 'tld' => '.sr', 'ioc' => 'SUR' ],
		[ 'name' => 'Svalbard und Jan Mayen', 'alpha2' => 'SJ', 'alpha3' => 'SJM', 'numeric' => '744', 'tld' => '.sj', 'ioc' => '' ],
		[ 'name' => 'Swasiland', 'alpha2' => 'SZ', 'alpha3' => 'SWZ', 'numeric' => '748', 'tld' => '.sz', 'ioc' => 'SWZ' ],
		[ 'name' => 'Syrien', 'alpha2' => 'SY', 'alpha3' => 'SYR', 'numeric' => '760', 'tld' => '.sy', 'ioc' => 'SYR' ],
		[ 'name' => 'Tadschikistan', 'alpha2' => 'TJ', 'alpha3' => 'TJK', 'numeric' => '762', 'tld' => '.tj', 'ioc' => 'TJK' ],
		[ 'name' => 'Republik China', 'alpha2' => 'TW', 'alpha3' => 'TWN', 'numeric' => '158', 'tld' => '.tw', 'ioc' => 'TPE' ],
		[ 'name' => 'Tansania', 'alpha2' => 'TZ', 'alpha3' => 'TZA', 'numeric' => '834', 'tld' => '.tz', 'ioc' => 'TAN' ],
		[ 'name' => 'Thailand', 'alpha2' => 'TH', 'alpha3' => 'THA', 'numeric' => '764', 'tld' => '.th', 'ioc' => 'THA' ],
		[ 'name' => 'Togo', 'alpha2' => 'TG', 'alpha3' => 'TGO', 'numeric' => '768', 'tld' => '.tg', 'ioc' => 'TOG' ],
		[ 'name' => 'Tokelau', 'alpha2' => 'TK', 'alpha3' => 'TKL', 'numeric' => '772', 'tld' => '.tk', 'ioc' => '' ],
		[ 'name' => 'Tonga', 'alpha2' => 'TO', 'alpha3' => 'TON', 'numeric' => '776', 'tld' => '.to', 'ioc' => 'TGA' ],
		[ 'name' => 'Trinidad und Tobago', 'alpha2' => 'TT', 'alpha3' => 'TTO', 'numeric' => '780', 'tld' => '.tt', 'ioc' => 'TRI' ],
		[ 'name' => 'Tristan da Cunha', 'alpha2' => 'TA', 'alpha3' => 'TAA', 'numeric' => '', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Tschad', 'alpha2' => 'TD', 'alpha3' => 'TCD', 'numeric' => '148', 'tld' => '.td', 'ioc' => 'CHA' ],
		[ 'name' => 'Tschechien', 'alpha2' => 'CZ', 'alpha3' => 'CZE', 'numeric' => '203', 'tld' => '.cz', 'ioc' => 'CZE' ],
		[ 'name' => 'Tschechoslowakei', 'alpha2' => 'CS', 'alpha3' => 'CSK', 'numeric' => '200', 'tld' => '.cs', 'ioc' => 'TCH' ],
		[ 'name' => 'Tunesien', 'alpha2' => 'TN', 'alpha3' => 'TUN', 'numeric' => '788', 'tld' => '.tn', 'ioc' => 'TUN' ],
		[ 'name' => 'Türkei', 'alpha2' => 'TR', 'alpha3' => 'TUR', 'numeric' => '792', 'tld' => '.tr', 'ioc' => 'TUR' ],
		[ 'name' => 'Turkmenistan', 'alpha2' => 'TM', 'alpha3' => 'TKM', 'numeric' => '795', 'tld' => '.tm', 'ioc' => 'TKM' ],
		[ 'name' => 'Turks- und Caicosinseln', 'alpha2' => 'TC', 'alpha3' => 'TCA', 'numeric' => '796', 'tld' => '.tc', 'ioc' => '' ],
		[ 'name' => 'Tuvalu', 'alpha2' => 'TV', 'alpha3' => 'TUV', 'numeric' => '798', 'tld' => '.tv', 'ioc' => 'TUV' ],
		[ 'name' => 'UdSSR', 'alpha2' => 'SU', 'alpha3' => 'SUN', 'numeric' => '810', 'tld' => '.su', 'ioc' => 'URS' ],
		[ 'name' => 'Uganda', 'alpha2' => 'UG', 'alpha3' => 'UGA', 'numeric' => '800', 'tld' => '.ug', 'ioc' => 'UGA' ],
		[ 'name' => 'Ukraine', 'alpha2' => 'UA', 'alpha3' => 'UKR', 'numeric' => '804', 'tld' => '.ua', 'ioc' => 'UKR' ],
		[ 'name' => 'Ungarn', 'alpha2' => 'HU', 'alpha3' => 'HUN', 'numeric' => '348', 'tld' => '.hu', 'ioc' => 'HUN' ],
		[ 'name' => 'United States Minor Outlying Islands', 'alpha2' => 'UM', 'alpha3' => 'UMI', 'numeric' => '581', 'tld' => '.um', 'ioc' => '' ],
		[ 'name' => 'Uruguay', 'alpha2' => 'UY', 'alpha3' => 'URY', 'numeric' => '858', 'tld' => '.uy', 'ioc' => 'URU' ],
		[ 'name' => 'Usbekistan', 'alpha2' => 'UZ', 'alpha3' => 'UZB', 'numeric' => '860', 'tld' => '.uz', 'ioc' => 'UZB' ],
		[ 'name' => 'Vanuatu', 'alpha2' => 'VU', 'alpha3' => 'VUT', 'numeric' => '548', 'tld' => '.vu', 'ioc' => 'VAN' ],
		[ 'name' => 'Vatikanstadt', 'alpha2' => 'VA', 'alpha3' => 'VAT', 'numeric' => '336', 'tld' => '.va', 'ioc' => '' ],
		[ 'name' => 'Venezuela', 'alpha2' => 'VE', 'alpha3' => 'VEN', 'numeric' => '862', 'tld' => '.ve', 'ioc' => 'VEN' ],
		[ 'name' => 'Vereinigte Arabische Emirate', 'alpha2' => 'AE', 'alpha3' => 'ARE', 'numeric' => '784', 'tld' => '.ae', 'ioc' => 'UAE' ],
		[ 'name' => 'USA', 'alpha2' => 'US', 'alpha3' => 'USA', 'numeric' => '840', 'tld' => '.us', 'ioc' => 'USA' ],
		[ 'name' => 'Großbritannien', 'alpha2' => 'GB', 'alpha3' => 'GBR', 'numeric' => '826', 'tld' => '.uk', 'ioc' => 'GBR' ],
		[ 'name' => 'Vietnam', 'alpha2' => 'VN', 'alpha3' => 'VNM', 'numeric' => '704', 'tld' => '.vn', 'ioc' => 'VIE' ],
		[ 'name' => 'Wallis und Futuna', 'alpha2' => 'WF', 'alpha3' => 'WLF', 'numeric' => '876', 'tld' => '.wf', 'ioc' => '' ],
		[ 'name' => 'Weihnachtsinsel', 'alpha2' => 'CX', 'alpha3' => 'CXR', 'numeric' => '162', 'tld' => '.cx', 'ioc' => '' ],
		[ 'name' => 'Westsahara', 'alpha2' => 'EH', 'alpha3' => 'ESH', 'numeric' => '732', 'tld' => '.eh', 'ioc' => '' ],
		[ 'name' => 'Zaire', 'alpha2' => 'ZR', 'alpha3' => 'ZAR', 'numeric' => '180', 'tld' => '', 'ioc' => '' ],
		[ 'name' => 'Zentralafrikanische Republik', 'alpha2' => 'CF', 'alpha3' => 'CAF', 'numeric' => '140', 'tld' => '.cf', 'ioc' => 'CAF' ],
		[ 'name' => 'Zypern', 'alpha2' => 'CY', 'alpha3' => 'CYP', 'numeric' => '196', 'tld' => '.cy', 'ioc' => 'CYP' ],
		// weitere Länder, abgeleitet von Großbritannien
		[ 'name' => 'Schottland', 'alpha2' => 'GB-SCT', 'alpha3' => 'SCT', 'numeric' => '826', 'tld' => '.uk', 'ioc' => 'SCO' ],
		[ 'name' => 'England', 'alpha2' => 'GB-ENG', 'alpha3' => 'GBR', 'numeric' => '826', 'tld' => '.uk', 'ioc' => 'ENG' ],
		[ 'name' => 'Nordirland', 'alpha2' => 'GB-NIR', 'alpha3' => 'NIR', 'numeric' => '826', 'tld' => '.uk', 'ioc' => 'NIR' ],
		[ 'name' => 'Wales', 'alpha2' => 'GB-WLS', 'alpha3' => 'WLS', 'numeric' => '826', 'tld' => '.uk', 'ioc' => 'WAL' ],
	];
}

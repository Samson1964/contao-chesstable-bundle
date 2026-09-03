<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\ContentElements;

use Contao\Config;
use Contao\ContentElement;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoChesstableBundle\Util\CountryCodes;
use Schachbulle\ContaoChesstableBundle\Util\TableHelper;

/**
 * Inhaltselement, das CSV-Daten als Schachtabelle ausgibt.
 *
 * Die Daten werden zeilenweise mit Semikolon als Spaltentrenner erwartet. Aus
 * der ersten Zeile wird anhand der Spaltenüberschriften ermittelt, welche Art
 * von Werten eine Spalte enthält (Name, Nation, Ergebnis usw.); daraus ergeben
 * sich die CSS-Klassen und Sonderbehandlungen wie das Drehen von Namen oder das
 * Anzeigen von Flaggen. Welche Überschriften zu welcher Spaltenart gehören,
 * legt der Administrator in den Systemeinstellungen fest.
 *
 * Die folgenden Eigenschaften stammen aus der Tabelle tl_content und werden von
 * Contao über __get() bereitgestellt; sie sind hier aufgeführt, damit statische
 * Codeprüfungen sie kennen.
 *
 * @property string $chesstable_csv           Die Tabellendaten im CSV-Format
 * @property string $chesstable_autoNumber    "1", wenn eine Nummernspalte vorangestellt wird
 * @property string $chesstable_markBold      Zeilennummern, die fett erscheinen
 * @property string $chesstable_markItalic    Zeilennummern, die kursiv erscheinen
 * @property string $chesstable_markierungen  Serialisierte Zuordnung von Farbmarkierungen
 * @property string $chesstable_namendrehen   "1", wenn Namen umgedreht werden
 * @property string $chesstable_flaggen       "1", wenn Flaggen statt Länderkürzel erscheinen
 * @property string $chesstable_date          "1", wenn das Änderungsdatum erscheint
 * @property string $chesstable_ende          Turnierende als Zeitstempel
 * @property string $chesstable_note          Hinweistext unterhalb der Tabelle
 * @property string $chesstable_lightbox      "1", wenn die Tabelle in einer Lightbox erscheint
 * @property string $chesstable_linktext      Beschriftung des Lightbox-Links
 * @property string $chesstable_punkteFormat  "1", wenn Punkte mit einer Nachkommastelle erscheinen
 * @property string $chesstable_vereinKuerzen "1", wenn Rechtsform-Zusätze aus Vereinsnamen entfernt werden
 */
class Chesstable extends ContentElement
{
	/**
	 * Vorlage für die normale Ausgabe.
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_chesstable';

	/**
	 * Vorlage für die Ausgabe in einer Lightbox.
	 *
	 * @var string
	 */
	protected $strTemplateLightbox = 'ce_chesstable_lightbox';

	/**
	 * Reihenfolge der Spaltenarten.
	 *
	 * Der Index entspricht der Nummer der Spaltenart, wie sie beim Auswerten der
	 * Kopfzeile vergeben wird; der Wert ist die CSS-Klasse der Zelle. Index 0
	 * steht für eine Spalte, die zu keiner konfigurierten Art passt.
	 */
	private const COLUMN_CLASSES = ['text', 'nation', 'place', 'club', 'name', 'points', 'rating', 'elo', 'result', 'color', 'control'];

	/**
	 * Namen der Einstellungsfelder je Spaltenart, passend zu COLUMN_CLASSES.
	 *
	 * Der erste Eintrag bleibt leer, weil die Spaltenart 0 (unbekannte Spalte)
	 * nicht konfiguriert wird.
	 */
	private const COLUMN_SETTINGS = ['', 'chesstable_nationfelder', 'chesstable_platzfelder', 'chesstable_vereinfelder', 'chesstable_namenfelder', 'chesstable_punktefelder', 'chesstable_wertungfelder', 'chesstable_ratingfelder', 'chesstable_ergebnisfelder', 'chesstable_farbfelder', 'chesstable_steuerfelder'];

	/**
	 * Ergebnis der Suche nach dem Flaggen-Stylesheet.
	 *
	 * Null bedeutet "noch nicht ermittelt". Der Wert wird für die Dauer des
	 * Aufrufs gemerkt, damit bei mehreren Tabellen auf einer Seite nicht
	 * wiederholt versucht wird, den Symlink anzulegen.
	 *
	 * @var string|null
	 */
	private static $strFlagIconCss;

	/**
	 * Wählt vor dem Rendern die passende Vorlage aus.
	 *
	 * Die Auswahl muss hier und nicht erst in compile() erfolgen: Contao erzeugt
	 * das Template-Objekt in der Elternmethode und füllt es anschließend mit den
	 * Standardwerten des Inhaltselements (Überschrift, CSS-ID, Randabstände).
	 * Würde man das Objekt nachträglich austauschen - so hat es dieses Bundle bis
	 * Version 4.1.3 gemacht -, gingen genau diese Werte verloren.
	 *
	 * @return string Das gerenderte Inhaltselement
	 */
	public function generate()
	{
		if ($this->chesstable_lightbox)
		{
			$this->strTemplate = $this->strTemplateLightbox;
		}

		return parent::generate();
	}

	/**
	 * Baut die Tabelle auf und übergibt sie an die Vorlage.
	 *
	 * Nebenwirkungen: Die Methode trägt bei Bedarf das Stylesheet des Bundles und
	 * das der Flaggenbibliothek in $GLOBALS['TL_CSS'] ein und legt dafür unter
	 * Umständen einen Symlink im öffentlichen Verzeichnis an (siehe
	 * getFlagIconCss()).
	 */
	protected function compile(): void
	{
		$spalten = $this->getColumnSettings();
		$blindfelder = $this->getConfigList('chesstable_blindfelder');
		$markierungen = $this->getMarkings();

		[$tabelle, $eigenklassen] = $this->parseCsv((string) ($this->chesstable_csv ?? ''));

		if ($this->chesstable_autoNumber)
		{
			[$tabelle, $eigenklassen] = $this->addAutoNumbers($tabelle, $eigenklassen);
		}

		// Spaltenarten erst jetzt bestimmen, weil die automatische Nummerierung
		// eine zusätzliche Spalte an den Anfang setzt und alle anderen verschiebt
		[$spaltenart, $steuerspalte] = $this->detectColumnTypes($tabelle[0] ?? [], $spalten);

		$flaggenCss = $this->chesstable_flaggen ? $this->getFlagIconCss() : '';

		$content = $this->renderTable($tabelle, $eigenklassen, $spaltenart, $steuerspalte, $blindfelder, $markierungen, $flaggenCss !== '');

		// Inserttags erst zum Schluss ersetzen, damit die Auswertung der Zellen
		// auf den unveränderten Werten stattfindet
		$content = System::getContainer()->get('contao.insert_tag.parser')->replace($content);

		$this->Template->tabelle = $content;
		$this->Template->datum = $this->chesstable_date ? (int) $this->tstamp : 0;
		$this->Template->turnierende = (int) $this->chesstable_ende;
		$this->Template->hinweis = $this->chesstable_note;
		$this->Template->linktext = $this->chesstable_linktext ?: 'Alternativtabelle';

		if (Config::get('chesstable_css'))
		{
			$GLOBALS['TL_CSS'][] = 'bundles/contaochesstable/default.css';
		}

		if ($flaggenCss !== '')
		{
			$GLOBALS['TL_CSS'][] = $flaggenCss;
		}
	}

	/**
	 * Liest die konfigurierten Spaltenüberschriften je Spaltenart ein.
	 *
	 * @return array<int, string[]> Je Spaltenart (Schlüssel entspricht dem Index
	 *                              in COLUMN_CLASSES) die Liste der zugehörigen
	 *                              Überschriften in Kleinschreibung
	 */
	private function getColumnSettings(): array
	{
		$spalten = [];

		foreach (self::COLUMN_SETTINGS as $art => $feld)
		{
			if ($feld !== '')
			{
				$spalten[$art] = $this->getConfigList($feld);
			}
		}

		return $spalten;
	}

	/**
	 * Zerlegt eine Einstellung mit Kommaliste in ein Array aus Kleinbuchstaben.
	 *
	 * @param string $feld Name der Einstellung, z. B. "chesstable_namenfelder"
	 *
	 * @return string[] Die einzelnen Begriffe, getrimmt und kleingeschrieben;
	 *                  ein leeres Array, wenn die Einstellung nicht gepflegt ist
	 */
	private function getConfigList(string $feld): array
	{
		$wert = (string) (Config::get($feld) ?? '');

		if (trim($wert) === '')
		{
			return [];
		}

		return array_map(static fn (string $begriff): string => strtolower(trim($begriff)), explode(',', $wert));
	}

	/**
	 * Stellt die farblichen Markierungen aus Einstellungen und Element zusammen.
	 *
	 * Die Farben selbst kommen aus den Systemeinstellungen, die Zuordnung zu
	 * Zeilennummern und Ländern aus dem Inhaltselement. Beides wird hier zu einer
	 * gemeinsamen Struktur verschmolzen. Markierungen, die im Inhaltselement
	 * gespeichert sind, in den Einstellungen aber nicht mehr existieren, bleiben
	 * ohne Farbe erhalten - so gehen die Zeilenangaben nicht verloren, wenn eine
	 * Markierung vorübergehend aus den Einstellungen entfernt wird.
	 *
	 * @return array<string, array{color: string, rows: int[], flags: string[]}>
	 *         Je internem Schlüssel (z. B. "up") die Farbe als Hexwert mit
	 *         führendem Doppelkreuz sowie die markierten Zeilen und Länder
	 */
	private function getMarkings(): array
	{
		$markierungen = [];

		foreach (StringUtil::deserialize(Config::get('chesstable_markColors'), true) as $eintrag)
		{
			if (!\is_array($eintrag) || empty($eintrag['intern']))
			{
				continue;
			}

			$markierungen[$eintrag['intern']] = [
				'color' => !empty($eintrag['color']) ? '#'.$eintrag['color'] : '',
				'rows' => [],
				'flags' => [],
			];
		}

		foreach (StringUtil::deserialize($this->chesstable_markierungen, true) as $eintrag)
		{
			if (!\is_array($eintrag) || empty($eintrag['intern']))
			{
				continue;
			}

			$schluessel = $eintrag['intern'];

			if (!isset($markierungen[$schluessel]))
			{
				$markierungen[$schluessel] = ['color' => '', 'rows' => [], 'flags' => []];
			}

			$markierungen[$schluessel]['rows'] = TableHelper::expandRanges($eintrag['rows'] ?? null);
			$markierungen[$schluessel]['flags'] = array_filter(array_map('trim', explode(',', (string) ($eintrag['flags'] ?? ''))));
		}

		return $markierungen;
	}

	/**
	 * Zerlegt die CSV-Daten in ein zweidimensionales Array.
	 *
	 * HTML-Entities werden vor dem Trennen aufgelöst, weil der ACE-Editor
	 * Sonderzeichen kodiert speichert: Ein Gleichheitszeichen käme sonst als
	 * "&#61;" an und ein Apostroph in einem Namen wie "L'Ami" würde die Zelle
	 * zerreißen.
	 *
	 * In der ersten Spalte wird die Kennung einer Sonderzeile von der Suche nach
	 * einer eigenen CSS-Klasse ausgenommen: "[TEXT]" steht in eckigen Klammern
	 * und würde sonst als Klassenangabe verstanden und aus der Zelle entfernt.
	 *
	 * @param string $csv Rohdaten aus dem Eingabefeld
	 *
	 * @return array{0: array<int, array<int, string>>, 1: array<int, array<int, string>>}
	 *         Die Zellinhalte und - in gleicher Anordnung - die je Zelle
	 *         angegebenen eigenen CSS-Klassen
	 */
	private function parseCsv(string $csv): array
	{
		$tabelle = [];
		$eigenklassen = [];

		foreach (preg_split('/\r\n|\n|\r/', $csv) ?: [] as $x => $zeile)
		{
			foreach (explode(';', html_entity_decode($zeile)) as $y => $zelle)
			{
				$zelle = trim($zelle);

				if ($y === 0 && ($zelle === '[TEXT]' || $zelle === '~'))
				{
					$tabelle[$x][$y] = $zelle;
					$eigenklassen[$x][$y] = '';
					continue;
				}

				[$tabelle[$x][$y], $eigenklassen[$x][$y]] = TableHelper::extractClass($zelle);
			}
		}

		return [$tabelle, $eigenklassen];
	}

	/**
	 * Stellt der Tabelle eine Spalte mit laufender Nummer voran.
	 *
	 * Kopfzeilen bekommen die Überschrift "Nr.", damit die Spalte als Platzspalte
	 * erkannt wird. Sonderzeilen (Trenner, Textzeilen, Leerzeilen) bleiben
	 * unverändert, weil sie über ihren Inhalt in Spalte 1 erkannt werden und
	 * durch eine zusätzliche Spalte unkenntlich würden. Gezählt werden nur
	 * echte Datenzeilen; bei mehreren durch "~" getrennten Tabellen beginnt die
	 * Zählung deshalb nicht neu, überspringt aber die Trenner.
	 *
	 * @param array<int, array<int, string>> $tabelle      Zellinhalte
	 * @param array<int, array<int, string>> $eigenklassen Eigene CSS-Klassen
	 *
	 * @return array{0: array<int, array<int, string>>, 1: array<int, array<int, string>>}
	 *         Tabelle und Klassen mit der zusätzlichen Spalte am Anfang
	 */
	private function addAutoNumbers(array $tabelle, array $eigenklassen): array
	{
		$neu = [];
		$neueKlassen = [];
		$nummer = 0;
		$kopfzeile = true;

		foreach ($tabelle as $x => $zeile)
		{
			if ($this->isSpecialRow($zeile))
			{
				$neu[$x] = $zeile;
				$neueKlassen[$x] = $eigenklassen[$x];

				// Nach einem Trenner folgt wieder eine Kopfzeile
				$kopfzeile = ($zeile[0] ?? '') === '~';
				continue;
			}

			$neu[$x] = array_merge([$kopfzeile ? 'Nr.' : (string) ++$nummer], $zeile);
			$neueKlassen[$x] = array_merge([''], $eigenklassen[$x]);
			$kopfzeile = false;
		}

		return [$neu, $neueKlassen];
	}

	/**
	 * Ermittelt aus der Kopfzeile die Art jeder Spalte.
	 *
	 * @param array<int, string>    $kopfzeile Die erste Zeile der Tabelle
	 * @param array<int, string[]>  $spalten   Konfigurierte Überschriften je Spaltenart
	 *
	 * @return array{0: array<int, int>, 1: int} Je Spaltennummer (ab 1) die
	 *         Spaltenart als Index in COLUMN_CLASSES sowie die Nummer der
	 *         Steuerspalte, oder 0, wenn es keine gibt
	 */
	private function detectColumnTypes(array $kopfzeile, array $spalten): array
	{
		$spaltenart = [];
		$steuerspalte = 0;

		foreach ($kopfzeile as $y => $titel)
		{
			$sp = $y + 1;
			$spaltenart[$sp] = 0;

			foreach ($spalten as $art => $begriffe)
			{
				if (\in_array(strtolower($titel), $begriffe, true))
				{
					$spaltenart[$sp] = $art;

					if (self::COLUMN_CLASSES[$art] === 'control')
					{
						$steuerspalte = $sp;
					}

					break;
				}
			}
		}

		return [$spaltenart, $steuerspalte];
	}

	/**
	 * Erzeugt das HTML der gesamten Tabelle.
	 *
	 * @param array<int, array<int, string>> $tabelle      Zellinhalte
	 * @param array<int, array<int, string>> $eigenklassen Eigene CSS-Klassen je Zelle
	 * @param array<int, int>                $spaltenart   Spaltenart je Spaltennummer
	 * @param int                            $steuerspalte Nummer der Steuerspalte, 0 wenn keine
	 * @param string[]                       $blindfelder  Zeichen, die ein Blindfeld kennzeichnen
	 * @param array<string, array{color: string, rows: int[], flags: string[]}> $markierungen Farbmarkierungen
	 * @param bool                           $flaggen      Ob in Nationsspalten Flaggen statt Text erscheinen
	 *
	 * @return string Das vollständige table-Element
	 */
	private function renderTable(array $tabelle, array $eigenklassen, array $spaltenart, int $steuerspalte, array $blindfelder, array $markierungen, bool $flaggen): string
	{
		$markierungFett = TableHelper::expandRanges((string) ($this->chesstable_markBold ?? ''));
		$markierungKursiv = TableHelper::expandRanges((string) ($this->chesstable_markItalic ?? ''));
		$namendrehen = (bool) $this->chesstable_namendrehen;
		$punkteFormat = (bool) $this->chesstable_punkteFormat;
		$vereinKuerzen = (bool) $this->chesstable_vereinKuerzen;

		$content = "<table class=\"chesstable\">\n";
		$spaltenzahl = 1;
		$kopfzeile = false;

		foreach ($tabelle as $x => $zeile)
		{
			$ze = $x + 1; // Zeilennummern zählen für den Redakteur ab 1

			if (($zeile[0] ?? '') === '~')
			{
				// Trennzeile: Abstand einfügen, die folgende Zeile wird Kopfzeile
				$content .= "<tr class=\"leerzeile\">\n  <td colspan=\"".$spaltenzahl."\"></td>\n</tr>\n";
				$kopfzeile = true;
				continue;
			}

			if (($zeile[0] ?? '') === '[TEXT]')
			{
				$content .= "<tr class=\"textzeile\">\n  <td colspan=\"".$spaltenzahl."\">".($zeile[1] ?? '')."</td>\n</tr>\n";
				continue;
			}

			if ($this->isEmptyRow($zeile))
			{
				$content .= "<tr class=\"leerzeile\">\n";
				$content .= str_repeat("  <td style=\"text-align:center;\">...</td>\n", $spaltenzahl);
				$content .= "</tr>\n";
				continue;
			}

			$td = ($ze === 1 || $kopfzeile) ? 'th' : 'td';
			$trstyle = '';
			$trcss = 'row'.$ze;

			// Steuerspalte mit dem Wert "team" kennzeichnet eine Mannschaftszeile
			if ($steuerspalte > 0 && ($zeile[$steuerspalte - 1] ?? '') === 'team')
			{
				$trcss .= ' team';
			}

			// Zeilenmarkierungen auswerten; Auf- und Absteiger sowie hervorgehobene
			// Zeilen bekommen zusätzlich eine eigene CSS-Klasse
			foreach ($markierungen as $schluessel => $markierung)
			{
				if (!\in_array($ze, $markierung['rows'], true))
				{
					continue;
				}

				// Eine Markierung ohne Farbe darf eine zuvor gesetzte nicht löschen
				if ($markierung['color'] !== '')
				{
					$trstyle = 'background-color:'.$markierung['color'].';';
				}

				if (\in_array($schluessel, ['up', 'down', 'high'], true))
				{
					$trcss .= ' '.$schluessel;
				}
			}

			$strZeile = '';

			foreach ($zeile as $y => $wert)
			{
				$sp = $y + 1;
				$ownclass = $eigenklassen[$x][$y] ?? '';
				$klasse = self::COLUMN_CLASSES[$spaltenart[$sp] ?? 0] ?? 'text';

				// Die Platzspalte wird nur hervorgehoben, wenn sie vorn steht
				if ($klasse === 'place' && $sp > 1)
				{
					$klasse = '';
				}

				if ($namendrehen && $klasse === 'name' && $td === 'td')
				{
					$wert = TableHelper::rotateName($wert);
				}

				if ($punkteFormat && $klasse === 'points' && $td === 'td')
				{
					$wert = TableHelper::formatPoints($wert);
				}

				if ($vereinKuerzen && $klasse === 'club' && $td === 'td')
				{
					$wert = TableHelper::shortenClubName($wert);
				}

				if (\in_array(strtolower($wert), $blindfelder, true))
				{
					$strZeile .= $this->renderCell($td, ['row'.$ze, 'col'.$sp, 'blindfield', $klasse, $ownclass], $wert);
					continue;
				}

				if ($klasse === 'control')
				{
					// Die Steuerspalte dient nur der Formatierung und wird nicht ausgegeben
					continue;
				}

				if ($td === 'td' && $klasse === 'nation')
				{
					$strZeile .= $this->renderNationCell($ze, $sp, $wert, $ownclass, $flaggen);

					// Eine Ländermarkierung überschreibt die Zeilenmarkierung
					foreach ($markierungen as $markierung)
					{
						if ($markierung['color'] !== '' && \in_array($wert, $markierung['flags'], true))
						{
							$trstyle = 'background-color:'.$markierung['color'].';';
						}
					}

					continue;
				}

				if ($klasse === 'color')
				{
					// Die Farbspalte zeigt die Brettfarbe allein über die CSS-Klasse
					if ($td === 'td')
					{
						$klasse .= match (strtolower($wert)) {
							'w' => '_w',
							'b' => '_b',
							default => '',
						};
					}

					$strZeile .= $this->renderCell($td, ['row'.$ze, $klasse, $ownclass], '&nbsp;', $wert);
					continue;
				}

				// In Ergebnisspalten die Brettfarbe aus dem Wert ableiten
				$boardcolor = '';

				if ($klasse === 'result')
				{
					if (stristr($wert, 'w') !== false)
					{
						$boardcolor = 'white';
					}
					elseif (stristr($wert, 'b') !== false || stristr($wert, 's') !== false)
					{
						$boardcolor = 'black';
					}
				}

				$strZeile .= $this->renderCell($td, ['row'.$ze, 'col'.$sp, $klasse, $ownclass, $boardcolor], $wert);
			}

			if (\in_array($ze, $markierungFett, true))
			{
				$trstyle .= ' font-weight:bold;';
			}

			if (\in_array($ze, $markierungKursiv, true))
			{
				$trstyle .= ' font-style:italic;';
			}

			$content .= '<tr class="'.$trcss.'"'.($trstyle !== '' ? ' style="'.trim($trstyle).'"' : '').">\n";
			$content .= $strZeile;
			$content .= "</tr>\n";

			$kopfzeile = false;
			$spaltenzahl = max(1, \count($zeile)); // Für den colspan der nächsten Sonderzeile merken
		}

		return $content."</table>\n";
	}

	/**
	 * Erzeugt eine einzelne Tabellenzelle.
	 *
	 * @param string   $td      Elementname, "td" oder "th"
	 * @param string[] $klassen CSS-Klassen; leere Einträge werden verworfen
	 * @param string   $inhalt  Bereits fertiger Zellinhalt (darf HTML enthalten,
	 *                          weil im Eingabefeld HTML erlaubt ist)
	 * @param string   $title   Optionaler Text für das title-Attribut; er wird
	 *                          maskiert, weil er aus den CSV-Daten stammt
	 *
	 * @return string Die Zelle inklusive Zeilenumbruch
	 */
	private function renderCell(string $td, array $klassen, string $inhalt, string $title = ''): string
	{
		$attribute = '';

		if ($title !== '')
		{
			$attribute .= ' title="'.StringUtil::specialchars($title).'"';
		}

		$attribute .= ' class="'.implode(' ', array_filter($klassen)).'"';

		return '<'.$td.$attribute.'>'.$inhalt.'</'.$td.">\n";
	}

	/**
	 * Erzeugt die Zelle einer Nationsspalte.
	 *
	 * Ist die Flaggenanzeige aktiv, wird das Länderkürzel durch ein Flaggensymbol
	 * ersetzt. Für Kürzel ohne bekannte Flagge bleibt der Text stehen, damit die
	 * Information nicht verlorengeht. Ohne Flaggenanzeige bekommt die Zelle das
	 * kleingeschriebene Kürzel als zusätzliche CSS-Klasse, damit eigene
	 * Stylesheets einzelne Länder ansprechen können.
	 *
	 * @param int    $ze        Zeilennummer ab 1
	 * @param int    $sp        Spaltennummer ab 1
	 * @param string $wert      Länderkürzel aus den CSV-Daten
	 * @param string $ownclass  Vom Redakteur vergebene eigene CSS-Klasse
	 * @param bool   $flaggen   Ob Flaggen statt Text ausgegeben werden sollen
	 *
	 * @return string Die fertige Zelle
	 */
	private function renderNationCell(int $ze, int $sp, string $wert, string $ownclass, bool $flaggen): string
	{
		if ($flaggen)
		{
			$flagge = CountryCodes::getFlagClass($wert);
			$inhalt = $flagge !== ''
				? '<span class="'.$flagge.'"></span>'
				: '<span class="ioc_code">'.StringUtil::specialchars($wert).'</span>';

			return $this->renderCell('td', ['row'.$ze, 'col'.$sp, 'nation', $ownclass], $inhalt, $wert);
		}

		// Nur Buchstaben und Ziffern als Klasse zulassen, der Wert kommt vom Redakteur
		$landklasse = strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', $wert));

		return $this->renderCell('td', ['row'.$ze, 'col'.$sp, 'nation', $ownclass, $landklasse], $wert, $wert);
	}

	/**
	 * Prüft, ob eine Zeile eine Sonderzeile ist.
	 *
	 * Sonderzeilen sind der Tabellentrenner "~", die Textzeile "[TEXT]" und die
	 * Leerzeile. Sie bestehen nicht aus regulären Spalten und dürfen deshalb
	 * weder nummeriert noch nach Spaltenart formatiert werden.
	 *
	 * @param array<int, string> $zeile Die Zellinhalte der Zeile
	 *
	 * @return bool True, wenn die Zeile gesondert behandelt werden muss
	 */
	private function isSpecialRow(array $zeile): bool
	{
		$erste = $zeile[0] ?? '';

		return $erste === '~' || $erste === '[TEXT]' || $this->isEmptyRow($zeile);
	}

	/**
	 * Prüft, ob eine Zeile leer ist.
	 *
	 * Eine leere Zeile in den CSV-Daten erzeugt im Frontend eine Zeile mit
	 * Auslassungspunkten. Erkannt wird sie daran, dass sie nach dem Trennen aus
	 * genau einer leeren Zelle besteht.
	 *
	 * @param array<int, string> $zeile Die Zellinhalte der Zeile
	 *
	 * @return bool True, wenn die Zeile keine Daten enthält
	 */
	private function isEmptyRow(array $zeile): bool
	{
		return \count($zeile) === 1 && ($zeile[0] ?? '') === '';
	}

	/**
	 * Ermittelt den öffentlichen Pfad zum Stylesheet der Flaggenbibliothek.
	 *
	 * Das Paket components/flag-icon-css wird von Composer nach vendor/ gelegt
	 * und ist von dort aus nicht über den Webserver erreichbar. Damit der
	 * Anwender nichts von Hand einrichten muss, verlinkt diese Methode das
	 * Verzeichnis beim ersten Aufruf in das öffentliche Verzeichnis. Schlägt das
	 * fehl - etwa weil der Hoster keine Symlinks erlaubt -, liefert die Methode
	 * einen leeren String; die Tabelle zeigt dann die Länderkürzel als Text.
	 *
	 * In Contao 4.13 kann das öffentliche Verzeichnis noch "web" heißen, deshalb
	 * wird der Parameter contao.web_dir abgefragt, den es in Contao 5 nicht mehr gibt.
	 *
	 * @return string Der Pfad für $GLOBALS['TL_CSS'], oder ein leerer String,
	 *                wenn die Bibliothek nicht eingebunden werden kann
	 */
	private function getFlagIconCss(): string
	{
		if (self::$strFlagIconCss !== null)
		{
			return self::$strFlagIconCss;
		}

		self::$strFlagIconCss = '';

		$container = System::getContainer();
		$projectDir = $container->getParameter('kernel.project_dir');
		$webDir = $container->hasParameter('contao.web_dir') ? $container->getParameter('contao.web_dir') : $projectDir.'/public';

		$quelle = $projectDir.'/vendor/components/flag-icon-css';
		$ziel = $webDir.'/bundles/flag-icon-css';

		if (!is_dir($ziel))
		{
			if (!is_dir($quelle) || !is_dir(\dirname($ziel)))
			{
				return '';
			}

			// Ein zuvor angelegter, inzwischen ins Leere zeigender Symlink würde
			// jeden weiteren Versuch blockieren
			if (is_link($ziel))
			{
				@unlink($ziel);
			}

			if (!@symlink($quelle, $ziel))
			{
				return '';
			}
		}

		return self::$strFlagIconCss = 'bundles/flag-icon-css/css/flag-icon.min.css|static';
	}
}

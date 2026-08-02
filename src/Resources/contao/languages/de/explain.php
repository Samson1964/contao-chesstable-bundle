<?php

declare(strict_types=1);

/**
 * Texte für das Hilfe-Popup des CSV-Eingabefelds.
 *
 * Die Aufzählung der Spaltenüberschriften wird aus den Systemeinstellungen
 * gelesen, damit die Hilfe zu der Konfiguration passt, die auf dieser
 * Installation tatsächlich gilt.
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

use Contao\Config;

/**
 * Liest eine Spaltenart-Einstellung als lesbare Aufzählung.
 *
 * Bewusst eine Closure statt einer benannten Funktion: Contao bindet
 * Sprachdateien innerhalb eines Aufrufs mehrfach ein, eine benannte Funktion
 * würde dabei beim zweiten Mal einen Fehler wegen doppelter Deklaration werfen.
 *
 * @param string $strKey Name der Einstellung, z. B. "chesstable_namenfelder"
 *
 * @return string Die Begriffe mit Komma und Leerzeichen getrennt; ein leerer
 *                String, wenn die Einstellung nicht gepflegt ist
 */
$chesstableExplainList = static function (string $strKey): string
{
	return str_replace(',', ', ', (string) (Config::get($strKey) ?? ''));
};

$GLOBALS['TL_LANG']['XPL']['chesstable_csv'] = array
(
	array('colspan', 'Geben Sie hier die Daten für Ihre Tabelle ein. Zeilen müssen durch einen Zeilenumbruch, Spalten durch ein Semikolon getrennt sein. Alle Zeilen müssen zudem die gleiche Spaltenanzahl haben!<br><br>Um innerhalb Ihrer Tabelle eine Trennung zu erreichen, schreiben Sie in eine Zeile nur ein Tilde-Zeichen (~). Die nachfolgende Zeile wird dann als HTML-Kopfzeile interpretiert.<br><br>Eine leere Zeile erzeugt eine Zeile mit Auslassungspunkten. Eine Zeile aus [TEXT] und einer zweiten Spalte gibt diese zweite Spalte über die gesamte Tabellenbreite aus.<br><br>Die erste Zeile wird <b>immer</b> als HTML-Kopfzeile interpretiert, wobei die Spaltenwerte wichtig für die Zuordnung der Spaltenart sind. Folgende Spaltenarten gibt es:'),
	array('Spaltenköpfe für Nationsfelder', $chesstableExplainList('chesstable_nationfelder')),
	array('Spaltenköpfe für Platzfelder', $chesstableExplainList('chesstable_platzfelder')),
	array('Spaltenköpfe für Vereinsfelder', $chesstableExplainList('chesstable_vereinfelder')),
	array('Spaltenköpfe für Namensfelder', $chesstableExplainList('chesstable_namenfelder')),
	array('Spaltenköpfe für Punktefelder', $chesstableExplainList('chesstable_punktefelder')),
	array('Spaltenköpfe für Wertungsfelder', $chesstableExplainList('chesstable_wertungfelder')),
	array('Spaltenköpfe für Ratingfelder', $chesstableExplainList('chesstable_ratingfelder')),
	array('Spaltenköpfe für Ergebnisfelder', $chesstableExplainList('chesstable_ergebnisfelder')),
	array('Spaltenköpfe für Farbfelder', $chesstableExplainList('chesstable_farbfelder')),
	array('Spaltenköpfe für Steuerfelder', $chesstableExplainList('chesstable_steuerfelder')),
	array('colspan', 'Sie können darüberhinaus jeder Zelle eine weitere CSS-Klasse zuordnen:'),
	array('[Klassenname]', 'Beispiel: 3;[sieger]Meier;SV Landshut<br>Die zweite Spalte bekommt die CSS-Klasse "own_sieger". Sie können die Klasse auch hinter dem Wort "Meier" einfügen.'),
);

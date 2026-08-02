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
	array('colspan', 'Enter the data for your table here. Rows must be separated by a line break, columns by a semicolon. All rows must have the same number of columns!<br><br>To separate two tables, put a single tilde (~) on a line of its own. The following row is then rendered as a header row.<br><br>An empty row produces a row of ellipses. A row consisting of [TEXT] and a second column outputs that second column across the full width of the table.<br><br>The first row is <b>always</b> rendered as a header row; its values determine the column types. The following column types exist:'),
	array('Column headings for nation fields', $chesstableExplainList('chesstable_nationfelder')),
	array('Column headings for rank fields', $chesstableExplainList('chesstable_platzfelder')),
	array('Column headings for club fields', $chesstableExplainList('chesstable_vereinfelder')),
	array('Column headings for name fields', $chesstableExplainList('chesstable_namenfelder')),
	array('Column headings for point fields', $chesstableExplainList('chesstable_punktefelder')),
	array('Column headings for tie-break fields', $chesstableExplainList('chesstable_wertungfelder')),
	array('Column headings for rating fields', $chesstableExplainList('chesstable_ratingfelder')),
	array('Column headings for result fields', $chesstableExplainList('chesstable_ergebnisfelder')),
	array('Column headings for colour fields', $chesstableExplainList('chesstable_farbfelder')),
	array('Column headings for control fields', $chesstableExplainList('chesstable_steuerfelder')),
	array('colspan', 'In addition you can assign an extra CSS class to every cell:'),
	array('[class name]', 'Example: 3;[winner]Meier;SV Landshut<br>The second column gets the CSS class "own_winner". You can also put the class behind the word "Meier".'),
);

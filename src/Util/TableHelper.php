<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\Util;

/**
 * Sammlung der reinen Umformungen, die beim Aufbau einer Schachtabelle nötig sind.
 *
 * Die Methoden sind bewusst frei von Contao-Abhängigkeiten, damit sie ohne
 * Framework getestet werden können. Sie arbeiten ausschließlich auf den Daten,
 * die der Redakteur im Inhaltselement eingegeben hat.
 */
final class TableHelper
{
	/**
	 * Löst eine Aufzählung von Zeilennummern mit Bereichen in einzelne Nummern auf.
	 *
	 * Redakteure geben die zu markierenden Zeilen verkürzt an, etwa "1,3-7,12".
	 * Für den Vergleich beim Tabellenaufbau wird daraus eine flache Liste aller
	 * gemeinten Zeilennummern. Leerzeichen um die Werte werden ignoriert, damit
	 * auch "1, 3 - 7" funktioniert.
	 *
	 * @param string|null $angabe Kommaliste aus Einzelnummern und Bereichen;
	 *                            null oder ein leerer String sind zulässig und
	 *                            stehen für "keine Markierung"
	 *
	 * @return int[] Aufsteigend gemeinte Einzelnummern in der Reihenfolge der
	 *               Eingabe. Unverständliche Angaben werden stillschweigend
	 *               übergangen, ein Bereich mit vertauschten Grenzen ("7-3")
	 *               liefert nichts. Sehr große Bereiche werden bei 1000 Werten
	 *               abgeschnitten, damit ein Vertipper wie "1-999999" nicht den
	 *               Speicher füllt.
	 */
	public static function expandRanges(?string $angabe): array
	{
		if ($angabe === null || trim($angabe) === '')
		{
			return [];
		}

		$nummern = [];

		foreach (explode(',', $angabe) as $teil)
		{
			$teil = trim($teil);

			if (ctype_digit($teil))
			{
				$nummern[] = (int) $teil;
				continue;
			}

			// Bereich in der Form "Zahl-Zahl" auflösen
			$grenzen = array_map('trim', explode('-', $teil));

			if (\count($grenzen) !== 2 || !ctype_digit($grenzen[0]) || !ctype_digit($grenzen[1]))
			{
				continue;
			}

			$von = (int) $grenzen[0];
			$bis = min((int) $grenzen[1], $von + 999);

			for ($nummer = $von; $nummer <= $bis; $nummer++)
			{
				$nummern[] = $nummer;
			}
		}

		return $nummern;
	}

	/**
	 * Dreht einen mit Komma getrennten Namen in die Leserichtung um.
	 *
	 * Turniersoftware liefert Namen meist als "Nachname,Vorname,Titel". Für die
	 * Ausgabe im Frontend wird daraus "Titel Vorname Nachname", also die
	 * umgekehrte Reihenfolge der Bestandteile.
	 *
	 * @param string $name Name mit Komma als Trennzeichen; ein Name ohne Komma
	 *                     bleibt unverändert
	 *
	 * @return string Der gedrehte Name ohne führende oder folgende Leerzeichen
	 */
	public static function rotateName(string $name): string
	{
		$teile = array_map('trim', explode(',', $name));

		return trim(implode(' ', array_reverse($teile)));
	}

	/**
	 * Trennt eine in eckigen Klammern angegebene CSS-Klasse vom Zellinhalt.
	 *
	 * Der Redakteur kann einer Zelle eine eigene Klasse mitgeben, indem er sie
	 * in eckige Klammern setzt: "[sieger]Meier" wird zu dem Inhalt "Meier" mit
	 * der Klasse "own_sieger". Die Klammern dürfen an beliebiger Stelle in der
	 * Zelle stehen.
	 *
	 * Weil der Klassenname unverändert in ein class-Attribut geschrieben wird,
	 * werden alle Zeichen außer Buchstaben, Ziffern, Bindestrich und Unterstrich
	 * verworfen - sonst könnte man über die CSV-Daten HTML-Attribute einschleusen.
	 *
	 * @param string $zelle Roher Zellinhalt aus den CSV-Daten
	 *
	 * @return array{0: string, 1: string} Der Zellinhalt ohne die Klammerangabe
	 *                                     und die CSS-Klasse mit Präfix "own_".
	 *                                     Fehlt eine der Klammern oder ist die
	 *                                     Angabe leer, bleibt die Klasse leer und
	 *                                     der Inhalt unverändert.
	 */
	public static function extractClass(string $zelle): array
	{
		$auf = strpos($zelle, '[');

		if ($auf === false)
		{
			return [$zelle, ''];
		}

		$zu = strpos($zelle, ']', $auf);

		if ($zu === false)
		{
			return [$zelle, ''];
		}

		$klasse = preg_replace('/[^A-Za-z0-9_-]/', '', substr($zelle, $auf + 1, $zu - $auf - 1));
		$inhalt = substr($zelle, 0, $auf).substr($zelle, $zu + 1);

		if ($klasse === '')
		{
			return [$inhalt, ''];
		}

		return [$inhalt, 'own_'.$klasse];
	}

	/**
	 * Formatiert einen Punktestand mit einer Nachkommastelle.
	 *
	 * Turniersoftware gibt halbe Punkte oft mit dem Zeichen "½" statt einer
	 * Dezimalzahl aus. Diese Methode bringt beide Schreibweisen auf eine
	 * einheitliche Form mit Komma als Dezimaltrennzeichen, wie sie im
	 * deutschsprachigen Raum üblich ist.
	 *
	 * @param string $wert Punktestand aus den CSV-Daten, z. B. "4", "4½" oder "½"
	 *
	 * @return string Der Punktestand mit einer Nachkommastelle, z. B. "4,0" oder
	 *                "4,5". Werte, die weder eine reine Zahl noch eine Zahl mit
	 *                angehängtem "½" sind - etwa bereits formatierte Werte wie
	 *                "4,5" - bleiben unverändert, damit vorhandene Angaben nicht
	 *                verfälscht werden.
	 */
	public static function formatPoints(string $wert): string
	{
		$getrimmt = trim($wert);

		if (preg_match('/^(\d+)½$/u', $getrimmt, $treffer))
		{
			return $treffer[1].',5';
		}

		if ($getrimmt === '½')
		{
			return '0,5';
		}

		if (ctype_digit($getrimmt))
		{
			return $getrimmt.',0';
		}

		return $wert;
	}

	/**
	 * Bekannte Zusätze, die shortenClubName() aus einem Vereinsnamen entfernt.
	 *
	 * Jeder Eintrag ist ein Regex-Fragment ohne Begrenzungszeichen.
	 */
	private const CLUB_SUFFIXES = ['e\.\s*v\.', 'eingetragener\s+verein'];

	/**
	 * Entfernt gebräuchliche Rechtsform-Zusätze vom Ende eines Vereinsnamens.
	 *
	 * Turniermeldelisten führen Vereine häufig mit dem Zusatz "e.V." (in allen
	 * Schreibvarianten und wahlweise in Klammern), was in einer knapp bemessenen
	 * Tabellenspalte unnötig Platz kostet.
	 *
	 * @param string $wert Vereinsname aus den CSV-Daten
	 *
	 * @return string Der Vereinsname ohne den Zusatz und ohne die dadurch
	 *                entstehenden Leerzeichen am Ende. Kommt keiner der bekannten
	 *                Zusätze vor, bleibt der Name unverändert.
	 */
	public static function shortenClubName(string $wert): string
	{
		$muster = '/\s*\(?\s*(?:'.implode('|', self::CLUB_SUFFIXES).')\s*\)?\.?\s*$/iu';

		return trim(preg_replace($muster, '', $wert));
	}
}

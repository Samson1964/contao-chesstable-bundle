<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\Widgets;

use Contao\Config;
use Contao\StringUtil;
use Contao\Widget;

/**
 * Backend-Widget zur Eingabe der farbig zu markierenden Zeilen und Länder.
 *
 * Welche Markierungen es gibt (Aufsteiger, Absteiger, ...) und welche Farbe sie
 * haben, legt der Administrator einmalig in den Systemeinstellungen fest. Im
 * Inhaltselement trägt der Redakteur dann nur noch ein, welche Zeilennummern und
 * welche Länderkürzel zu welcher Markierung gehören. Das Widget zeigt deshalb
 * eine feste Zeile je konfigurierter Markierung statt einer frei erweiterbaren
 * Liste.
 *
 * Markierungen, die im Datensatz gespeichert sind, in den Einstellungen aber
 * nicht mehr vorkommen, werden rot und ohne Farbfeld angezeigt. So sieht der
 * Redakteur, dass die Angabe wirkungslos ist, ohne dass die Daten verlorengehen.
 */
class ChesstableColors extends Widget
{
	/**
	 * Der Wert wird beim Speichern des Formulars übernommen.
	 *
	 * @var bool
	 */
	protected $blnSubmitInput = true;

	/**
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Wandelt die eingegebenen Zeilen vor dem Speichern in einen String um.
	 *
	 * Das Feld nimmt die Daten als Array aus dem Formular entgegen, die
	 * Datenbankspalte ist aber ein varchar. Deshalb wird hier serialisiert,
	 * bevor die Prüfung der Elternklasse greift.
	 *
	 * @param mixed $varInput Das Array der Formularzeilen
	 *
	 * @return mixed Der serialisierte Wert, wie ihn die Elternklasse liefert
	 */
	protected function validator($varInput)
	{
		return parent::validator(serialize((array) $varInput));
	}

	/**
	 * Erzeugt das Eingabefeld.
	 *
	 * @return string Das HTML des Widgets; bei nicht gepflegten Einstellungen
	 *                nur die Kopfzeile ohne Eingabefelder
	 */
	public function generate(): string
	{
		$zeilen = $this->collectRows();

		$content = '<div>';
		$content .= '<span style="display:inline-block; width:15%; font-style:italic;">Name</span>';
		$content .= '<span style="display:inline-block; width:15%; font-style:italic; margin-right:10px;">Farbe</span>';
		$content .= '<span style="display:inline-block; width:32%; font-style:italic; margin-right:5px;">Zeilennummern</span>';
		$content .= '<span style="display:inline-block; width:32%; font-style:italic;">Länder</span>';
		$content .= '</div>';

		$index = 0;

		foreach ($zeilen as $schluessel => $zeile)
		{
			$id = $this->strName.'_'.$index;
			$namensfarbe = $zeile['defined'] ? '' : ' color:red;';

			$content .= '<div>';
			$content .= '<input type="hidden" name="'.$this->strName.'['.$index.'][intern]" value="'.StringUtil::specialchars($schluessel).'">';
			$content .= '<span style="display:inline-block; width:15%; font-weight:bold;'.$namensfarbe.'">'.StringUtil::specialchars($zeile['name']).'</span>';
			$content .= '<span style="display:inline-block; width:15%; margin-right:10px; background-color:'.StringUtil::specialchars($zeile['color']).'">&nbsp;</span>';
			$content .= '<input type="text" name="'.$this->strName.'['.$index.'][rows]" id="ctrl_'.$id.'_rows" class="tl_text" style="width:32%; margin-right:5px;" value="'.StringUtil::specialchars($zeile['rows']).'" onfocus="Backend.getScrollOffset()">';
			$content .= '<input type="text" name="'.$this->strName.'['.$index.'][flags]" id="ctrl_'.$id.'_flags" class="tl_text" style="width:32%" value="'.StringUtil::specialchars($zeile['flags']).'" onfocus="Backend.getScrollOffset()">';
			$content .= '</div>';

			$index++;
		}

		return $content;
	}

	/**
	 * Führt die Vorgaben aus den Einstellungen mit den Daten des Datensatzes zusammen.
	 *
	 * @return array<string, array{name: string, color: string, rows: string, flags: string, defined: bool}>
	 *         Je interner Kennung die Anzeigedaten einer Widget-Zeile. "defined"
	 *         ist false, wenn die Markierung nur noch im Datensatz steht, in den
	 *         Systemeinstellungen aber nicht mehr definiert ist.
	 */
	private function collectRows(): array
	{
		$zeilen = [];

		foreach (StringUtil::deserialize(Config::get('chesstable_markColors'), true) as $eintrag)
		{
			if (!\is_array($eintrag) || empty($eintrag['intern']))
			{
				continue;
			}

			$zeilen[$eintrag['intern']] = [
				'name' => (string) ($eintrag['name'] ?? $eintrag['intern']),
				'color' => !empty($eintrag['color']) ? '#'.$eintrag['color'] : '',
				'rows' => '',
				'flags' => '',
				'defined' => true,
			];
		}

		// Der gespeicherte Wert ist beim erneuten Anzeigen nach einem Fehler
		// bereits ein Array, aus der Datenbank kommt er dagegen serialisiert
		foreach (StringUtil::deserialize($this->varValue, true) as $eintrag)
		{
			if (!\is_array($eintrag) || empty($eintrag['intern']))
			{
				continue;
			}

			$schluessel = $eintrag['intern'];

			if (!isset($zeilen[$schluessel]))
			{
				$zeilen[$schluessel] = [
					'name' => (string) $schluessel,
					'color' => '',
					'rows' => '',
					'flags' => '',
					'defined' => false,
				];
			}

			$zeilen[$schluessel]['rows'] = (string) ($eintrag['rows'] ?? '');
			$zeilen[$schluessel]['flags'] = (string) ($eintrag['flags'] ?? '');
		}

		return $zeilen;
	}
}

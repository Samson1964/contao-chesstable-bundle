<?php

declare(strict_types=1);

/**
 * Erweiterung der Tabelle tl_content um das Inhaltselement "Schachtabelle".
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

/*
 * Paletten
 *
 * Das Feld "guests" gibt es nur bis Contao 4.13; in Contao 5 wurde es zugunsten
 * der Mitgliedergruppen entfernt. Statt die Contao-Version abzufragen, wird
 * geprüft, ob der Kern das Feld überhaupt kennt - die DCA des Kerns ist an
 * dieser Stelle bereits geladen.
 */
$strGuests = isset($GLOBALS['TL_DCA']['tl_content']['fields']['guests']) ? 'guests,' : '';

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'chesstable_lightbox';
$GLOBALS['TL_DCA']['tl_content']['palettes']['chesstable'] = '{type_legend},type,headline;{chesstable_legend_csv},chesstable_csv,chesstable_autoNumber;{chesstable_legend_aufab},chesstable_markierungen,chesstable_markBold,chesstable_markItalic;{chesstable_legend_lightbox},chesstable_lightbox;{chesstable_legend_optionen},chesstable_namendrehen,chesstable_flaggen,chesstable_date,chesstable_ende,chesstable_note;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},'.$strGuests.'cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['chesstable_lightbox'] = 'chesstable_linktext,chesstable_hinweis';

/*
 * Felder
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_csv'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_csv'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'textarea',
	'eval'                    => array
	(
		'allowHtml'           => true,
		'class'               => 'monospace',
		'rows'                => 30,
		'rte'                 => 'ace',
		'helpwizard'          => true
	),
	'explanation'             => 'chesstable_csv',
	'sql'                     => "mediumtext NULL",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_autoNumber'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_autoNumber'],
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'isBoolean'           => true,
	),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_markBold'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_markBold'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'long',
	),
	'sql'                     => "blob NULL",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_markItalic'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_markItalic'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'long',
	),
	'sql'                     => "blob NULL",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_markierungen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_markierungen'],
	'inputType'               => 'chesstableColors',
	'eval'                    => array
	(
		'tl_class'            => 'long',
		'helpwizard'          => false,
	),
	'sql'                     => "varchar(1024) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_namendrehen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_namendrehen'],
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'isBoolean'           => true,
	),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_flaggen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_flaggen'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50', 'isBoolean' => true),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_date'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_date'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50', 'isBoolean' => true),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_ende'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_ende'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'date',
		'datepicker'          => true,
		'tl_class'            => 'w50 wizard',
		'doNotCopy'           => true
	),
	'sql'                     => "varchar(11) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_note'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_note'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'tl_class'            => 'w50 long',
		'maxlength'           => 255,
	),
	'sql'                     => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_lightbox'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_lightbox'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class' => 'w50 clr', 'isBoolean' => true, 'submitOnChange' => true),
	'sql'                     => "char(1) NOT NULL default ''",
);

// Reines Hinweisfeld ohne Datenbankspalte, siehe tl_content_chesstable::jshinweis()
$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_hinweis'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_hinweis'],
	'eval'                    => array('tl_class' => 'long clr'),
	'input_field_callback'    => array('tl_content_chesstable', 'jshinweis'),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['chesstable_linktext'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['chesstable_linktext'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class' => 'w50 clr', 'maxlength' => 32),
	'sql'                     => "varchar(32) NOT NULL default ''",
);

/**
 * Callbacks des Inhaltselements "Schachtabelle".
 */
class tl_content_chesstable extends Contao\Backend
{
	/**
	 * Zeigt in der Lightbox-Palette den Hinweis auf das benötigte Template an.
	 *
	 * Die Lightbox beruht auf Colorbox. Contao lädt die dafür nötigen Skripte
	 * nur, wenn im Seitenlayout das Template j_colorbox eingebunden ist. Da sich
	 * das nicht automatisch nachrüsten lässt, bekommt der Redakteur an dieser
	 * Stelle einen Hinweis statt eines Eingabefelds.
	 *
	 * @param Contao\DataContainer $dc Der Data Container des Datensatzes; wird
	 *                                 nicht ausgewertet, weil der Hinweis für
	 *                                 alle Datensätze gleich ist
	 *
	 * @return string Der Hinweiskasten als HTML
	 */
	public function jshinweis(Contao\DataContainer $dc): string
	{
		$strHinweis = $GLOBALS['TL_LANG']['tl_content']['chesstable_colorbox'] ?? 'Damit die Lightbox funktioniert, muss im Seitenlayout das Template %s eingebunden sein.';

		return '<div class="tl_message clr">
			<p class="tl_info">'.sprintf($strHinweis, 'j_colorbox').'</p>
			</div>';
	}
}

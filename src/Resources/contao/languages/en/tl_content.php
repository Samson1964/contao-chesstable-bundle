<?php

declare(strict_types=1);

/**
 * Englische Beschriftungen des Inhaltselements "Schachtabelle".
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

$GLOBALS['TL_LANG']['tl_content']['chesstable_csv'] = array('CSV data', 'Enter the table data here. The columns must be separated with a semicolon.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_autoNumber'] = array('Automatic numbering', 'A column "Nr." with automatic numbering is added as the first column.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_markBold'] = array('Mark rows bold', 'Enter the numbers of the rows to be printed in bold, separated by comma and hyphen. Example: 1,3-5,11.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_markItalic'] = array('Mark rows italic', 'Enter the numbers of the rows to be printed in italics, separated by comma and hyphen. Example: 1,3-5,11.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_markierungen'] = array('Mark rows with colour', 'Enter the numbers of the rows to be marked in the first input field, separated by comma and hyphen. Example: 1,3-5,11. In the second input field you can enter the country codes whose rows are to be marked. Country colours override row colours!');
$GLOBALS['TL_LANG']['tl_content']['chesstable_namendrehen'] = array('Rotate player name', 'The content of the player column is split at the comma and reversed.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_punkteFormat'] = array('Format points with one decimal', 'Scores are shown with a consistent one decimal place, e.g. "4" becomes "4,0" and "4½" becomes "4,5".');
$GLOBALS['TL_LANG']['tl_content']['chesstable_vereinKuerzen'] = array('Shorten club names', 'Legal-form suffixes such as "e.V." are removed from club names.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_flaggen'] = array('Show nation flags', 'Show nation flags if a country column exists.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_date'] = array('Show update date', 'Show the date of the last update of this table or content element.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_ende'] = array('End of tournament', 'Shows the end date of the tournament if set.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_note'] = array('Note', 'Note which is shown below the table.');
$GLOBALS['TL_LANG']['tl_content']['chesstable_lightbox'] = array('Lightbox view', 'Show the table in a lightbox');
$GLOBALS['TL_LANG']['tl_content']['chesstable_linktext'] = array('Text for the lightbox link', 'Text for the lightbox link');
$GLOBALS['TL_LANG']['tl_content']['chesstable_hinweis'] = array('Requirement', '');

$GLOBALS['TL_LANG']['tl_content']['chesstable_colorbox'] = 'The lightbox requires the template %s to be included in the page layout.';

$GLOBALS['TL_LANG']['tl_content']['chesstable_legend_csv'] = 'CSV data';
$GLOBALS['TL_LANG']['tl_content']['chesstable_legend_aufab'] = 'Marking of rows';
$GLOBALS['TL_LANG']['tl_content']['chesstable_legend_lightbox'] = 'Table in a lightbox';
$GLOBALS['TL_LANG']['tl_content']['chesstable_legend_optionen'] = 'More options';

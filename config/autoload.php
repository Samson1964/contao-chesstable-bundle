<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'chesstable'                => 'system/modules/chesstable/chesstable.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_chesstable'          => 'system/modules/chesstable/templates',
	'ce_chesstable_lightbox' => 'system/modules/chesstable/templates',
));

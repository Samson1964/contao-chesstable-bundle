<?php

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
 * palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{chesstable_legend:hide},chesstable_blindfelder,chesstable_nationfelder,chesstable_platzfelder,chesstable_vereinfelder,chesstable_namenfelder,chesstable_punktefelder,chesstable_wertungfelder,chesstable_ratingfelder,chesstable_ergebnisfelder,chesstable_farbfelder,chesstable_steuerfelder,chesstable_markColors,chesstable_css';

/**
 * fields
 */

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_blindfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_blindfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_nationfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_nationfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_platzfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_platzfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_vereinfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_vereinfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_namenfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_namenfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_punktefelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_punktefelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_wertungfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_wertungfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_ratingfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_ratingfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_ergebnisfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_ergebnisfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_farbfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_farbfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_steuerfelder'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_steuerfelder'],
	'inputType'     => 'text',
	'eval'          => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_markColors'] = array
(
	'label'                               => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_markColors'],
	'exclude'                             => true,
	'inputType'                           => 'multiColumnWizard',
	'eval'                                => array
	(
		'tl_class'                        => 'long clr',
		'buttonPos'                       => 'middle',
		'buttons'                         => array
		(
			'copy'                        => false,
			'delete'                      => 'system/themes/flexible/icons/delete.svg',
			'move'                        => false,
			'up'                          => 'system/themes/flexible/icons/up.svg',
			'down'                        => 'system/themes/flexible/icons/down.svg'
		),
		'columnFields'                    => array
		(
			'intern' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_markColors_intern'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array('tl_class' => 'w50', 'style' => 'width:100%', 'mandatory' => true),
			),
			'name' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_markColors_name'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array('tl_class' => 'w50', 'style' => 'width:100%', 'mandatory' => true),
			),
			'color' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_markColors_color'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array('tl_class' => 'w50 wizard', 'colorpicker'=>true, 'style' => 'width:90%', 'mandatory' => true)
			),
		)
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['chesstable_css'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['chesstable_css'],
	'inputType'     => 'checkbox',
	'eval'          => array('tl_class' => 'w50 clr')
);

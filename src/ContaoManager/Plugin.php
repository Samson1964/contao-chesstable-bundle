<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoChesstableBundle\ContaoChesstableBundle;

/**
 * Meldet das Bundle beim Contao Manager an.
 */
class Plugin implements BundlePluginInterface
{
	/**
	 * Gibt die Ladereihenfolge des Bundles bekannt.
	 *
	 * Das Bundle muss nach dem Contao-Kern geladen werden, weil es dessen
	 * DCA-Dateien erweitert - unter anderem prüft die DCA von tl_content, ob der
	 * Kern das Feld "guests" mitbringt.
	 *
	 * @param ParserInterface $parser Wird zum Auflösen von Konfigurationsdateien
	 *                                benötigt, hier aber nicht verwendet
	 *
	 * @return BundleConfig[] Die Konfiguration dieses einen Bundles
	 */
	public function getBundles(ParserInterface $parser): array
	{
		return [
			BundleConfig::create(ContaoChesstableBundle::class)
				->setLoadAfter([ContaoCoreBundle::class]),
		];
	}
}

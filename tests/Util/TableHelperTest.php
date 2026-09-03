<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChesstableBundle\Util\TableHelper;

/**
 * Prüft die Umformungen, mit denen die Redakteurseingaben aufbereitet werden.
 */
class TableHelperTest extends TestCase
{
	/**
	 * Stellt sicher, dass Einzelnummern und Bereiche zu einer flachen Liste werden.
	 */
	public function testExpandRangesLoestBereicheAuf(): void
	{
		$this->assertSame([1, 3, 4, 5, 6, 7, 34], TableHelper::expandRanges('1,3-7,34'));
	}

	/**
	 * Leerzeichen um die Werte dürfen das Ergebnis nicht verändern.
	 */
	public function testExpandRangesIgnoriertLeerzeichen(): void
	{
		$this->assertSame([1, 5, 6], TableHelper::expandRanges(' 1 , 5 - 6 '));
	}

	/**
	 * Eine fehlende Angabe ist zulässig und bedeutet "keine Markierung".
	 */
	public function testExpandRangesLiefertLeeresArrayOhneAngabe(): void
	{
		$this->assertSame([], TableHelper::expandRanges(null));
		$this->assertSame([], TableHelper::expandRanges(''));
		$this->assertSame([], TableHelper::expandRanges('   '));
	}

	/**
	 * Unbrauchbare Angaben werden übergangen, ohne die brauchbaren zu verlieren.
	 */
	public function testExpandRangesUeberspringtUnbrauchbareAngaben(): void
	{
		$this->assertSame([4], TableHelper::expandRanges('abc,4,7-,-,x-y'));
	}

	/**
	 * Ein Bereich mit vertauschten Grenzen liefert nichts.
	 */
	public function testExpandRangesIgnoriertVertauschteGrenzen(): void
	{
		$this->assertSame([], TableHelper::expandRanges('7-3'));
	}

	/**
	 * Ein versehentlich riesiger Bereich darf den Speicher nicht füllen.
	 */
	public function testExpandRangesBegrenztGrosseBereiche(): void
	{
		$this->assertCount(1000, TableHelper::expandRanges('1-999999'));
	}

	/**
	 * Der Name wird an den Kommata zerlegt und in Leserichtung gedreht.
	 */
	public function testRotateNameDrehtDieBestandteile(): void
	{
		$this->assertSame('GM Richard Rapport', TableHelper::rotateName('Rapport,Richard,GM'));
	}

	/**
	 * Ein Name ohne Komma bleibt erhalten, überflüssige Leerzeichen fallen weg.
	 */
	public function testRotateNameOhneKomma(): void
	{
		$this->assertSame('Rapport', TableHelper::rotateName('  Rapport  '));
	}

	/**
	 * Die eigene CSS-Klasse wird aus dem Zellinhalt herausgelöst.
	 */
	public function testExtractClassTrenntKlasseUndInhalt(): void
	{
		$this->assertSame(['GM Richard Rapport', 'own_sieger'], TableHelper::extractClass('[sieger]GM Richard Rapport'));
	}

	/**
	 * Die Klammern dürfen auch hinter dem Text stehen.
	 */
	public function testExtractClassErkenntKlasseAmEnde(): void
	{
		$this->assertSame(['Meier', 'own_2'], TableHelper::extractClass('Meier[2]'));
	}

	/**
	 * Ohne Klammern bleibt der Inhalt unverändert und die Klasse leer.
	 */
	public function testExtractClassOhneKlammern(): void
	{
		$this->assertSame(['Meier', ''], TableHelper::extractClass('Meier'));
	}

	/**
	 * Eine unvollständige oder leere Klammerangabe darf keine Klasse erzeugen.
	 */
	public function testExtractClassBeiUnvollstaendigerAngabe(): void
	{
		$this->assertSame(['Meier[2', ''], TableHelper::extractClass('Meier[2'));
		$this->assertSame(['Meier', ''], TableHelper::extractClass('Meier[]'));
	}

	/**
	 * Über die Klammerangabe darf sich kein HTML-Attribut einschleusen lassen.
	 */
	public function testExtractClassFiltertGefaehrlicheZeichen(): void
	{
		$this->assertSame(['Meier', 'own_aonclickalert1'], TableHelper::extractClass('[a" onclick="alert(1)]Meier'));
	}

	/**
	 * Eine ganze Zahl bekommt die Nachkommastelle ",0".
	 */
	public function testFormatPointsGanzeZahl(): void
	{
		$this->assertSame('4,0', TableHelper::formatPoints('4'));
		$this->assertSame('0,0', TableHelper::formatPoints('0'));
		$this->assertSame('10,0', TableHelper::formatPoints('10'));
	}

	/**
	 * Eine Zahl mit angehängtem Halbe-Punkte-Zeichen wird zur Dezimalzahl.
	 */
	public function testFormatPointsMitHalbemPunkt(): void
	{
		$this->assertSame('4,5', TableHelper::formatPoints('4½'));
		$this->assertSame('0,5', TableHelper::formatPoints('½'));
	}

	/**
	 * Bereits formatierte oder unbekannte Werte bleiben unangetastet.
	 */
	public function testFormatPointsLaesstAndereWerteUnveraendert(): void
	{
		$this->assertSame('4,5', TableHelper::formatPoints('4,5'));
		$this->assertSame('4.5', TableHelper::formatPoints('4.5'));
		$this->assertSame('', TableHelper::formatPoints(''));
		$this->assertSame('Pkt.', TableHelper::formatPoints('Pkt.'));
	}

	/**
	 * Leerzeichen um den Wert dürfen die Erkennung nicht stören.
	 */
	public function testFormatPointsIgnoriertLeerzeichen(): void
	{
		$this->assertSame('4,0', TableHelper::formatPoints(' 4 '));
	}

	/**
	 * Der Zusatz "e.V." wird in allen gebräuchlichen Schreibweisen entfernt.
	 */
	public function testShortenClubNameEntferntEingetragenerVereinZusatz(): void
	{
		$this->assertSame('SV Budapest', TableHelper::shortenClubName('SV Budapest e.V.'));
		$this->assertSame('SC Havanna', TableHelper::shortenClubName('SC Havanna e. V.'));
		$this->assertSame('TSV Musterstadt', TableHelper::shortenClubName('TSV Musterstadt (e.V.)'));
		$this->assertSame('Club', TableHelper::shortenClubName('Club eingetragener Verein'));
	}

	/**
	 * Ein Vereinsname ohne bekannten Zusatz bleibt unverändert.
	 */
	public function testShortenClubNameOhneZusatz(): void
	{
		$this->assertSame('SC Riga', TableHelper::shortenClubName('SC Riga'));
	}
}

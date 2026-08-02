<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChesstableBundle\Util\CountryCodes;

/**
 * Prüft die Zuordnung von Länderkürzeln zu den Flaggen-CSS-Klassen.
 */
class CountryCodesTest extends TestCase
{
	/**
	 * Der in Schachtabellen übliche IOC-Code muss erkannt werden.
	 */
	public function testErkenntIocCode(): void
	{
		$this->assertSame('flag-icon flag-icon-de', CountryCodes::getFlagClass('GER'));
		$this->assertSame('flag-icon flag-icon-us', CountryCodes::getFlagClass('USA'));
	}

	/**
	 * Groß-/Kleinschreibung und Leerzeichen kommen in CSV-Daten häufig vor.
	 */
	public function testIgnoriertSchreibweiseUndLeerzeichen(): void
	{
		$this->assertSame('flag-icon flag-icon-de', CountryCodes::getFlagClass(' ger '));
	}

	/**
	 * Weicht der ISO-Code vom IOC-Code ab, muss auch der ISO-Code greifen.
	 *
	 * Der Iran heißt im IOC-Code IRI und in der ISO-Norm IRN; in der Praxis
	 * tauchen beide Schreibweisen in den Turnierdaten auf.
	 */
	public function testErkenntAlpha3AlsAusweichweg(): void
	{
		$this->assertSame('flag-icon flag-icon-ir', CountryCodes::getFlagClass('IRI'));
		$this->assertSame('flag-icon flag-icon-ir', CountryCodes::getFlagClass('IRN'));
	}

	/**
	 * Die im Schach gebräuchlichen historischen Verbände müssen erhalten bleiben.
	 */
	public function testErkenntHistorischeVerbaende(): void
	{
		$this->assertSame('flag-icon flag-icon-su', CountryCodes::getFlagClass('URS'));
		$this->assertSame('flag-icon flag-icon-dd', CountryCodes::getFlagClass('GDR'));
	}

	/**
	 * Die britischen Landesteile stellen eigene Mannschaften.
	 */
	public function testErkenntBritischeLandesteile(): void
	{
		$this->assertSame('flag-icon flag-icon-gb-sct', CountryCodes::getFlagClass('SCO'));
		$this->assertSame('flag-icon flag-icon-gb-wls', CountryCodes::getFlagClass('WAL'));
	}

	/**
	 * Unbekannte oder leere Kürzel liefern keine Klasse, damit der Aufrufer
	 * stattdessen den Text ausgeben kann.
	 */
	public function testLiefertLeerenStringBeiUnbekanntemKuerzel(): void
	{
		$this->assertSame('', CountryCodes::getFlagClass('XYZ'));
		$this->assertSame('', CountryCodes::getFlagClass(''));
		$this->assertSame('', CountryCodes::getFlagClass('   '));
	}
}

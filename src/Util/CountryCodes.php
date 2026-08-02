<?php

declare(strict_types=1);

namespace Schachbulle\ContaoChesstableBundle\Util;

/**
 * Zuordnung von Länderkürzeln zu den CSS-Klassen des Pakets components/flag-icon-css.
 *
 * Die Flaggenbibliothek arbeitet ausschließlich mit ISO-3166-Alpha-2-Codes, in
 * Schachtabellen sind aber die dreistelligen IOC-Codes üblich (GER statt DE).
 * Diese Klasse übersetzt zwischen beiden Welten und kennt zusätzlich die im
 * Schach gebräuchlichen historischen Verbände (UdSSR, DDR, Tschechoslowakei)
 * sowie die vier britischen Landesteile, die im Sport eigene Mannschaften
 * stellen, in der ISO-Norm aber nicht als Staaten geführt werden.
 */
final class CountryCodes
{
	/**
	 * Ermittelt die CSS-Klassen für die Flagge eines Landes.
	 *
	 * Gesucht wird zuerst im IOC-Code, weil das der in Schachtabellen erwartete
	 * Fall ist. Erst wenn dort nichts gefunden wird, greift die Suche im
	 * Alpha-3-Code der ISO-Norm. Das brauchen Länder wie der Iran, deren
	 * IOC-Code (IRI) und ISO-Code (IRN) auseinanderlaufen und bei denen in der
	 * Praxis beide Schreibweisen in den CSV-Daten auftauchen.
	 *
	 * @param string $code Länderkürzel aus den CSV-Daten, Groß-/Kleinschreibung
	 *                     und umgebende Leerzeichen sind egal
	 *
	 * @return string Die beiden CSS-Klassen, z. B. "flag-icon flag-icon-de",
	 *                oder ein leerer String, wenn zu dem Kürzel keine Flagge
	 *                bekannt ist. Der Aufrufer gibt dann das Kürzel als Text aus.
	 */
	public static function getFlagClass(string $code): string
	{
		$code = trim(strtoupper($code));

		if ($code === '')
		{
			return '';
		}

		foreach (self::COUNTRIES as $country)
		{
			if ($country['ioc'] === $code)
			{
				return 'flag-icon flag-icon-'.strtolower($country['alpha2']);
			}
		}

		foreach (self::COUNTRIES as $country)
		{
			if ($country['alpha3'] === $code)
			{
				return 'flag-icon flag-icon-'.strtolower($country['alpha2']);
			}
		}

		return '';
	}

	/**
	 * Länderliste mit ISO-3166-Codes und IOC-Code.
	 *
	 * Ein leerer IOC-Code bedeutet, dass das Gebiet keine eigene olympische
	 * Mannschaft stellt; solche Einträge sind nur über den Alpha-3-Code
	 * erreichbar. Der Name dient allein der Lesbarkeit dieser Liste.
	 */
	private const COUNTRIES = [
		['name' => 'Afghanistan', 'alpha2' => 'AF', 'alpha3' => 'AFG', 'ioc' => 'AFG'],
		['name' => 'Ägypten', 'alpha2' => 'EG', 'alpha3' => 'EGY', 'ioc' => 'EGY'],
		['name' => 'Åland', 'alpha2' => 'AX', 'alpha3' => 'ALA', 'ioc' => ''],
		['name' => 'Albanien', 'alpha2' => 'AL', 'alpha3' => 'ALB', 'ioc' => 'ALB'],
		['name' => 'Algerien', 'alpha2' => 'DZ', 'alpha3' => 'DZA', 'ioc' => 'ALG'],
		['name' => 'Amerikanisch-Samoa', 'alpha2' => 'AS', 'alpha3' => 'ASM', 'ioc' => 'ASA'],
		['name' => 'Amerikanische Jungferninseln', 'alpha2' => 'VI', 'alpha3' => 'VIR', 'ioc' => 'ISV'],
		['name' => 'Andorra', 'alpha2' => 'AD', 'alpha3' => 'AND', 'ioc' => 'AND'],
		['name' => 'Angola', 'alpha2' => 'AO', 'alpha3' => 'AGO', 'ioc' => 'ANG'],
		['name' => 'Anguilla', 'alpha2' => 'AI', 'alpha3' => 'AIA', 'ioc' => ''],
		['name' => 'Antarktika', 'alpha2' => 'AQ', 'alpha3' => 'ATA', 'ioc' => ''],
		['name' => 'Antigua und Barbuda', 'alpha2' => 'AG', 'alpha3' => 'ATG', 'ioc' => 'ANT'],
		['name' => 'Äquatorialguinea', 'alpha2' => 'GQ', 'alpha3' => 'GNQ', 'ioc' => 'GEQ'],
		['name' => 'Argentinien', 'alpha2' => 'AR', 'alpha3' => 'ARG', 'ioc' => 'ARG'],
		['name' => 'Armenien', 'alpha2' => 'AM', 'alpha3' => 'ARM', 'ioc' => 'ARM'],
		['name' => 'Aruba', 'alpha2' => 'AW', 'alpha3' => 'ABW', 'ioc' => 'ARU'],
		['name' => 'Ascension', 'alpha2' => 'AC', 'alpha3' => 'ASC', 'ioc' => ''],
		['name' => 'Aserbaidschan', 'alpha2' => 'AZ', 'alpha3' => 'AZE', 'ioc' => 'AZE'],
		['name' => 'Äthiopien', 'alpha2' => 'ET', 'alpha3' => 'ETH', 'ioc' => 'ETH'],
		['name' => 'Australien', 'alpha2' => 'AU', 'alpha3' => 'AUS', 'ioc' => 'AUS'],
		['name' => 'Bahamas', 'alpha2' => 'BS', 'alpha3' => 'BHS', 'ioc' => 'BAH'],
		['name' => 'Bahrain', 'alpha2' => 'BH', 'alpha3' => 'BHR', 'ioc' => 'BRN'],
		['name' => 'Bangladesch', 'alpha2' => 'BD', 'alpha3' => 'BGD', 'ioc' => 'BAN'],
		['name' => 'Barbados', 'alpha2' => 'BB', 'alpha3' => 'BRB', 'ioc' => 'BAR'],
		['name' => 'Weißrussland', 'alpha2' => 'BY', 'alpha3' => 'BLR', 'ioc' => 'BLR'],
		['name' => 'Belgien', 'alpha2' => 'BE', 'alpha3' => 'BEL', 'ioc' => 'BEL'],
		['name' => 'Belize', 'alpha2' => 'BZ', 'alpha3' => 'BLZ', 'ioc' => 'BIZ'],
		['name' => 'Benin', 'alpha2' => 'BJ', 'alpha3' => 'BEN', 'ioc' => 'BEN'],
		['name' => 'Bermuda', 'alpha2' => 'BM', 'alpha3' => 'BMU', 'ioc' => 'BER'],
		['name' => 'Bhutan', 'alpha2' => 'BT', 'alpha3' => 'BTN', 'ioc' => 'BHU'],
		['name' => 'Bolivien', 'alpha2' => 'BO', 'alpha3' => 'BOL', 'ioc' => 'BOL'],
		['name' => 'Bonaire, Sint Eustatius und Saba', 'alpha2' => 'BQ', 'alpha3' => 'BES', 'ioc' => ''],
		['name' => 'Bosnien und Herzegowina', 'alpha2' => 'BA', 'alpha3' => 'BIH', 'ioc' => 'BIH'],
		['name' => 'Botswana', 'alpha2' => 'BW', 'alpha3' => 'BWA', 'ioc' => 'BOT'],
		['name' => 'Bouvetinsel', 'alpha2' => 'BV', 'alpha3' => 'BVT', 'ioc' => ''],
		['name' => 'Brasilien', 'alpha2' => 'BR', 'alpha3' => 'BRA', 'ioc' => 'BRA'],
		['name' => 'Britische Jungferninseln', 'alpha2' => 'VG', 'alpha3' => 'VGB', 'ioc' => 'IVB'],
		['name' => 'Britisches Territorium im Indischen Ozean', 'alpha2' => 'IO', 'alpha3' => 'IOT', 'ioc' => ''],
		['name' => 'Brunei Darussalam', 'alpha2' => 'BN', 'alpha3' => 'BRN', 'ioc' => 'BRU'],
		['name' => 'Bulgarien', 'alpha2' => 'BG', 'alpha3' => 'BGR', 'ioc' => 'BUL'],
		['name' => 'Burkina Faso', 'alpha2' => 'BF', 'alpha3' => 'BFA', 'ioc' => 'BUR'],
		['name' => 'Burma', 'alpha2' => 'BU', 'alpha3' => 'BUR', 'ioc' => ''],
		['name' => 'Burundi', 'alpha2' => 'BI', 'alpha3' => 'BDI', 'ioc' => 'BDI'],
		['name' => 'Ceuta, Melilla', 'alpha2' => 'EA', 'alpha3' => '', 'ioc' => ''],
		['name' => 'Chile', 'alpha2' => 'CL', 'alpha3' => 'CHL', 'ioc' => 'CHI'],
		['name' => 'China', 'alpha2' => 'CN', 'alpha3' => 'CHN', 'ioc' => 'CHN'],
		['name' => 'Clipperton', 'alpha2' => 'CP', 'alpha3' => 'CPT', 'ioc' => ''],
		['name' => 'Cookinseln', 'alpha2' => 'CK', 'alpha3' => 'COK', 'ioc' => 'COK'],
		['name' => 'Costa Rica', 'alpha2' => 'CR', 'alpha3' => 'CRI', 'ioc' => 'CRC'],
		['name' => 'Elfenbeinküste', 'alpha2' => 'CI', 'alpha3' => 'CIV', 'ioc' => 'CIV'],
		['name' => 'Curaçao', 'alpha2' => 'CW', 'alpha3' => 'CUW', 'ioc' => ''],
		['name' => 'Dänemark', 'alpha2' => 'DK', 'alpha3' => 'DNK', 'ioc' => 'DEN'],
		['name' => 'DDR', 'alpha2' => 'DD', 'alpha3' => '', 'ioc' => 'GDR'],
		['name' => 'BRD', 'alpha2' => 'DE', 'alpha3' => 'DEU', 'ioc' => 'FRG'],
		['name' => 'Deutschland', 'alpha2' => 'DE', 'alpha3' => 'DEU', 'ioc' => 'GER'],
		['name' => 'Diego Garcia', 'alpha2' => 'DG', 'alpha3' => 'DGA', 'ioc' => ''],
		['name' => 'Dominica', 'alpha2' => 'DM', 'alpha3' => 'DMA', 'ioc' => 'DMA'],
		['name' => 'Dominikanische Republik', 'alpha2' => 'DO', 'alpha3' => 'DOM', 'ioc' => 'DOM'],
		['name' => 'Dschibuti', 'alpha2' => 'DJ', 'alpha3' => 'DJI', 'ioc' => 'DJI'],
		['name' => 'Ekuador', 'alpha2' => 'EC', 'alpha3' => 'ECU', 'ioc' => 'ECU'],
		['name' => 'El Salvador', 'alpha2' => 'SV', 'alpha3' => 'SLV', 'ioc' => 'ESA'],
		['name' => 'Eritrea', 'alpha2' => 'ER', 'alpha3' => 'ERI', 'ioc' => 'ERI'],
		['name' => 'Estland', 'alpha2' => 'EE', 'alpha3' => 'EST', 'ioc' => 'EST'],
		['name' => 'Falklandinseln', 'alpha2' => 'FK', 'alpha3' => 'FLK', 'ioc' => ''],
		['name' => 'Färöer', 'alpha2' => 'FO', 'alpha3' => 'FRO', 'ioc' => 'FRO'],
		['name' => 'Fidschi', 'alpha2' => 'FJ', 'alpha3' => 'FJI', 'ioc' => 'FIJ'],
		['name' => 'Finnland', 'alpha2' => 'FI', 'alpha3' => 'FIN', 'ioc' => 'FIN'],
		['name' => 'Frankreich', 'alpha2' => 'FR', 'alpha3' => 'FRA', 'ioc' => 'FRA'],
		['name' => 'Französisch-Guayana', 'alpha2' => 'GF', 'alpha3' => 'GUF', 'ioc' => ''],
		['name' => 'Französisch-Polynesien', 'alpha2' => 'PF', 'alpha3' => 'PYF', 'ioc' => ''],
		['name' => 'Französische Süd- und Antarktisgebiete', 'alpha2' => 'TF', 'alpha3' => 'ATF', 'ioc' => ''],
		['name' => 'Gabun', 'alpha2' => 'GA', 'alpha3' => 'GAB', 'ioc' => 'GAB'],
		['name' => 'Gambia', 'alpha2' => 'GM', 'alpha3' => 'GMB', 'ioc' => 'GAM'],
		['name' => 'Georgien', 'alpha2' => 'GE', 'alpha3' => 'GEO', 'ioc' => 'GEO'],
		['name' => 'Ghana', 'alpha2' => 'GH', 'alpha3' => 'GHA', 'ioc' => 'GHA'],
		['name' => 'Gibraltar', 'alpha2' => 'GI', 'alpha3' => 'GIB', 'ioc' => ''],
		['name' => 'Grenada', 'alpha2' => 'GD', 'alpha3' => 'GRD', 'ioc' => 'GRN'],
		['name' => 'Griechenland', 'alpha2' => 'GR', 'alpha3' => 'GRC', 'ioc' => 'GRE'],
		['name' => 'Grönland', 'alpha2' => 'GL', 'alpha3' => 'GRL', 'ioc' => ''],
		['name' => 'Guadeloupe', 'alpha2' => 'GP', 'alpha3' => 'GLP', 'ioc' => ''],
		['name' => 'Guam', 'alpha2' => 'GU', 'alpha3' => 'GUM', 'ioc' => 'GUM'],
		['name' => 'Guatemala', 'alpha2' => 'GT', 'alpha3' => 'GTM', 'ioc' => 'GUA'],
		['name' => 'Guernsey', 'alpha2' => 'GG', 'alpha3' => 'GGY', 'ioc' => ''],
		['name' => 'Guinea', 'alpha2' => 'GN', 'alpha3' => 'GIN', 'ioc' => 'GUI'],
		['name' => 'Guinea-Bissau', 'alpha2' => 'GW', 'alpha3' => 'GNB', 'ioc' => 'GBS'],
		['name' => 'Guyana', 'alpha2' => 'GY', 'alpha3' => 'GUY', 'ioc' => 'GUY'],
		['name' => 'Haiti', 'alpha2' => 'HT', 'alpha3' => 'HTI', 'ioc' => 'HAI'],
		['name' => 'Heard und McDonaldinseln', 'alpha2' => 'HM', 'alpha3' => 'HMD', 'ioc' => ''],
		['name' => 'Honduras', 'alpha2' => 'HN', 'alpha3' => 'HND', 'ioc' => 'HON'],
		['name' => 'Hongkong', 'alpha2' => 'HK', 'alpha3' => 'HKG', 'ioc' => 'HKG'],
		['name' => 'Indien', 'alpha2' => 'IN', 'alpha3' => 'IND', 'ioc' => 'IND'],
		['name' => 'Indonesien', 'alpha2' => 'ID', 'alpha3' => 'IDN', 'ioc' => 'INA'],
		['name' => 'Insel Man', 'alpha2' => 'IM', 'alpha3' => 'IMN', 'ioc' => ''],
		['name' => 'Irak', 'alpha2' => 'IQ', 'alpha3' => 'IRQ', 'ioc' => 'IRQ'],
		['name' => 'Iran', 'alpha2' => 'IR', 'alpha3' => 'IRN', 'ioc' => 'IRI'],
		['name' => 'Irland', 'alpha2' => 'IE', 'alpha3' => 'IRL', 'ioc' => 'IRL'],
		['name' => 'Island', 'alpha2' => 'IS', 'alpha3' => 'ISL', 'ioc' => 'ISL'],
		['name' => 'Israel', 'alpha2' => 'IL', 'alpha3' => 'ISR', 'ioc' => 'ISR'],
		['name' => 'Italien', 'alpha2' => 'IT', 'alpha3' => 'ITA', 'ioc' => 'ITA'],
		['name' => 'Jamaika', 'alpha2' => 'JM', 'alpha3' => 'JAM', 'ioc' => 'JAM'],
		['name' => 'Japan', 'alpha2' => 'JP', 'alpha3' => 'JPN', 'ioc' => 'JPN'],
		['name' => 'Jemen', 'alpha2' => 'YE', 'alpha3' => 'YEM', 'ioc' => 'YEM'],
		['name' => 'Jersey', 'alpha2' => 'JE', 'alpha3' => 'JEY', 'ioc' => ''],
		['name' => 'Jordanien', 'alpha2' => 'JO', 'alpha3' => 'JOR', 'ioc' => 'JOR'],
		['name' => 'Jugoslawien', 'alpha2' => 'YU', 'alpha3' => 'YUG', 'ioc' => 'YUG'],
		['name' => 'Kaimaninseln', 'alpha2' => 'KY', 'alpha3' => 'CYM', 'ioc' => 'CAY'],
		['name' => 'Kambodscha', 'alpha2' => 'KH', 'alpha3' => 'KHM', 'ioc' => 'CAM'],
		['name' => 'Kamerun', 'alpha2' => 'CM', 'alpha3' => 'CMR', 'ioc' => 'CMR'],
		['name' => 'Kanada', 'alpha2' => 'CA', 'alpha3' => 'CAN', 'ioc' => 'CAN'],
		['name' => 'Kanarische Inseln', 'alpha2' => 'IC', 'alpha3' => '', 'ioc' => ''],
		['name' => 'Kap Verde', 'alpha2' => 'CV', 'alpha3' => 'CPV', 'ioc' => 'CPV'],
		['name' => 'Kasachstan', 'alpha2' => 'KZ', 'alpha3' => 'KAZ', 'ioc' => 'KAZ'],
		['name' => 'Katar', 'alpha2' => 'QA', 'alpha3' => 'QAT', 'ioc' => 'QAT'],
		['name' => 'Kenia', 'alpha2' => 'KE', 'alpha3' => 'KEN', 'ioc' => 'KEN'],
		['name' => 'Kirgisistan', 'alpha2' => 'KG', 'alpha3' => 'KGZ', 'ioc' => 'KGZ'],
		['name' => 'Kiribati', 'alpha2' => 'KI', 'alpha3' => 'KIR', 'ioc' => 'KIR'],
		['name' => 'Kokosinseln', 'alpha2' => 'CC', 'alpha3' => 'CCK', 'ioc' => ''],
		['name' => 'Kolumbien', 'alpha2' => 'CO', 'alpha3' => 'COL', 'ioc' => 'COL'],
		['name' => 'Komoren', 'alpha2' => 'KM', 'alpha3' => 'COM', 'ioc' => 'COM'],
		['name' => 'Kongo, Demokratische Republik', 'alpha2' => 'CD', 'alpha3' => 'COD', 'ioc' => 'COD'],
		['name' => 'Kongo, Republik', 'alpha2' => 'CG', 'alpha3' => 'COG', 'ioc' => 'CGO'],
		['name' => 'Nordkorea', 'alpha2' => 'KP', 'alpha3' => 'PRK', 'ioc' => 'PRK'],
		['name' => 'Südkorea', 'alpha2' => 'KR', 'alpha3' => 'KOR', 'ioc' => 'KOR'],
		['name' => 'Kosovo', 'alpha2' => 'XK', 'alpha3' => 'XKX', 'ioc' => 'KOS'],
		['name' => 'Kroatien', 'alpha2' => 'HR', 'alpha3' => 'HRV', 'ioc' => 'CRO'],
		['name' => 'Kuba', 'alpha2' => 'CU', 'alpha3' => 'CUB', 'ioc' => 'CUB'],
		['name' => 'Kuwait', 'alpha2' => 'KW', 'alpha3' => 'KWT', 'ioc' => 'KUW'],
		['name' => 'Laos', 'alpha2' => 'LA', 'alpha3' => 'LAO', 'ioc' => 'LAO'],
		['name' => 'Lesotho', 'alpha2' => 'LS', 'alpha3' => 'LSO', 'ioc' => 'LES'],
		['name' => 'Lettland', 'alpha2' => 'LV', 'alpha3' => 'LVA', 'ioc' => 'LAT'],
		['name' => 'Libanon', 'alpha2' => 'LB', 'alpha3' => 'LBN', 'ioc' => 'LIB'],
		['name' => 'Liberia', 'alpha2' => 'LR', 'alpha3' => 'LBR', 'ioc' => 'LBR'],
		['name' => 'Libyen', 'alpha2' => 'LY', 'alpha3' => 'LBY', 'ioc' => 'LBA'],
		['name' => 'Liechtenstein', 'alpha2' => 'LI', 'alpha3' => 'LIE', 'ioc' => 'LIE'],
		['name' => 'Litauen', 'alpha2' => 'LT', 'alpha3' => 'LTU', 'ioc' => 'LTU'],
		['name' => 'Luxemburg', 'alpha2' => 'LU', 'alpha3' => 'LUX', 'ioc' => 'LUX'],
		['name' => 'Macau', 'alpha2' => 'MO', 'alpha3' => 'MAC', 'ioc' => ''],
		['name' => 'Madagaskar', 'alpha2' => 'MG', 'alpha3' => 'MDG', 'ioc' => 'MAD'],
		['name' => 'Malawi', 'alpha2' => 'MW', 'alpha3' => 'MWI', 'ioc' => 'MAW'],
		['name' => 'Malaysia', 'alpha2' => 'MY', 'alpha3' => 'MYS', 'ioc' => 'MAS'],
		['name' => 'Malediven', 'alpha2' => 'MV', 'alpha3' => 'MDV', 'ioc' => 'MDV'],
		['name' => 'Mali', 'alpha2' => 'ML', 'alpha3' => 'MLI', 'ioc' => 'MLI'],
		['name' => 'Malta', 'alpha2' => 'MT', 'alpha3' => 'MLT', 'ioc' => 'MLT'],
		['name' => 'Marokko', 'alpha2' => 'MA', 'alpha3' => 'MAR', 'ioc' => 'MAR'],
		['name' => 'Marshallinseln', 'alpha2' => 'MH', 'alpha3' => 'MHL', 'ioc' => 'MHL'],
		['name' => 'Martinique', 'alpha2' => 'MQ', 'alpha3' => 'MTQ', 'ioc' => ''],
		['name' => 'Mauretanien', 'alpha2' => 'MR', 'alpha3' => 'MRT', 'ioc' => 'MTN'],
		['name' => 'Mauritius', 'alpha2' => 'MU', 'alpha3' => 'MUS', 'ioc' => 'MRI'],
		['name' => 'Mayotte', 'alpha2' => 'YT', 'alpha3' => 'MYT', 'ioc' => ''],
		['name' => 'Mexiko', 'alpha2' => 'MX', 'alpha3' => 'MEX', 'ioc' => 'MEX'],
		['name' => 'Mikronesien', 'alpha2' => 'FM', 'alpha3' => 'FSM', 'ioc' => 'FSM'],
		['name' => 'Moldawien', 'alpha2' => 'MD', 'alpha3' => 'MDA', 'ioc' => 'MDA'],
		['name' => 'Monaco', 'alpha2' => 'MC', 'alpha3' => 'MCO', 'ioc' => 'MON'],
		['name' => 'Mongolei', 'alpha2' => 'MN', 'alpha3' => 'MNG', 'ioc' => 'MGL'],
		['name' => 'Montenegro', 'alpha2' => 'ME', 'alpha3' => 'MNE', 'ioc' => 'MNE'],
		['name' => 'Montserrat', 'alpha2' => 'MS', 'alpha3' => 'MSR', 'ioc' => ''],
		['name' => 'Mosambik', 'alpha2' => 'MZ', 'alpha3' => 'MOZ', 'ioc' => 'MOZ'],
		['name' => 'Myanmar', 'alpha2' => 'MM', 'alpha3' => 'MMR', 'ioc' => 'MYA'],
		['name' => 'Namibia', 'alpha2' => 'NA', 'alpha3' => 'NAM', 'ioc' => 'NAM'],
		['name' => 'Nauru', 'alpha2' => 'NR', 'alpha3' => 'NRU', 'ioc' => 'NRU'],
		['name' => 'Nepal', 'alpha2' => 'NP', 'alpha3' => 'NPL', 'ioc' => 'NEP'],
		['name' => 'Neukaledonien', 'alpha2' => 'NC', 'alpha3' => 'NCL', 'ioc' => ''],
		['name' => 'Neuseeland', 'alpha2' => 'NZ', 'alpha3' => 'NZL', 'ioc' => 'NZL'],
		['name' => 'Neutrale Zone', 'alpha2' => 'NT', 'alpha3' => 'NTZ', 'ioc' => ''],
		['name' => 'Nicaragua', 'alpha2' => 'NI', 'alpha3' => 'NIC', 'ioc' => 'NCA'],
		['name' => 'Niederlande', 'alpha2' => 'NL', 'alpha3' => 'NLD', 'ioc' => 'NED'],
		['name' => 'Niederländische Antillen', 'alpha2' => 'AN', 'alpha3' => 'ANT', 'ioc' => 'AHO'],
		['name' => 'Niger', 'alpha2' => 'NE', 'alpha3' => 'NER', 'ioc' => 'NIG'],
		['name' => 'Nigeria', 'alpha2' => 'NG', 'alpha3' => 'NGA', 'ioc' => 'NGR'],
		['name' => 'Niue', 'alpha2' => 'NU', 'alpha3' => 'NIU', 'ioc' => ''],
		['name' => 'Nördliche Marianen', 'alpha2' => 'MP', 'alpha3' => 'MNP', 'ioc' => ''],
		['name' => 'Nordmazedonien', 'alpha2' => 'MK', 'alpha3' => 'MKD', 'ioc' => 'MKD'],
		['name' => 'Norfolkinsel', 'alpha2' => 'NF', 'alpha3' => 'NFK', 'ioc' => ''],
		['name' => 'Norwegen', 'alpha2' => 'NO', 'alpha3' => 'NOR', 'ioc' => 'NOR'],
		['name' => 'Oman', 'alpha2' => 'OM', 'alpha3' => 'OMN', 'ioc' => 'OMA'],
		['name' => 'Österreich', 'alpha2' => 'AT', 'alpha3' => 'AUT', 'ioc' => 'AUT'],
		['name' => 'Osttimor', 'alpha2' => 'TL    )', 'alpha3' => 'TLS', 'ioc' => 'TLS'],
		['name' => 'Pakistan', 'alpha2' => 'PK', 'alpha3' => 'PAK', 'ioc' => 'PAK'],
		['name' => 'Palästina', 'alpha2' => 'PS', 'alpha3' => 'PSE', 'ioc' => 'PLE'],
		['name' => 'Palau', 'alpha2' => 'PW', 'alpha3' => 'PLW', 'ioc' => 'PLW'],
		['name' => 'Panama', 'alpha2' => 'PA', 'alpha3' => 'PAN', 'ioc' => 'PAN'],
		['name' => 'Papua-Neuguinea', 'alpha2' => 'PG', 'alpha3' => 'PNG', 'ioc' => 'PNG'],
		['name' => 'Paraguay', 'alpha2' => 'PY', 'alpha3' => 'PRY', 'ioc' => 'PAR'],
		['name' => 'Peru', 'alpha2' => 'PE', 'alpha3' => 'PER', 'ioc' => 'PER'],
		['name' => 'Philippinen', 'alpha2' => 'PH', 'alpha3' => 'PHL', 'ioc' => 'PHI'],
		['name' => 'Pitcairninseln', 'alpha2' => 'PN', 'alpha3' => 'PCN', 'ioc' => ''],
		['name' => 'Polen', 'alpha2' => 'PL', 'alpha3' => 'POL', 'ioc' => 'POL'],
		['name' => 'Portugal', 'alpha2' => 'PT', 'alpha3' => 'PRT', 'ioc' => 'POR'],
		['name' => 'Puerto Rico', 'alpha2' => 'PR', 'alpha3' => 'PRI', 'ioc' => 'PUR'],
		['name' => 'Réunion', 'alpha2' => 'RE', 'alpha3' => 'REU', 'ioc' => ''],
		['name' => 'Ruanda', 'alpha2' => 'RW', 'alpha3' => 'RWA', 'ioc' => 'RWA'],
		['name' => 'Rumänien', 'alpha2' => 'RO', 'alpha3' => 'ROU', 'ioc' => 'ROU'],
		['name' => 'Russland', 'alpha2' => 'RU', 'alpha3' => 'RUS', 'ioc' => 'RUS'],
		['name' => 'Salomonen', 'alpha2' => 'SB', 'alpha3' => 'SLB', 'ioc' => 'SOL'],
		['name' => 'Saint-Barthélemy', 'alpha2' => 'BL', 'alpha3' => 'BLM', 'ioc' => ''],
		['name' => 'Saint-Martin', 'alpha2' => 'MF', 'alpha3' => 'MAF', 'ioc' => ''],
		['name' => 'Sambia', 'alpha2' => 'ZM', 'alpha3' => 'ZMB', 'ioc' => 'ZAM'],
		['name' => 'Samoa', 'alpha2' => 'WS', 'alpha3' => 'WSM', 'ioc' => 'SAM'],
		['name' => 'San Marino', 'alpha2' => 'SM', 'alpha3' => 'SMR', 'ioc' => 'SMR'],
		['name' => 'São Tomé und Príncipe', 'alpha2' => 'ST', 'alpha3' => 'STP', 'ioc' => 'STP'],
		['name' => 'Saudi-Arabien', 'alpha2' => 'SA', 'alpha3' => 'SAU', 'ioc' => 'KSA'],
		['name' => 'Schweden', 'alpha2' => 'SE', 'alpha3' => 'SWE', 'ioc' => 'SWE'],
		['name' => 'Schweiz', 'alpha2' => 'CH', 'alpha3' => 'CHE', 'ioc' => 'SUI'],
		['name' => 'Senegal', 'alpha2' => 'SN', 'alpha3' => 'SEN', 'ioc' => 'SEN'],
		['name' => 'Serbien', 'alpha2' => 'RS', 'alpha3' => 'SRB', 'ioc' => 'SRB'],
		['name' => 'Serbien und Montenegro', 'alpha2' => 'CS', 'alpha3' => 'SCG', 'ioc' => 'SCG'],
		['name' => 'Seychellen', 'alpha2' => 'SC', 'alpha3' => 'SYC', 'ioc' => 'SEY'],
		['name' => 'Sierra Leone', 'alpha2' => 'SL', 'alpha3' => 'SLE', 'ioc' => 'SLE'],
		['name' => 'Simbabwe', 'alpha2' => 'ZW', 'alpha3' => 'ZWE', 'ioc' => 'ZIM'],
		['name' => 'Singapur', 'alpha2' => 'SG', 'alpha3' => 'SGP', 'ioc' => 'SGP'],
		['name' => 'Sint Maarten', 'alpha2' => 'SX', 'alpha3' => 'SXM', 'ioc' => ''],
		['name' => 'Slowakei', 'alpha2' => 'SK', 'alpha3' => 'SVK', 'ioc' => 'SVK'],
		['name' => 'Slowenien', 'alpha2' => 'SI', 'alpha3' => 'SVN', 'ioc' => 'SLO'],
		['name' => 'Somalia', 'alpha2' => 'SO', 'alpha3' => 'SOM', 'ioc' => 'SOM'],
		['name' => 'Spanien', 'alpha2' => 'ES', 'alpha3' => 'ESP', 'ioc' => 'ESP'],
		['name' => 'Sri Lanka', 'alpha2' => 'LK', 'alpha3' => 'LKA', 'ioc' => 'SRI'],
		['name' => 'St. Helena', 'alpha2' => 'SH', 'alpha3' => 'SHN', 'ioc' => ''],
		['name' => 'St. Kitts und Nevis', 'alpha2' => 'KN', 'alpha3' => 'KNA', 'ioc' => 'SKN'],
		['name' => 'St. Lucia', 'alpha2' => 'LC', 'alpha3' => 'LCA', 'ioc' => 'LCA'],
		['name' => 'Saint-Pierre und Miquelon', 'alpha2' => 'PM', 'alpha3' => 'SPM', 'ioc' => ''],
		['name' => 'St. Vincent und die Grenadinen', 'alpha2' => 'VC', 'alpha3' => 'VCT', 'ioc' => 'VIN'],
		['name' => 'Südafrika', 'alpha2' => 'ZA', 'alpha3' => 'ZAF', 'ioc' => 'RSA'],
		['name' => 'Sudan', 'alpha2' => 'SD', 'alpha3' => 'SDN', 'ioc' => 'SUD'],
		['name' => 'Südgeorgien und die Südlichen Sandwichinseln', 'alpha2' => 'GS', 'alpha3' => 'SGS', 'ioc' => ''],
		['name' => 'Südsudan', 'alpha2' => 'SS', 'alpha3' => 'SSD', 'ioc' => 'SSD'],
		['name' => 'Suriname', 'alpha2' => 'SR', 'alpha3' => 'SUR', 'ioc' => 'SUR'],
		['name' => 'Svalbard und Jan Mayen', 'alpha2' => 'SJ', 'alpha3' => 'SJM', 'ioc' => ''],
		['name' => 'Swasiland', 'alpha2' => 'SZ', 'alpha3' => 'SWZ', 'ioc' => 'SWZ'],
		['name' => 'Syrien', 'alpha2' => 'SY', 'alpha3' => 'SYR', 'ioc' => 'SYR'],
		['name' => 'Tadschikistan', 'alpha2' => 'TJ', 'alpha3' => 'TJK', 'ioc' => 'TJK'],
		['name' => 'Republik China', 'alpha2' => 'TW', 'alpha3' => 'TWN', 'ioc' => 'TPE'],
		['name' => 'Tansania', 'alpha2' => 'TZ', 'alpha3' => 'TZA', 'ioc' => 'TAN'],
		['name' => 'Thailand', 'alpha2' => 'TH', 'alpha3' => 'THA', 'ioc' => 'THA'],
		['name' => 'Togo', 'alpha2' => 'TG', 'alpha3' => 'TGO', 'ioc' => 'TOG'],
		['name' => 'Tokelau', 'alpha2' => 'TK', 'alpha3' => 'TKL', 'ioc' => ''],
		['name' => 'Tonga', 'alpha2' => 'TO', 'alpha3' => 'TON', 'ioc' => 'TGA'],
		['name' => 'Trinidad und Tobago', 'alpha2' => 'TT', 'alpha3' => 'TTO', 'ioc' => 'TRI'],
		['name' => 'Tristan da Cunha', 'alpha2' => 'TA', 'alpha3' => 'TAA', 'ioc' => ''],
		['name' => 'Tschad', 'alpha2' => 'TD', 'alpha3' => 'TCD', 'ioc' => 'CHA'],
		['name' => 'Tschechien', 'alpha2' => 'CZ', 'alpha3' => 'CZE', 'ioc' => 'CZE'],
		['name' => 'Tschechoslowakei', 'alpha2' => 'CS', 'alpha3' => 'CSK', 'ioc' => 'TCH'],
		['name' => 'Tunesien', 'alpha2' => 'TN', 'alpha3' => 'TUN', 'ioc' => 'TUN'],
		['name' => 'Türkei', 'alpha2' => 'TR', 'alpha3' => 'TUR', 'ioc' => 'TUR'],
		['name' => 'Turkmenistan', 'alpha2' => 'TM', 'alpha3' => 'TKM', 'ioc' => 'TKM'],
		['name' => 'Turks- und Caicosinseln', 'alpha2' => 'TC', 'alpha3' => 'TCA', 'ioc' => ''],
		['name' => 'Tuvalu', 'alpha2' => 'TV', 'alpha3' => 'TUV', 'ioc' => 'TUV'],
		['name' => 'UdSSR', 'alpha2' => 'SU', 'alpha3' => 'SUN', 'ioc' => 'URS'],
		['name' => 'Uganda', 'alpha2' => 'UG', 'alpha3' => 'UGA', 'ioc' => 'UGA'],
		['name' => 'Ukraine', 'alpha2' => 'UA', 'alpha3' => 'UKR', 'ioc' => 'UKR'],
		['name' => 'Ungarn', 'alpha2' => 'HU', 'alpha3' => 'HUN', 'ioc' => 'HUN'],
		['name' => 'United States Minor Outlying Islands', 'alpha2' => 'UM', 'alpha3' => 'UMI', 'ioc' => ''],
		['name' => 'Uruguay', 'alpha2' => 'UY', 'alpha3' => 'URY', 'ioc' => 'URU'],
		['name' => 'Usbekistan', 'alpha2' => 'UZ', 'alpha3' => 'UZB', 'ioc' => 'UZB'],
		['name' => 'Vanuatu', 'alpha2' => 'VU', 'alpha3' => 'VUT', 'ioc' => 'VAN'],
		['name' => 'Vatikanstadt', 'alpha2' => 'VA', 'alpha3' => 'VAT', 'ioc' => ''],
		['name' => 'Venezuela', 'alpha2' => 'VE', 'alpha3' => 'VEN', 'ioc' => 'VEN'],
		['name' => 'Vereinigte Arabische Emirate', 'alpha2' => 'AE', 'alpha3' => 'ARE', 'ioc' => 'UAE'],
		['name' => 'USA', 'alpha2' => 'US', 'alpha3' => 'USA', 'ioc' => 'USA'],
		['name' => 'Großbritannien', 'alpha2' => 'GB', 'alpha3' => 'GBR', 'ioc' => 'GBR'],
		['name' => 'Vietnam', 'alpha2' => 'VN', 'alpha3' => 'VNM', 'ioc' => 'VIE'],
		['name' => 'Wallis und Futuna', 'alpha2' => 'WF', 'alpha3' => 'WLF', 'ioc' => ''],
		['name' => 'Weihnachtsinsel', 'alpha2' => 'CX', 'alpha3' => 'CXR', 'ioc' => ''],
		['name' => 'Westsahara', 'alpha2' => 'EH', 'alpha3' => 'ESH', 'ioc' => ''],
		['name' => 'Zaire', 'alpha2' => 'ZR', 'alpha3' => 'ZAR', 'ioc' => ''],
		['name' => 'Zentralafrikanische Republik', 'alpha2' => 'CF', 'alpha3' => 'CAF', 'ioc' => 'CAF'],
		['name' => 'Zypern', 'alpha2' => 'CY', 'alpha3' => 'CYP', 'ioc' => 'CYP'],
		['name' => 'Schottland', 'alpha2' => 'GB-SCT', 'alpha3' => 'SCT', 'ioc' => 'SCO'],
		['name' => 'England', 'alpha2' => 'GB-ENG', 'alpha3' => 'GBR', 'ioc' => 'ENG'],
		['name' => 'Nordirland', 'alpha2' => 'GB-NIR', 'alpha3' => 'NIR', 'ioc' => 'NIR'],
		['name' => 'Wales', 'alpha2' => 'GB-WLS', 'alpha3' => 'WLS', 'ioc' => 'WAL'],
	];
}

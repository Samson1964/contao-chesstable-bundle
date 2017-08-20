<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['TL_LANG']['XPL']['chesstable_csv'] = array
(
	array('colspan', 'Geben Sie hier die Daten für Ihre Tabelle ein. Zeilen müssen durch einen Zeilenumbruch, Spalten durch ein Semikolon getrennt sein. Alle Zeilen müssen zudem die gleiche Spaltenanzahl haben!<br><br>Um innerhalb Ihrer Tabelle eine Trennung zu erreichen, schreiben Sie in eine Zeile nur ein Tilde-Zeichen (~). Die nachfolgende Zeile wird dann als HTML-Kopfzeile interpretiert.<br><br>Die erste Zeile wird <b>immer</b> als HTML-Kopfzeile interpretiert, wobei die Spaltenwerte wichtig für die Zuordnung der Spaltenart sind. Folgende Spaltenarten gibt es:'),
	array('Spaltenköpfe für Nationsfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_nationfelder'])), 
	array('Spaltenköpfe für Platzfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_platzfelder'])), 
	array('Spaltenköpfe für Vereinsfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_vereinfelder'])), 
	array('Spaltenköpfe für Namensfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_namenfelder'])), 
	array('Spaltenköpfe für Punktefelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_punktefelder'])), 
	array('Spaltenköpfe für Wertungsfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_wertungfelder'])), 
	array('Spaltenköpfe für Ratingfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_ratingfelder'])), 
	array('Spaltenköpfe für Ergebnisfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_ergebnisfelder'])), 
	array('Spaltenköpfe für Farbfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_farbfelder'])), 
	array('Spaltenköpfe für Steuerfelder', str_replace(',', ', ', $GLOBALS['TL_CONFIG']['chesstable_steuerfelder'])), 
	array('colspan', 'Sie können darüberhinaus jeder Spalte eine weitere CSS-Klasse zuordnen:'), 
	array('{Klassenname}', 'Beispiel: 3;{2}Meier;SV Landshut<br>Die zweite Spalte bekommt die CSS-Klasse 2 mit dem Prefix own_, also "own_2". Sie können die Klasse auch hinter dem Wort "Meier" einfügen.'), 
);

$GLOBALS['TL_LANG']['XPL']['chesstable_aufsteiger'] = array
(
	array('colspan', 'Sie können den Hintergrund von Zeilen farbig markieren. Geben Sie dazu eine kommagetrennte Liste der Zeilennummern ein. Sie können dabei mehrere Zeilen mit Bindestrich getrennt zusammenfassen.<br><br>Die Standardfarbe ist grün, was aber vom Administrator mit Anpassungen am CSS überschrieben werden kann.'),
);

$GLOBALS['TL_LANG']['XPL']['chesstable_absteiger'] = array
(
	array('colspan', 'Sie können den Hintergrund von Zeilen farbig markieren. Geben Sie dazu eine kommagetrennte Liste der Zeilennummern ein. Sie können dabei mehrere Zeilen mit Bindestrich getrennt zusammenfassen.<br><br>Die Standardfarbe ist rot, was aber vom Administrator mit Anpassungen am CSS überschrieben werden kann.'),
);

$GLOBALS['TL_LANG']['XPL']['chesstable_markieren'] = array
(
	array('colspan', 'Sie können den Hintergrund von Zeilen farbig markieren. Geben Sie dazu eine kommagetrennte Liste der Zeilennummern ein. Sie können dabei mehrere Zeilen mit Bindestrich getrennt zusammenfassen.<br><br>Die Standardfarbe ist gelb, was aber vom Administrator mit Anpassungen am CSS überschrieben werden kann.'),
);

$GLOBALS['TL_LANG']['XPL']['chesstable_markieren2'] = array
(
	array('colspan', 'Bei den Einstellungen zum DSI gibt ein paar Dinge zu beachten:'),
	array('Anzahl Seiten pro Aufruf', 'Mit dieser Einstellung können Sie dem DSI sagen, wie viele Seiten er pro Aufruf abarbeiten soll. Beachten Sie: Egal was Sie einstellen, der DSI wird <strong>immer</strong> stündlich den nächsten Satz von Seiten abarbeiten.'),
	array('Neuaufbau-Frequenz', 'Diese Einstellung steht <strong>nicht</strong> in Relation zu "Anzahl Seiten pro Aufruf"! Sie bestimmt lediglich, wie häufig der DSI Ihren <strong>ganzen</strong> Suchindex neu indexieren soll. Stellen Sie hier z.B. "täglich" ein und der DSI ist noch nicht fertig mit dem Abarbeiten des vorherigen Stacks (weil z.B. 5000 Seiten vorhanden sind und Sie eingestellt haben, er soll immer nur 100 pro Aufruf nehmen [5000/100 = 50 Aufrufe = ~50h]), so wird er einfach den vorherigen Job zu Ende bringen und dann den nächsten anfangen.')
);

?>

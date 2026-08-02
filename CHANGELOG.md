# Schachtabelle Changelog

## Version 4.2.0 (2026-08-02) - mit Claude Code

* Add: Kompatibilität mit Contao 4.13 wiederhergestellt, das Bundle läuft jetzt unter Contao 4.13 und 5 mit PHP 8.1 bis 8.3
* Add: Englische Fassung des Hilfe-Popups zu den CSV-Daten -- bei englischer Backend-Sprache blieb das Popup bisher leer
* Add: Unit-Tests für die Auflösung von Zeilenbereichen, das Drehen von Namen, die eigenen CSS-Klassen und die Flaggenzuordnung (`vendor/bin/phpunit`)
* Fix: Contao-Inserttags in den Tabellenzellen wurden nie ersetzt, weil die Ersetzung erst nach dem Zusammenbau der Zelle erfolgte
* Fix: Eine Zeile mit `[TEXT]` wurde nicht als Textzeile erkannt, weil die eckigen Klammern als Angabe einer eigenen CSS-Klasse verstanden wurden
* Fix: Bei aktiver automatischer Nummerierung waren alle Spaltenarten um eine Spalte verschoben, Namen und Flaggen landeten dadurch in der falschen Spalte
* Fix: Bei aktiver automatischer Nummerierung zerstörte die zusätzliche Spalte die Trenn-, Text- und Leerzeilen
* Fix: Der Hinweis auf das benötigte Template `j_colorbox` blieb leer, weil es den verwendeten Sprachschlüssel nicht gab
* Fix: Eine Markierung ohne Farbe löschte die Farbe einer anderen Markierung derselben Zeile
* Fix: Farbmarkierungen, die im Inhaltselement gespeichert, in den Systemeinstellungen aber nicht mehr definiert sind, führten zu PHP-Warnungen
* Fix: `unserialize()` auf Werte, die keine Zeichenkette sind, durchgängig durch `StringUtil::deserialize()` ersetzt
* Fix: `explode()` auf leere Felder (`chesstable_markBold`, `chesstable_markItalic`) erzeugte unter PHP 8.1 Verfallswarnungen
* Fix: Das Widget für die Farbmarkierungen vergab zweimal dieselbe HTML-ID
* Fix: Werte im Widget und im `title`-Attribut der Nationsspalte werden maskiert, eigene CSS-Klassen aus den CSV-Daten auf Buchstaben, Ziffern, Binde- und Unterstrich begrenzt
* Fix: `symlink()`-Warnung, wenn der Server keine Symlinks erlaubt; die Tabelle zeigt dann die Länderkürzel als Text
* Change: **Überschrift, CSS-ID und Randabstände werden jetzt ausgegeben.** Die Vorlage wird vor dem Rendern gewählt, statt das fertige Template-Objekt zu ersetzen -- dabei gingen bisher alle Standardwerte des Inhaltselements verloren. Wer eine Überschrift gepflegt hat, sieht sie nach dem Update im Frontend.
* Change: **Das ungenutzte Feld `chesstable_file` wurde entfernt.** Contao schlägt beim Datenbank-Update das Löschen der Spalte `tl_content.chesstable_file` vor; das Feld war in keiner Palette und wurde nie ausgewertet.
* Change: Das Stylesheet der Flaggenbibliothek wird nur noch eingebunden, wenn die Flaggenanzeige aktiv und die Bibliothek erreichbar ist
* Change: Die Symbole des MultiColumnWizard in den Systemeinstellungen werden ohne Pfad angegeben, weil Contao 5 kein `up.svg` mehr mitliefert
* Change: Länderliste und Umformungen in die ohne Contao testbaren Klassen `Util\CountryCodes` und `Util\TableHelper` ausgelagert; die 270 Ländereinträge stehen nicht mehr im Inhaltselement, und die eine große `compile()`-Methode ist in benannte Einzelschritte zerlegt
* Change: Alle PHP-Dateien mit `declare(strict_types=1)` und durchgehenden deutschen Kommentarblöcken
* Change: `composer.json` -- PHP `^8.1`, `contao/core-bundle: ^4.13 || ^5.0`, benötigte Symfony-Komponenten ausdrücklich benannt, veraltete Entwicklungsabhängigkeiten (`doctrine/doctrine-cache-bundle`, `php-http/*`) entfernt
* Change: `services.yml` in `services.yaml` umbenannt, der wirkungslose `_instanceof`-Block wurde durch `_defaults` mit Autowiring ersetzt
* Change: `runonce_org.php` entfernt -- die Datenübernahme aus Version 3.0.0 war seit Jahren durch ihren Dateinamen abgeschaltet
* Change: Veraltete Hilfetexte zu den Feldern `chesstable_aufsteiger`, `chesstable_absteiger` und `chesstable_markieren` entfernt, die englische Sprachdatei auf den Stand der deutschen gebracht

## Version 4.1.3 (2026-07-30)

* Change: Beschreibung, Keywords und Homepage in der composer.json ergänzt, damit Packagist das Paket verständlich darstellt und über die Suche auffindbar macht

## Version 4.1.2 (2026-06-22) - mit Claude Code

* Fix: composer.json "symfony/dependency-injection" von "^6.4" auf "^6.4 || ^7.0" gelockert -> Voraussetzung für Symfony 7 (Contao ab 5.4/5.5)
* Fix: services.yml -> nicht mehr benötigten "_instanceof"-Block für "Symfony\Component\DependencyInjection\ContainerAwareInterface" entfernt -> dieses Interface existiert in Symfony 7 nicht mehr und war die eigentliche Ursache für den in 4.1.1 gesetzten Versions-Pin
* Fix: PHP-8-Warnung "foreach() argument must be of type array|object, bool given" -> unserialize() in Chesstable.php (Zeile 42) und tl_content.php (Zeile 190) mit (array) gecastet
* Fix: PHP-8-Warnung "Undefined variable $klasse" in Chesstable.php -> Variable wird jetzt in jedem Schleifendurchlauf initialisiert
* Fix: PHP-8-Warnung "Undefined array key" in Chesstable.php (Fehlerunterdrückung @ durch ?? ersetzt) und ChesstableColors.php (Zeile 67, isset()-Prüfung)

## Version 4.1.1 (2025-12-18)

* Add: composer.json "symfony/dependency-injection": "^6.4" -> wegen: In ResolveInstanceofConditionalsPass.php line 168: "Symfony\Component\DependencyInjection\ContainerAwareInterface" is set as an "instanceof" conditional, but it does not exist. Siehe auch https://community.contao.org/de/showthread.php?87239-Fehler-nach-Update-von-5-3-auf-5-4&p=587371&viewfull=1#post587371

## Version 4.1.0 (2025-12-15)

Contao-5-spezifische Fehler
* Fix: Class "Widget" not found -> aus extends \Widget wird extends \Contao\Widget
* Fix: Class "ContentElement" not found -> aus extends \ContentElement wird extends \Contao\ContentElement
* Fix: Undefined constant "ContentElements\TL_ROOT" -> aus TL_ROOT wird \System::getContainer()->getParameter('kernel.project_dir')
* Fix: "You cannot access this file directly!" aus den (Sprach-)Dateien entfernt -> nicht mehr kompatibel mit Contao 5
* Fix: Aus Ordner web wird public
* Fix: Attempted to call an undefined method named "replaceInsertTags" of class "Contao\Controller" in /src/ContentElements/Chesstable.php (line 309) -> \Contao\Controller::replaceInsertTags ersetzt durch \Contao\System::getContainer()->get('contao.insert_tag.parser')->replace
* Fix: tl_content_chesstable::jshinweis(): Argument #1 ($dc) must be of type DataContainer, Contao\DC_Table given, called in 
Sonstige Fehler:
* Fix: Warning: Undefined variable $spaltenzahl in src/ContentElements/Chesstable.php (line 220) 
* Change: Abhängigkeit auf PHP 8 und Contao 5 gesetzt

## Version 4.0.2 (2025-12-14)

* Change: tl_content -> extends \Contao\Backend statt \Backend

## Version 4.0.1 (2025-12-14)

* Change: tl_content -> extends \Backend statt Backend

## Version 4.0.0 (2025-12-14)

* Add: Kompatibilität mit Contao ^5.3

## Version 3.1.0 (2025-09-26)

* Fix: Nicht vorhandenes Hilfe-Popup bei chesstable_namendrehen entfernt
* Add: chesstable_autonumber -> automatische Nummerierung aktivieren: es wird eine Nr.-Spalte vorn hinzugefügt

## Version 3.0.8 (2024-06-07)

* Fix: unserialize(): Argument #1 ($data) must be of type string, array given in Widgets/ChesstableColors.php (line 43) 

## Version 3.0.7 (2024-05-28)

* Fix: Warning: Trying to access array offset on value of type bool in ContentElements/Chesstable.php (line 66) 
* Fix: Warning: Trying to access array offset on value of type bool in Widgets/ChesstableColors.php (line 65)

## Version 3.0.6 (2023-11-28)

* Fix: Warning: Trying to access array offset on value of type bool (wenn Farben noch nicht angelegt sind in System-Einstellungen)
* Fix: Chesstable-Klasse -> diverse Warnungen wegen nichtdefinierter Variablen
* Fix: Problem bei = in einer Ergebniszelle -> zusätzliche Leerzelle wird rechts daneben erzeugt -> Statt = stand &61; in der Datenbank
* Fix: Erwin L'Ami wird am Apostroph getrennt -> sh. vorheriges Problem -> html_entity_decode vor Verarbeitung des Strings mit explode

## Version 3.0.5 (2023-03-22)

* Add: Anpassung composer.json an PHP8

## Version 3.0.4 (2022-06-29)

* Fix: Wenn kein Land angegeben ist, wird die Zeile trotzdem farblich markiert.
* Fix: Leerzeile wird rot markiert
* Add: Leerzeile in den CSV-Daten erzeugt eine Tabellen-Leerzeile mit der Anzahl definierter Spalten 
* Fix: Fettschreibung von Brett, Platz oder Nummer nur, wenn 1. Spalte

## Version 3.0.3 (2022-03-25)

* Fix: Palette an Core (tl_content) angepaßt

## Version 3.0.2 (2022-03-01)

* Fix: IRN-Flagge wird nicht erkannt (Flagge zusätzlich in Alpha3-Code suchen, wenn in IOC-Code nicht gefunden)
* Add: Flaggen-Array um Nordirland, Wales, England und Schottland erweitert

## Version 3.0.1 (2022-02-24)

* Fix: Warning: Invalid argument supplied for foreach() - wenn tl_content.chesstable_markierungen leer ist
* Fix: Warning: Invalid argument supplied for foreach() - im Widget, wenn noch keine System-Einstellungen gesetzt sind
* Fix: Widget ChesstableColors: Wenn tl_content.chesstable_markierungen keinen key hat, darf kein Eingabefeld gezeigt werden

## Version 3.0.0 (2022-02-23)

* Change: Abhängigkeit components/flag-icon-css von ^3.3 auf ^3.5
* Add: Abhängigkeit menatwork/contao-multicolumnwizard-bundle
* Add: Widget ChesstableColors für die Eingabe der farbig zu markierenden Zeilen
* Add: runconce.php zur Übertragung der alten Tabellenfelder aus tl_content
* Add: Automatisches Markieren von Spielern nach Farbe anhand des Landes
* Add: Markierungen von Zeilen in Fett- und/oder Kursivschrift
* Add: Standard-CSS in Einstellungen aktivierbar (aus Template ausgelagert)

## Version 2.1.5 (2020-03-20)

* Fix: Feldlängen über 32 Zeichen nicht erlaubt

## Version 2.1.4 (2020-03-20)

* Change: Feldlängen für farbliche Hervorhebungen verlängert

## Version 2.1.3 (2019-09-30)

* Fix: Abhängigkeit Symfony entfernt

## Version 2.1.2 (2019-09-27)

* Fix: Abhängigkeit core-bundle auf ^4 geändert

## Version 2.1.1 (2019-09-04)

* Fix: Flagge wurde angezeigt, auch wenn nicht vorhanden

## Version 2.1.0 (2019-08-18)

* Add: Abhängigkeit zu components/flag-icon-css für die Flaggen, Löschung der eigenen Flaggen
* Fix: Im Lightbox-Modus wurden nicht alle Informationen ausgegeben
* Add: Suche nach w, s, b in Ergebnisspalten (CSS-Klasse result) und Setzen zusätzlicher CSS-Klasse white/black
* Add: Im CSV kann je Spalte eine zusätzliche CSS-Klasse gesetzt werden, z.B. [gg] - ergibt die CSS-Klasse own_gg
* Fix: Ersetzung von Contao-Inserrtags funktioniert wieder

## Version 2.0.0 * 2.0.1 (2019-08-17)

* Initialversion als Contao-4-Bundle

## Version 1.3.1 (2018-08-17)

* Fix: Anpassung CSS-Klassen an Contao 4
* Add: Ersetzung von Inserttags

## Version 1.3.0 (2016-08-20)

* Add: Absteiger/Aufsteiger/Sonstige Zeilen jetzt auch von-bis möglich (Bsp. 3-7)
* Fix: Blindfelder mit Großbuchstaben wurden nicht angezeigt. Groß- und Kleinschreibung ist jetzt egal
* Add: Hinweistext hinzugefügt, der unter der Tabelle erscheinen kann

## Version 1.2.0 (2016-07-27)

* Add: Turnierendedatum hinzugefügt

## Version 1.1.3 (2015-06-04)

* Add: Hilfefunktion beim Inhaltselement
* Add: Eigene CSS-Klasse in allen Spalten möglich

## Version 1.1.2 (2015-05-16)

* Add: Aktualisierungsdatum der Tabelle kann optional ausgegeben werden.

## Version 1.1.1 (2014-10-22)

* bei aktivierter Flaggenanzeige, die CSS-Klasse für das Landeskürzel nicht anzeigen

## Version 1.1.0 (2014-09-13)

* Wechsel zum ACE-Editor für das Feld chesstable_csv
* Feldgrößen in tl_content verkleinert, siehe https://community.contao.org/de/showthread.php?52773-Datenbank-voll-tl_content-nimmt-keine-weiteren-Felder-an

## Version 1.0.0 (2014-07-25)

Erste öffentliche Version

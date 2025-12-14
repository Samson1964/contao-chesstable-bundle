# Schachtabelle Changelog

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

* Neu: Absteiger/Aufsteiger/Sonstige Zeilen jetzt auch von-bis möglich (Bsp. 3-7)
* Fix: Blindfelder mit Großbuchstaben wurden nicht angezeigt. Groß- und Kleinschreibung ist jetzt egal
* Neu: Hinweistext hinzugefügt, der unter der Tabelle erscheinen kann

## Version 1.2.0 (2016-07-27)

* Neu: Turnierendedatum hinzugefügt

## Version 1.1.3 (2015-06-04)

* Neu: Hilfefunktion beim Inhaltselement
* Neu: Eigene CSS-Klasse in allen Spalten möglich

## Version 1.1.2 (2015-05-16)

* Neu: Aktualisierungsdatum der Tabelle kann optional ausgegeben werden.

## Version 1.1.1 (2014-10-22)

* bei aktivierter Flaggenanzeige, die CSS-Klasse für das Landeskürzel nicht anzeigen

## Version 1.1.0 (2014-09-13)

* Wechsel zum ACE-Editor für das Feld chesstable_csv
* Feldgrößen in tl_content verkleinert, siehe https://community.contao.org/de/showthread.php?52773-Datenbank-voll-tl_content-nimmt-keine-weiteren-Felder-an

## Version 1.0.0 (2014-07-25)

Erste öffentliche Version

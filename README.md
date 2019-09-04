# Schachtabelle

Schachtabelle ist ein Inhaltselement f�r Contao 4, um aus Daten im CSV-Format (mit Semikolon als Trennzeichen) eine HTML-Tabelle zu erstellen. Dabei werden die Eigenschaften einer Tabelle f�r Schachturniere beachtet, wie z.B. Blindfelder in Kreuztabellen oder Farbangaben bei Ergebnissen. Das Inhaltselement kann nat�rlich auch f�r andere Tabellen benutzt werden.

## Kurzanleitung

Nach der Installation gibt es ein neues Inhaltselement Schachtabelle im Bereich Schach. Zus�tzliche Voreinstellungen, wie z.B. die Identifikation von Spaltenarten k�nnen in den Einstellungen unter System vorgenommen werden. Bei den CSV-Daten ist darauf zu achten, das die Spaltenanzahl je Zeile immer gleich ist. Ausnahmen gibt es nur bei bestimmten Zeilenarten.

Beispiel einer Kreuztabelle:
````
Nr.;Spieler                  ;Elo ;Land;1;2;3;4;5;6;7;8;Pkt.;Pl.
1  ;GM Richard Rapport       ;2735;HUN ;x;�;�;�;�;�;�;1;4,0 ;4.
2  ;GM Leinier Dominguez     ;2760;USA ;�;x;�;�;�;�;1;1;4,5 ;1.
3  ;GM Teimur Radschabow     ;2759;AZE ;�;�;x;�;�;�;1;�;4,0 ;5.
4  ;GM Daniel Fridman        ;2638;GER ;�;�;�;x;1;0;0;0;2,5 ;8.
5  ;GM Kaido K�laots         ;2560;EST ;�;�;�;0;x;�;�;0;2,5 ;7.
6  ;GM Radoslaw Wojtaszek    ;2737;POL ;�;�;�;1;�;x;�;�;4,0 ;3.
7  ;GM Liviu Dieter Nisipeanu;2672;GER ;�;0;0;1;�;�;x;0;2,5 ;6.
8  ;GM Jan Nepomniachtchi    ;2775;RUS ;0;0;�;1;1;�;1;x;4,0 ;2.
````

## Spalten- und Zellenarten

### Blindfelder

Blindfelder in Spalten vom Typ Ergebnis (siehe System -> Einstellungen) werden z.B. mit einem x markiert. Welche Zeichen f�r Blindfelder m�glich sind, kannst Du in den Einstellungen festlegen.

### Nationsspalten

Im obigen Beispiel ist die Spalte "Land" eine Nationsspalte. Trage hier den IOC-Code des Landes ein. In den Optionen des Inhaltselements kannst Du festlegen, ob in dieser Spalte der IOC-Code als Text ausgegeben wird oder stattdessen ein Flaggen-Icon erscheint.

### Namensspalten

Im obigen Beispiel ist die Spalte "Spieler" eine Namensspalte. Du kannst die Namen auch mit Komma trennen, z.B. "Rapport,Richard,GM" und im Inhaltselement einstellen, die Namen zu drehen. Dann wird daraus "GM Richard Rapport".

### Ergebnisspalten

Tauchen innerhalb von Ergebnisspalten die Zeichen b, s oder w auf, wird der Zelle eine CSS-Klasse "white" oder "black" hinzugef�gt.

## Besondere Angaben

### Inserttags

Contao-Inserttags http://de.contaowiki.org/Insert-Tags sind in jeder Zelle m�glich. Sie werden erst am Schlu� ersetzt, nachdem alle tabellenspezifischen Umstellungen erledigt sind.

### Eigene CSS-Klassen

Je Zelle kann eine eigene CSS-Klasse zugeordnet werden:

Beispiel anhand obiger Tabelle:
````
Nr.;Spieler                  ;Elo ;Land;1;2;3;4;5;6;7;8;Pkt.;Pl.
1  ;[klasse]GM Richard Rapport       ;2735;HUN ;x;�;�;�;�;�;�;1;4,0 ;4.
2  ;GM Leinier Dominguez     ;2760;USA ;�;x;�;�;�;�;1;1;4,5 ;1.
````
Bei Richard Rapport wurde eine zus�tzliche CSS-Klasse angegeben. Die Zelle bekommt jetzt zus�tzlich "own_klasse" als CSS-Klasse.

### Sonderzeilen

````
Nr.;Spieler                  ;Elo ;Land;1;2;3;4;5;6;7;8;Pkt.;Pl.
1  ;GM Richard Rapport       ;2735;HUN ;x;�;�;�;�;�;�;1;4,0 ;4.
2  ;GM Leinier Dominguez     ;2760;USA ;�;x;�;�;�;�;1;1;4,5 ;1.
3  ;GM Teimur Radschabow     ;2759;AZE ;�;�;x;�;�;�;1;�;4,0 ;5.
4  ;GM Daniel Fridman        ;2638;GER ;�;�;�;x;1;0;0;0;2,5 ;8.
5  ;GM Kaido K�laots         ;2560;EST ;�;�;�;0;x;�;�;0;2,5 ;7.
6  ;GM Radoslaw Wojtaszek    ;2737;POL ;�;�;�;1;�;x;�;�;4,0 ;3.
7  ;GM Liviu Dieter Nisipeanu;2672;GER ;�;0;0;1;�;�;x;0;2,5 ;6.
8  ;GM Jan Nepomniachtchi    ;2775;RUS ;0;0;�;1;1;�;1;x;4,0 ;2.
~
Nr.;Spieler                  ;Elo ;Land;1;2;3;4;5;6;7;8;Pkt.;Pl.
1  ;GM Richard Rapport       ;2735;HUN ;x;�;�;�;�;�;�;1;4,0 ;4.
2  ;GM Leinier Dominguez     ;2760;USA ;�;x;�;�;�;�;1;1;4,5 ;1.
3  ;GM Teimur Radschabow     ;2759;AZE ;�;�;x;�;�;�;1;�;4,0 ;5.
4  ;GM Daniel Fridman        ;2638;GER ;�;�;�;x;1;0;0;0;2,5 ;8.
5  ;GM Kaido K�laots         ;2560;EST ;�;�;�;0;x;�;�;0;2,5 ;7.
6  ;GM Radoslaw Wojtaszek    ;2737;POL ;�;�;�;1;�;x;�;�;4,0 ;3.
7  ;GM Liviu Dieter Nisipeanu;2672;GER ;�;0;0;1;�;�;x;0;2,5 ;6.
8  ;GM Jan Nepomniachtchi    ;2775;RUS ;0;0;�;1;1;�;1;x;4,0 ;2.
[TEXT];Das ist ein Text
````
Mehrere Tabellen k�nnen mit einem ~ optisch getrennt werden. So eine Zeile hat die Eigenschaften einer Kopfzeile.
Au�erdem kann in einer eigenen Zeile ein Hinweistext angezeigt werden. Dazu bekommt die Zeile nur zwei Spalten:
````
[TEXT];Das ist ein Text
````

**Frank Hoppe**

# T-PRO

Die Skripte dienen der Generierung einer nutzerfreundlichen Oberfläche für den Thesaurus der Provenienzbegriffe (T-PRO). Vgl. dazu http://provenienz.gbv.de

Die Masterdatei ist [T-PRO.ttl](T-PRO.ttl). Das Skript [displayHTML.php](displayHTML.php) erzeugt eine HTML-Ausgabe. Zugleich werden die RDF-Daten nach NTriples und JSON exportiert sowie unter T-PRO_export.ttl neu abgespeichert. Das Skript [displayMediaWiki.php](displayMediaWiki.php) erzeugt eine Ausgabe in Markup für MediaWiki. Hierauf beruht die Darstellung unter http://provenienz.gbv.de/Thesaurus

Das Skript [transform.pl](transform.pl) exportiert die Daten aus [T-PRO.ttl](T-PRO.ttl) nach RDF/XML ([T-PRO.xml](T-PRO.xml)) mithilfe des Skripts [rdf2rdf-1.0.1-2.3.1.jar](rdf2rdf-1.0.1-2.3.1.jar). Die erzeugten XML-Daten werden mit regulären Ausdrücken nachbearbeitet.
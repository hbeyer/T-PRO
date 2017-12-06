#!/usr/bin/perl
use strict;
use warnings;
use utf8;
use XML::LibXML;

# Prüfen, ob die notwendigen Dateien vorhanden sind
open TEST1, "<", "rdf2rdf-1.0.1-2.3.1.jar" or die "Fehler beim Öffnen der Datei: $!";
close TEST1;
open TEST2, "<", "T-PRO.ttl" or die "Fehler beim Öffnen der Datei: $!";
close TEST2;

# Umwandeln des TTL-Dokuments in ein RDF/XML-Dokument, abspeichern als temporäre Datei
system "rdf2rdf-1.0.1-2.3.1.jar T-PRO.ttl temporaryRDF.rdf";

# Laden des Inhalts der temporären Datei
local $/= undef;
open FILE, "temporaryRDF.rdf" or die "Fehler beim Öffnen der Datei: $!";
my $string = <FILE>;
close FILE;

# Vereinfachen des RDF/XML-Dokuments mit regulären Ausdrücken
my $findConcept = '<rdf:Description rdf:about="([^"]+)">\s+<rdf:type rdf:resource="[^#]+#Concept"/>([^~]+?)</rdf:Description>';
$string =~ s/$findConcept/<skos:Concept rdf:about=\"$1\">$2<\/skos:Concept>/g;

my $findCollection = '<rdf:Description rdf:about="([^"]+)">\s+<rdf:type rdf:resource="[^#]+#Collection"/>([^~]+?)</rdf:Description>';
$string =~ s/$findCollection/<skos:Collection rdf:about=\"$1\">$2<\/skos:Collection>/g;

my $findScheme = '<rdf:Description rdf:about="([^"]+)">\s+<rdf:type rdf:resource="[^#]+#ConceptScheme"/>([^~]+?)</rdf:Description>';
$string =~ s/$findScheme/<skos:ConceptScheme rdf:about=\"$1\">$2<\/skos:ConceptScheme>/g;

# Überprüfen der XML-Syntax des RDF/XML-Dokuments
my $parser   = XML::LibXML->new();
my $xml_text = eval { $parser->parse_string($string); };
if ($@) {
	die ("Das Ergebnis ist nicht wohlgeformt: ".$@);
}

# Abspeichern des Dokuments unter einem neuen Namen
open SAVERESULT, ">", "T-PRO.xml";
print SAVERESULT $string;
close SAVERESULT;

# Löschen der temporären Datei
unlink "temporaryRDF.rdf";

print "Die Datei T-PRO.ttl wurde umgewandelt in T-PRO.xml.";
= T-PRO Thesaurus der Provenienzbegriffe =
Stand: <?php echo date('d.m.Y'); ?>


Hier entsteht eine Repräsentation des T-PRO als Linked Open Data in RDF-SKOS
Die Vorlage finden Sie unter [[T-PRO_Thesaurus_der_Provenienzbegriffe]]

Downlad der RDF-Daten: [http://hartmut-beyer.bplaced.net/thesaurus/T-PRO.xml RDF-XML], [http://hartmut-beyer.bplaced.net/thesaurus/T-PRO.ttl Turtle], [http://hartmut-beyer.bplaced.net/thesaurus/T-PRO.nt NTriples], [http://hartmut-beyer.bplaced.net/thesaurus/T-PRO.json JSON]



<?php

$path = 'T-PRO.ttl';
$format = 'turtle';

require __DIR__ .'/vendor/autoload.php';
include('class_skosConcept.php');
include('class_skosLink.php');

$data = file_get_contents($path);
$graph = new EasyRdf_Graph();
EasyRdf_Namespace::set('tpro', 'http://provenienz.gbv.de/thesaurus#');
$graph->parse($data, $format);

$serialiser = new EasyRdf_Serialiser_Turtle;
$turtle = $serialiser->serialise($graph, 'turtle');
file_put_contents('T-PRO_export.ttl', $turtle);

/* $serialiserX = new EasyRdf_Serialiser_RdfXml;
$rdfxml = $serialiserX->serialise($graph, 'rdfxml');
file_put_contents('T-PRO.xml', $rdfxml); */

$serialiserN = new EasyRdf_Serialiser_Ntriples;
$ntriples = $serialiserN->serialise($graph, 'ntriples');
file_put_contents('T-PRO.nt', $ntriples);

$serialiserJ = new EasyRdf_Serialiser_Json;
$json = $serialiserJ->serialise($graph, 'json');
file_put_contents('T-PRO.json', $json);

unset($serialiser, $serialiserX, $serialiserN, $serialiserJ, $turtle, $ntriples, $rdfxml, $json);

$resourcesAll = $graph->resourcesMatching('skos:prefLabel');
$resources = array();
foreach ($resourcesAll as $resource) {
	if ($resource->getUri() != 'http://provenienz.gbv.de/thesaurus#') {
		$resources[] = $resource;
	}
}
unset($resourcesAll);

$collection = array();
foreach ($resources as $resource) {
	$collection[] = new skosConcept($resource);
}

unset($resources);

function sortByLabel($a, $b) {
	$return = strcmp($a->prefLabel, $b->prefLabel);
	return($return);
}
usort($collection, 'sortByLabel');

foreach($collection as $concept) {
	$concept->displayMediaWiki();
}

?>
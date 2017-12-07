<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="UTF-8" />
		<title>T-PRO</title>
		<!-- <script type="text/javascript" src=""></script> -->
	</head>
	<body>
	
	<h1>T-PRO Thesaurus der Provenienzbegriffe</h1>
	
    <p>Stand: <?php echo date('d.m.Y'); ?></p>
    <p>Der Thesaurus steht in folgenden Notationen zum Download bereit:
    <ul>
        <li><a href="T-PRO.xml">RDF-XML</a></li>
        <li><a href="T-PRO.ttl">Turtle</a></li>
        <li><a href="T-PRO.nt">N-Triples</a></li>
        <li><a href="T-PRO.json">JSON</a></li>
    </ul>    
    </p>
    <hr />

	<?php
	
	$path = 'T-PRO.ttl';
	$format = 'turtle';
	
	require __DIR__ .'/vendor/autoload.php';
	include('class_skosConcept.php');
	include('class_skosCollection.php');
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
		$concept->displayHTML();
	}
	
	?>
	
	</body>
</html>

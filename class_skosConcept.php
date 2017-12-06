<?php

class skosConcept {
	
	public $url;
	public $ancor;
	public $prefLabel;
	public $altLabels = array();
	public $prefLabelEn;
	public $altLabelsEn = array();
	public $prefLabelFr;
	public $altLabelsFr = array();
	public $definition;
	public $broader;
	public $narrower = array();
	public $closeMatch = array();
	public $partOfCollections = array();
	
	function __construct(EasyRdf_Resource $resource) {
		$this->url = $resource->getUri();
		$this->ancor = substr($resource->shorten(), 5);
		$this->prefLabel = $resource->getLiteral('skos:prefLabel', 'de')->__toString();
		if ($resource->getLiteral('skos:prefLabel', 'en')) {
			$this->prefLabelEn = $resource->getLiteral('skos:prefLabel', 'en')->__toString();
		}
		if ($resource->getLiteral('skos:prefLabel', 'fr')) {
			$this->prefLabelFr = $resource->getLiteral('skos:prefLabel', 'fr')->__toString();
		}		
		foreach ($resource->allLiterals('skos:altLabel', 'de') as $literal) {
			$this->altLabels[] = $literal->__toString();
		}
		foreach ($resource->allLiterals('skos:altLabel', 'en') as $literal) {
			$this->altLabelsEn[] = $literal->__toString();
		}
		foreach ($resource->allLiterals('skos:altLabel', 'fr') as $literal) {
			$this->altLabelsFr[] = $literal->__toString();
		}
		if ($resource->getLiteral('skos:definition', 'de')) {
			$this->definition = $resource->getLiteral('skos:definition', 'de')->__toString();
		}
		if ($resource->getResource('skos:broader')) {
			$this->broader = new skosLink($resource->getResource('skos:broader'));
		}
		if ($resource->allResources('skos:narrower') != null) {
			foreach ($resource->allResources('skos:narrower') as $narrower) {
				$this->narrower[] = new skosLink($narrower);
			}
		}
		if ($resource->allResources('skos:closeMatch') != null) {
			foreach ($resource->allResources('skos:closeMatch') as $closeMatch) {
				$this->closeMatch[] = new skosLink($closeMatch);
			}
		}
		$graph = $resource->getGraph();
		$refering = $graph->resourcesMatching('skos:member', $resource);
		foreach ($refering as $resourceRef) {
			$literal = $resourceRef->getLiteral('skos:hiddenLabel', 'de');
			$label = $literal->__toString();
			$this->partOfCollections[$label] = array();
			$members = $resourceRef->allResources('skos:member');
			foreach ($members as $member) {
				if ($this->url != $member->getUri()) {
					$this->partOfCollections[$label][] = new skosLink($member);
				}
			}
		}
		
	}
	
	public function displayMediaWiki() {
		
		if ($this->prefLabelEn) {
			array_unshift($this->altLabelsEn, $this->prefLabelEn);
			$this->altLabelsEn = array_map('addItalicsMediaWiki', $this->altLabelsEn);
		}
		if ($this->prefLabelFr) {
			array_unshift($this->altLabelsFr, $this->prefLabelFr);
			$this->altLabelsFr = array_map('addItalicsMediaWiki', $this->altLabelsFr);
		}
		$english = implode(', ', $this->altLabelsEn);
		if ($english) {
			$english .= ' (en)';
		}
		$french = implode(', ', $this->altLabelsFr);
		if ($french) {
			$french .= ' (fr)';
		}
		$foreign = $english;
		if ($english and $french) {
			$foreign .= ', ';
		}
		$foreign .= $french;
		
		$narrower = array();
		foreach ($this->narrower as $skosLink) {
			$narrower[] = $skosLink->makeLinkMediaWiki();
		}
		
		$closeMatch = array();
		foreach ($this->closeMatch as $skosLink) {
			$closeMatch[] = $skosLink->makeLinkMediaWiki();
		}
		
		$partOfCollections = array();
		foreach ($this->partOfCollections as $key => $members) {
			$stringCollections = 'In Kombination mit ';
			foreach ($members as $member) {
				$stringCollections .= $member->makeLinkMediaWiki().", ";
			}
			$stringCollections = substr($stringCollections, 0, -2);
			$stringCollections .= ' statt &bdquo;'.$key.'&rdquo;';
			$partOfCollections[] = $stringCollections;
		}
		$partOfCollections = implode('<br />', $partOfCollections);
		
		include('skosConceptWiki.phtml');
		
	}	
	
	public function displayHTML() {
		
		if ($this->prefLabelEn) {
			array_unshift($this->altLabelsEn, $this->prefLabelEn);
			$this->altLabelsEn = array_map('addItalics', $this->altLabelsEn);
		}
		if ($this->prefLabelFr) {
			array_unshift($this->altLabelsFr, $this->prefLabelFr);
			$this->altLabelsFr = array_map('addItalics', $this->altLabelsFr);
		}
		$english = implode(', ', $this->altLabelsEn);
		if ($english) {
			$english .= ' (en)';
		}
		$french = implode(', ', $this->altLabelsFr);
		if ($french) {
			$french .= ' (fr)';
		}
		$foreign = $english;
		if ($english and $french) {
			$foreign .= ', ';
		}
		$foreign .= $french;
		
		$narrower = array();
		foreach ($this->narrower as $skosLink) {
			$narrower[] = $skosLink->makeLink();
		}
		
		$closeMatch = array();
		foreach ($this->closeMatch as $skosLink) {
			$closeMatch[] = $skosLink->makeLink();
		}
		
		$partOfCollections = array();
		foreach ($this->partOfCollections as $key => $members) {
			$partOfCollections[$key] = '';
			foreach ($members as $member) {
				$partOfCollections[$key] .= $member->makeLink().', ';
			}
			$partOfCollections[$key] = substr($partOfCollections[$key], 0, -2);
		}
		
		include('skosConcept.phtml');
		
	}
	
}

function addItalics($string) {
	$string = '<i>'.$string.'</i>';
	return($string);
}

function addItalicsMediaWiki($string) {
	$string = "''".$string."''";
	return($string);
}

?>

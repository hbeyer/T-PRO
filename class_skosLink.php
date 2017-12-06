<?php

class skosLink {
	
	public $ancor;
	public $label;
	
	function __construct(EasyRdf_Resource $resource) {
		$this->ancor = substr($resource->shorten(), 5);
		$literal = $resource->getLiteral('skos:prefLabel', 'de');
		if (get_class($literal) == 'EasyRdf_Literal') {
			$this->label = $literal->__toString();
		}
		elseif ($resource->getResource('rdf:type')->shorten() == 'skos:Collection') { 
			$literal = $resource->getLiteral('skos:hiddenLabel', 'de');
			$this->label = $literal->__toString();
		}
		else {
			$this->label = "*Resource nicht auffindbar*";
		}
		
	}
	public function makeLink() {
		$link = '<a href="#'.$this->ancor.'">'.$this->label.'</a>';
		return($link);
	}
	
	public function makeLinkMediaWiki() {
		$link ="[[#".$this->ancor."|".$this->label."]]";
		return($link);
	}	
	
}

?>

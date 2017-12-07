<?php 

class skosCollection {

	public $url;
	public $ancor;
	public $labelsDe = array();
	public $labelsEn = array();
	public $labelsFr = array();
	public $definition;
	
	function __construct($resource) {
		$this->url = $resource->getUri();
		$this->ancor = substr($resource->shorten(), 5);		
		$literalsDe = $resource->allLiterals('skos:hiddenLabel', 'de');
		foreach ($literalsDe as $literal) {
			$this->labelsDe[] = $literal->__toString();
		}
		$literalsEn = $resource->allLiterals('skos:hiddenLabel', 'en');
		foreach ($literalsEn as $literal) {
			$this->labelsEn[] = $literal->__toString();
		}
		$literalsFr = $resource->allLiterals('skos:hiddenLabel', 'fr');
		foreach ($literalsFr as $literal) {
			$this->labelsFr[] = $literal->__toString();
		}
		if ($resource->getLiteral('skos:definition', 'de')) {
			$this->definition = $resource->getLiteral('skos:definition', 'de')->__toString();
		}		
	}
	
	public function makeSyntheticLabel() {
		$labelsDe = array_map('addQuotations', $this->labelsDe);
		$label = implode(', ', $labelsDe);
		if ($this->labelsEn) {
			$labelsEn = array_map('addItalics', $this->labelsEn); 
			$labelsEn = implode(', ', $labelsEn);
			if ($labelsEn) {
				$label .= ', '.$labelsEn.' (en)';
			}
		}
		if ($this->labelsFr) {
			$labelsFr = array_map('addItalics', $this->labelsFr); 
			$labelsFr = implode(', ', $labelsFr);
			if ($labelsFr) {
				$label .= ', '.$labelsFr.' (fr)';
			}			
		}
		return($label);
	}

}

?>
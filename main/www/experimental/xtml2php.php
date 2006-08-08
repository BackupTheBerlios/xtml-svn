<?php
	class XTML2PHP
	{
		private $document;

		public function __construct($template)
		{
			$this->document = new DOMDocument();
			$this->document->load($template);
		}
		
		public function _transform($element)
		{
			while ($element)
			{
				switch ($element->nodeType)
				{
					case XML_ELEMENT_NODE:
					{
						if ($element->tagName !== "")
						{
							print "Element=" . $element->tagName . "<br>\n";
							$tag = $element->tagName . "_tag";
							$tag($element);
							//$this->_transform($element->firstChild);
						}
					}
					break;
				}
				
				$element = $element->nextSibling;
			}
		}
		
		public function transform()
		{
			print "<PRE>";
			$element = $this->document->documentElement;
			$this->_transform($element);	
		}
	}
	
	$xtml2php = new XTML2PHP("occupancy.xml");
	$xtml2php->transform();
?>
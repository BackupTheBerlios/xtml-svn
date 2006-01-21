<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, by Classes Are Code.
	 * Released under the GNU GPL v2
	 */

	require_once("PistolTableSupport.class.php");
	
	/**
	 *
	 */
	class cTag
		extends PistolTag
	{
		private $tables;
		private $tablesIndex;
		private $iftable;
		private $testmode;
		
		function cTag($pistol)
		{
			parent::PistolTag($pistol);
			
			$this->tables = array();
			$this->tablesIndex = 0;
			$this->testmode = false;
			
			$this->iftable = array(
				"!=" => "ifneq",
				"==" => "ifeq",
				">" => "ifgt",
				">=" => "ifgte",
				"<" => "iflt",
				"<=" => "iflte");
		}

		/**
		 * 
		 */
		function copyright()
		{
			return "Core - The PiSToL Core Tag Library\n" .
				"Copyright 2005, 2006, by Classes Are Code.\n" .
				"Released under the GNU GPL v2\n" .
				"http://pistol.classesarecode.net/"
				;
		}

		/**
		 * 
		 */
		function enableTestMode()
		{
			$this->testmode = true;
		}
		
		/**
		 * 
		 */
		function getTable()
		{
			return $this->tables[$this->tablesIndex-1];
		}
		
		/**
		 * 
		 */
		function ifneq($lvalue, $rvalue)
		{
			return $lvalue != $rvalue;
		}
		
		/**
		 * 
		 */
		function ifeq($lvalue, $rvalue)
		{
			return $lvalue == $rvalue;
		}
		
		/**
		 * 
		 */
		function tag_set($element)
		{
			$var = $element->getAttribute("var");
			$value = $this->pistol->_getValueOrAttribute($element);

			$this->pistol->setVar($var, $value);
			
			return "";
		}

		/**
		 * 
		 */
		function tag_array($element)
		{
			$a = array();
			$child = $element->firstChild;
						
			while ($child)
			{
				switch ($child->nodeType)
				{
					case XML_ELEMENT_NODE:
					{
						$a[] = $this->pistol->processElement($child, true);
					}
					
					default:
					{
						// ignore all other node types
					}
				}

				$child = $child->nextSibling;
			}
			
			return $a;
		}

		/**
		 * 
		 */
		function tag_object($element)
		{
			$object = new stdClass();
			$i = 0;
			
			while ($attr = $element->attributes->item($i++))
			{
				$member = $attr->nodeName;
				$object->$member = $attr->nodeValue;
			}
			
			return $object;
		}

		/**
		 * 
		 */
		function tag_testmode($element)
		{
			if ($this->testmode)
			{
				return $this->pistol->process($element->firstChild);
			}
			
			return "";
		}

		/**
		 * 
		 */
		function tag_ifset($element)
		{
			$var = $element->getAttribute("var");
			
			if ($this->pistol->hasData($var))
			{
				return $this->pistol->process($element->firstChild);
			}
			
			return "";
		}

		/**
		 * 
		 */
		function tag_if($element)
		{
			$method = $this->iftable[$element->getAttribute("op")];

			if ($this->$method(
				$this->pistol->getVar($element->getAttribute("lvalue")), 
				$this->pistol->getVar($element->getAttribute("rvalue"))))
			{
				return $this->pistol->process($element->firstChild);
			}
			
			return "";
		}

		/**
		 * 
		 */
		function tag_table($element)
		{
			$output = "";
			$this->tables[$this->tablesIndex++] = new PistolTableSupport();
			$table = $this->getTable();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$table->setRowClasses(explode(",", $rowClasses));
			}
			
			$output .= $this->pistol->_totext($element);
			$output .= $this->pistol->process($element->firstChild);
			$output .= "</table>";
			
			unset($this->tables[--$this->tablesIndex]);
			
			return $output;
		}
		
		/**
		 * 
		 */
		function tag_tr($element)
		{
			$explicitClassSpecified = false;
			$output = "";
			$table = $this->getTable();
			
			if ($table)
			{
				$table->setColumnCount(0);
				
				$colClasses = $element->getAttribute("col-classes");
				
				if ($colClasses)
				{
					$element->removeAttribute("col-classes");
					$row = $table->getRow();
					$row->setColClasses(explode(",", $colClasses));
				}
				
				if ($rowClasses = $table->getRowClasses())
				{
					$index = ($table->getRowCount() % count($rowClasses));
					
					if ($element->getAttribute("class") == "")
					{
						$element->setAttribute("class", $rowClasses[$index]);
					}
					else
					{
						$explicitClassSpecified = true;
					}
				}
			}
			
			$output .= $this->pistol->_totext($element);
			$output .= $this->pistol->process($element->firstChild);
			$output .= "</tr>";
			
			if ($colClasses)
			{
				$element->setAttribute("col-classes", $colClasses);
			}
			
			if ($table && !$explicitClassSpecified)
			{
				$table->incrementRowCount();
			}
			
			if (!$explicitClassSpecified)
			{
				$element->removeAttribute("class");
			}

			return $output;
		}
		
		/**
		 * 
		 */
		function tag_td($element)
		{
			$output = "";
			$table = $this->getTable();
			$row = null;
			
			if ($table && $row = $table->getRow())
			{
				if ($row && $colClasses = $row->getColClasses())
				{
					$index = ($row->getColCount() % count($colClasses));
					if ($colClasses[$index] != "*")
					{
						$element->setAttribute("class", $colClasses[$index]);
					}
				}
			}
			
			$output .= $this->pistol->_totext($element);
			$output .= $this->pistol->process($element->firstChild);
			$output .= "</td>";
			
			if ($row)
			{
				$row->incrementColCount();
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function tag_loop($element)
		{
			$output = "";
			$data = $element->getAttribute("value");
			$var = $element->getAttribute("var");

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$' && $this->pistol->hasData($data))
			{
				$data = $this->pistol->getVar($data);
			}

			if (is_array($data))
			{
				$firstChild = $element->firstChild;
				$previousValue = $this->pistol->getVar($var);
				
				foreach ($data as $tmp)
				{
					$this->pistol->setVar($var, $tmp);

					$output .= $this->pistol->process($firstChild);
				}

				$this->pistol->setVar($var, $previousValue);
			}
			
			return $output;
		}

		/**
		 * 
		 */
		function tag_redirect($element)
		{
			$to = $value = $this->pistol->_getValueOrAttribute($element, "to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function tag_out($element)
		{
			$value = $this->pistol->_getValueOrAttribute($element);
		
			if ($value{0} == '$')
			{
				return $this->pistol->getVar($value);
			}
			else
			{
				return $value;
			}
		}
	}
?>
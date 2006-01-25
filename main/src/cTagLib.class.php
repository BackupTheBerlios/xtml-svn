<?
	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PiSToL - PHP Standard Tag Library
	 * Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).
	 * Released under the GNU GPL v2
	 */

	require_once("PistolTableSupport.class.php");
	
	/**
	 *
	 */
	class cTagLib
		extends PistolTag
	{
		private $tables;
		private $tablesIndex;
		private $iftable;
		
		/**
		 * @ignore
		 */
		function cTagLib($pistol)
		{
			parent::PistolTag($pistol);
			
			$this->tables = array();
			$this->tablesIndex = 0;
			
			$this->iftable = array(
				"!=" => "ifneq",
				"==" => "ifeq",
				">" => "ifgt",
				">=" => "ifgte",
				"<" => "iflt",
				"<=" => "iflte");
		}

		/**
		 * @ignore
		 */
		function copyright()
		{
			return "Core - The PiSToL Core Tag Library\n" .
				"Copyright 2005, 2006 by John Allen and others (see AUTHORS file for additional info).\n" .
				"Released under the GNU GPL v2\n" .
				"http://pistol.classesarecode.net/"
				;
		}

		/**
		 * @ignore
		 */
		function getTable()
		{
			return $this->tables[$this->tablesIndex-1];
		}
		
		/**
		 * @ignore
		 */
		function ifneq($lvalue, $rvalue)
		{
			return $lvalue != $rvalue;
		}
		
		/**
		 * @ignore
		 */
		function ifeq($lvalue, $rvalue)
		{
			return $lvalue == $rvalue;
		}
		
		/**
		 * 
		 */
		function c_colon_include($element)
		{
			$file = $this->pistol->_getAttributeOrBody($element, "file");
			$pistol = new Pistol($file); 
			
			return $pistol->doinclude();
		}
		
		/**
		 * 
		 */
		function c_colon_set($element)
		{
			$var = $element->getAttribute("var");
			$value = $this->pistol->_getAttributeOrBody($element);

			$this->pistol->setVar($var, $value);
			
			return "";
		}

		/**
		 * 
		 */
		function c_colon_array($element)
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
		function c_colon_object($element)
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
		function c_colon_preview($element)
		{
			if ($this->pistol->isPreviewModeEnabled())
			{
				return $this->pistol->process($element->firstChild);
			}
			else
			{
				return $this->pistol->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_ifset($element)
		{
			$var = $element->getAttribute("var");
			
			if ($this->pistol->hasData($var))
			{
				return $this->pistol->process($element->firstChild);
			}
			else
			{
				return $this->pistol->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_if($element)
		{
			$method = $this->iftable[$element->getAttribute("op")];

			if ($this->$method(
				$this->pistol->getVar($element->getAttribute("lvalue")), 
				$this->pistol->getVar($element->getAttribute("rvalue"))))
			{
				return $this->pistol->process($element->firstChild);
			}
			else
			{
				return $this->pistol->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_table($element)
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
		function c_colon_tr($element)
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
		function c_colon_td($element)
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
		function c_colon_loop($element)
		{
			$output = "";
			$data = $element->getAttribute("value");
			$var = $element->getAttribute("var");
			$limit = $element->getAttribute("limit");
			$index = 0;

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$' && $this->pistol->hasData($data))
			{
				$data = $this->pistol->getVar($data);
			}

			$firstChild = $element->firstChild;
			$previousValue = $this->pistol->getVar($var);

			if (is_array($data))
			{
				// array support for loops			
				foreach ($data as $tmp)
				{
					$this->pistol->setVar($var, $tmp);
					
					if ($limit && $index++ == $limit)
					{
						break;
					}
					
					$output .= $this->pistol->process($firstChild);
				}
			}
			else if (gettype($data) == "resource")
			{
				$type = get_resource_type($data);
				
				if ($type == "mysql result")
				{
					// MySQL result resource support for loops			
					while ($tmp = mysql_fetch_object($data))
					{
						if ($limit && $index++ == $limit)
						{
							break;
						}
					
						$this->pistol->setVar($var, $tmp);
						$output .= $this->pistol->process($firstChild);
					}
				}
				else if ($type == "pgsql result")
				{
					// PostgreSQL result resource support for loops			
					while ($tmp = pg_fetch_object($data))
					{
						if ($limit && $index++ == $limit)
						{
							break;
						}
					
						$this->pistol->setVar($var, $tmp);
						$output .= $this->pistol->process($firstChild);
					}
				}
			}

			$this->pistol->setVar($var, $previousValue);
			 			
			return $output;
		}

		/**
		 * 
		 */
		function c_colon_redirect($element)
		{
			$to = $value = $this->pistol->_getAttributeOrBody($element, "to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function c_colon_out($element)
		{
			$value = $this->pistol->_getAttributeOrBody($element);
		
			if ($value{0} == '$')
			{
				return $this->pistol->getVar($value);
			}
			else
			{
				return $value;
			}
		}

		/**
		 * 
		 */
		function c_colon_string($element)
		{
			return $this->c_colon_out($element);
		}
	}
?>

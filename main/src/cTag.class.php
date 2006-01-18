<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * PSTL - The PHP Standard Tag Library
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */

	require_once("PSTLTableSupport.class.php");
	
	/**
	 *
	 */
	class cTag
		extends PSTLTag
	{
		var $tables;
		var $tablesIndex;
		var $iftable;
		
		function cTag($pstl)
		{
			parent::PSTLTag($pstl);
			
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
		 * 
		 */
		function copyright()
		{
			return "PSTL Core - The Core PHP Standard Tag Library\n" .
				"Copyright 2005, 2006, the dublinux.net group.\n" .
				"Released under the GNU GPL v2\n" .
				"http://pstl.dublinux.net/"
				;
		}

		/**
		 * 
		 */
		function getTable()
		{
			return $this->tables[$this->tablesIndex-1];
		}
		
		function ifneq($lvalue, $rvalue)
		{
			return $lvalue != $rvalue;
		}
		
		/**
		 * 
		 */
		function tag_set($element)
		{
			$var = $element->getAttribute("var");
			$value = $this->pstl->_getvalue($element);

			$this->pstl->setVar($var, $value);						
			
			return "";
		}

		/**
		 * 
		 */
		function tag_if($element)
		{
			$method = $this->iftable[$element->getAttribute("op")];

			if ($this->$method(
				$this->pstl->getVar($element->getAttribute("lvalue")), 
				$this->pstl->getVar($element->getAttribute("rvalue"))))
			{
				return $this->pstl->process($element->firstChild);
			}
			
			return "";
		}

		/**
		 * 
		 */
		function tag_table($element)
		{
			$output = "";
			$this->tables[$this->tablesIndex++] = new PSTLTableSupport();
			$table = $this->getTable();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$table->setRowClasses(explode(",", $rowClasses));
			}
			
			$output .= $this->pstl->_totext($element);
			$output .= $this->pstl->process($element->firstChild);
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
			
			$output .= $this->pstl->_totext($element);
			$output .= $this->pstl->process($element->firstChild);
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
			
			$output .= $this->pstl->_totext($element);
			$output .= $this->pstl->process($element->firstChild);
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
			$data = $element->getAttribute("data");
			$var = $element->getAttribute("var");

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$' && $this->pstl->hasData($data))
			{
				$data = $this->pstl->getVar($data);
			}

			if (is_array($data))
			{
				$firstChild = $element->firstChild;
				$previousValue = $this->pstl->getVar($var);
				
				foreach ($data as $tmp)
				{
					$this->pstl->setVar($var, $tmp);

					$output .= $this->pstl->process($firstChild);
				}

				$this->pstl->setVar($var, $previousValue);
			}
			
			return $output;
		}

		/**
		 * 
		 */
		function tag_redirect($element)
		{
			$to = $value = $this->pstl->_getvalue($element, "to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function tag_out($element)
		{
			$value = $this->pstl->_getvalue($element);
		
			if ($value{0} == '$')
			{
				return $this->pstl->getVar($value);
			}
			else
			{
				return $value;
			}
		}
	}
?>
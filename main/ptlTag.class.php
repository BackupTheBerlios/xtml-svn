<?
	/**
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * Copyright 2005, 2006, the dublinux.net group.
	 * Released under the GNU GPL v2
	 */
	
	/**
	 * 
	 */
	class ptlTable
	{
		var $rowClasses;
		var $rowCount;
		var $row;
		
		/**
		 * 
		 */
		function ptlTable()
		{
			$this->rowClasses = null;
			$this->rowCount = 0;
			$this->row = new ptlRow();
		}
		
		/**
		 * 
		 */
		function getRow()
		{
			return $this->row;
		}
		
		/**
		 * 
		 */
		function setColumnCount($count = 0)
		{
			$this->row->setColumnCount($count);
		}
		
		/**
		 * 
		 */
		function setRowClasses($classes)
		{
			$this->rowClasses = $classes;
		}
		
		/**
		 * 
		 */
		function getRowClasses()
		{
			return $this->rowClasses; 
		}
		
		/**
		 * 
		 */
		function incrementRowCount()
		{
			$this->rowCount++;
		}

		/**
		 * 
		 */
		function getRowCount()
		{
			return $this->rowCount;
		}
	}
	
	/**
	 * 
	 */
	class ptlRow
	{
		var $colClasses;
		var $colCount;
		
		/**
		 * 
		 */
		function ptlRow()
		{
			$this->colClasses = null;
			$this->colCount = 0;
		}
		
		/**
		 * 
		 */
		function setColumnCount($count = 0)
		{
			$this->colCount = 0;
		}
		
		/**
		 * 
		 */
		function setColClasses($classes)
		{
			$this->colClasses = $classes;
		}
		
		/**
		 * 
		 */
		function getColClasses()
		{
			return $this->colClasses; 
		}
		
		/**
		 * 
		 */
		function incrementColCount()
		{
			$this->colCount++;
		}

		/**
		 * 
		 */
		function getColCount()
		{
			return $this->colCount;
		}
	}
	
	/**
	 *
	 */
	class ptlTag
	{
		var $tables;
		var $tablesIndex;
		
		function ptlTag()
		{
			$this->tables = array();
			$this->tablesIndex = 0;
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
		function tag_translate($engine, $element)
		{
			// TODO: implement translate logic
			$engine->append($element->getAttribute("en"));
		}

		/**
		 * 
		 */
		function tag_if($engine, $element)
		{
			$var = $element->getAttribute("var");
			$eq = $element->getAttribute("eq");
			$neq = $element->getAttribute("neq");

			if (isset($eq) && $engine->getData($var) == $var)
			{
				$engine->process($element->firstChild);
			}
			else if (isset($neq) && $engine->getData($var) != $var)
			{
				$engine->process($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function tag_table($engine, $element)
		{
			$this->tables[$this->tablesIndex++] = new ptlTable();
			$table = $this->getTable();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$table->setRowClasses(explode(",", $rowClasses));
			}
			
			$engine->append($element);
			$engine->process($element->firstChild);
			$engine->append("</table>");
			
			unset($this->tables[--$this->tablesIndex]);
		}
		
		/**
		 * 
		 */
		function tag_tr($engine, $element)
		{
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
					$element->setAttribute("class", $rowClasses[$index]);
				}
			}
			
			$engine->append($element);
			$engine->process($element->firstChild);
			$engine->append("</tr>");
			
			if ($table)
			{
				$table->incrementRowCount();
			}
		}
		
		/**
		 * 
		 */
		function tag_td($engine, $element)
		{
			$table = $this->getTable();
			$row = null;
			
			if ($table && $row = $table->getRow())
			{
				if ($row && $colClasses = $row->getColClasses())
				{
					$index = ($row->getColCount() % count($colClasses));
					$element->setAttribute("class", $colClasses[$index]);
				}
			}
			
			$engine->append($element);
			$engine->process($element->firstChild);
			$engine->append("</td>");
			
			if ($row)
			{
				$row->incrementColCount();
			}
		}
		
		/**
		 * 
		 */
		function tag_loop($engine, $element)
		{
			$data = $element->getAttribute("data");
			$var = $element->getAttribute("var");

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '%' && $engine->hasData($data))
			{
				$data = $engine->getData($data);
			}

			if (is_array($data))
			{
				foreach ($data as $tmp)
				{
					$engine->setData("%" . $var, $tmp);
					$engine->process($element->firstChild);
					$engine->unsetData("%" . $var);
				}
			}
		}

		/**
		 * 
		 */
		function tag_redirect($engine, $element)
		{
			$to = $element->getAttribute("to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function tag_out($engine, $element)
		{
			$value = $element->getAttribute("value");
		
			if ($value{0} == '%')
			{
				$engine->append($engine->getData($value));
			}
			else
			{
				$engine->append("$value");
			}
			
			$engine->process($element->firstChild);
		}
	}
?>

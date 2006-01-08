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
		extends tagImpl
	{
		var $lang;
		var $tables;
		var $tablesIndex;
		
		function ptlTag($engine)
		{
			parent::tagImpl($engine);
			// default language to en (English)
			
			$this->lang = isset($_REQUEST['lang']) ? $_REQUEST['lang']:"en";
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
		function getlang()
		{
			return $this->lang;
		}
		
		/**
		 * 
		 */
		function setlang($lang)
		{
			$this->lang = $lang;
		}
		
		/**
		 * 
		 */
		function tag_setlang($element)
		{
			$lang = $this->engine->getData($element->getAttribute("en"));
			$this->setLang($lang);
		}
		
		/**
		 * 
		 */
		function tag_getlang($element)
		{
			$this->engine->append($this->getlang());
		}
		
		/**
		 * 
		 */
		function tag_translate($element)
		{
			// TODO: implement translate logic
			
			$text = $this->engine->getData($element->getAttribute("en"));
			$this->engine->append($text);
		}

		/**
		 * 
		 */
		function tag_if($element)
		{
			$var = $element->getAttribute("var");
			$eq = $element->getAttribute("eq");
			$neq = $element->getAttribute("neq");

			if (isset($eq) && $this->engine->getData($var) == $var)
			{
				$this->engine->process($element->firstChild);
			}
			else if (isset($neq) && $this->engine->getData($var) != $var)
			{
				$this->engine->process($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function tag_table($element)
		{
			$this->tables[$this->tablesIndex++] = new ptlTable();
			$table = $this->getTable();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$table->setRowClasses(explode(",", $rowClasses));
			}
			
			$this->engine->append($element);
			$this->engine->process($element->firstChild);
			$this->engine->append("</table>");
			
			unset($this->tables[--$this->tablesIndex]);
		}
		
		/**
		 * 
		 */
		function tag_tr($element)
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
			
			$this->engine->append($element);
			$this->engine->process($element->firstChild);
			$this->engine->append("</tr>");
			
			if ($table)
			{
				$table->incrementRowCount();
			}
		}
		
		/**
		 * 
		 */
		function tag_td($element)
		{
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
			
			$this->engine->append($element);
			$this->engine->process($element->firstChild);
			$this->engine->append("</td>");
			
			if ($row)
			{
				$row->incrementColCount();
			}
		}
		
		/**
		 * 
		 */
		function tag_loop($element)
		{
			$data = $element->getAttribute("data");
			$var = $element->getAttribute("var");

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '%' && $this->engine->hasData($data))
			{
				$data = $this->engine->getData($data);
			}

			if (is_array($data))
			{
				foreach ($data as $tmp)
				{
					$this->engine->setData("%" . $var, $tmp);
					$this->engine->process($element->firstChild);
					$this->engine->unsetData("%" . $var);
				}
			}
		}

		/**
		 * 
		 */
		function tag_redirect($element)
		{
			$to = $element->getAttribute("to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function tag_out($element)
		{
			$value = $element->getAttribute("var");
		
			if ($value{0} == '%')
			{
				$this->engine->append($this->engine->getData($value));
			}
			else
			{
				$this->engine->append("$value");
			}
		}
	}
?>

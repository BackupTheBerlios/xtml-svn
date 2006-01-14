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
	class pstlTag
		extends tagBase
	{
		var $tables;
		var $tablesIndex;
		
		function pstlTag($pstl)
		{
			parent::tagBase($pstl);
			
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
		function tag_if($element)
		{
			$var = $element->getAttribute("var");
			$eq = $element->getAttribute("eq");
			$neq = $element->getAttribute("neq");

			if (isset($eq) && $this->pstl->getData($var) == $var)
			{
				$this->pstl->process($element->firstChild);
			}
			else if (isset($neq) && $this->pstl->getData($var) != $var)
			{
				$this->pstl->process($element->firstChild);
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
			
			$this->pstl->append($element);
			$this->pstl->process($element->firstChild);
			$this->pstl->append("</table>");
			
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
			
			$this->pstl->append($element);
			$this->pstl->process($element->firstChild);
			$this->pstl->append("</tr>");
			
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
			
			$this->pstl->append($element);
			$this->pstl->process($element->firstChild);
			$this->pstl->append("</td>");
			
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
			else if ($data{0} == '$' && $this->pstl->hasData($data))
			{
				$data = $this->pstl->getData($data);
			}

			if (is_array($data))
			{
				foreach ($data as $tmp)
				{
					$this->pstl->setData($var, $tmp);
					$this->pstl->process($element->firstChild);
					$this->pstl->unsetData($var);
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
		
			if ($value{0} == '$')
			{
				$this->pstl->append($this->pstl->getData($value));
			}
			else
			{
				$this->pstl->append("$value");
			}
		}
	}
?>
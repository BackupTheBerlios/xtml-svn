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
	class TableHelper
	{
		var $rowClasses;
		var $rowCount;
		
		/**
		 * 
		 */
		function TableHelper()
		{
			$this->rowClasses = null;
			$this->rowCount = 0;
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
	class ptlTag
	{
		var $tableHelpers;
		var $tableHelpersIndex;
		
		function ptlTag()
		{
			$this->tableHelpers = array();
			$this->tableHelpersIndex = 0;
		}

		/**
		 * 
		 */
		function getTableHelper()
		{
			return $this->tableHelpers[$this->tableHelpersIndex-1];
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
			$this->tableHelpers[$this->tableHelpersIndex++] = new TableHelper();
			$tableHelper = $this->getTableHelper();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$tableHelper->setRowClasses(explode(",", $rowClasses));
			}
			
			$engine->append($element);
			$engine->process($element->firstChild);
			$engine->append("</table>");
			
			unset($this->tableHelpers[--$this->tableHelpersIndex]);
		}
		
		/**
		 * 
		 */
		function tag_tr($engine, $element)
		{
			$tableHelper = $this->getTableHelper();
			
			if ($tableHelper)
			{
				if ($rowClasses = $tableHelper->getRowClasses())
				{
					$index = ($tableHelper->getRowCount() % count($rowClasses));
					$element->setAttribute("class", $rowClasses[$index]);
				}
			}
			
			$engine->append($element);
			$engine->process($element->firstChild);
			$engine->append("</tr>");
			
			if ($tableHelper)
			{
				$tableHelper->incrementRowCount();
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

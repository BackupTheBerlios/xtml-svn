<?php
	/*
	 * $Author$
	 * $LastChangedDate$
	 * $LastChangedRevision$
	 * $LastChangedBy$
	 * $HeadURL$
	 * 
	 * XTML - eXtensible Tag Markup Language
	 * 
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU Lesser General Public
	 * License as published by the Free Software Foundation; either
	 * version 2.1 of the License, or (at your option) any later version.
	 *
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License 
	 * along with this library; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 *
	 * You may contact the authors of XTML by e-mail at:
	 * developers@classesarecode.net
	 *
	 * The latest version of XTML can be obtained from:
	 * http://developer.berlios.de/projects/xtml/
	 *
	 * @link http://developer.berlios.de/projects/xtml/
	 * @copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 * Added branch for 0.99
	 * 
	 */

	require_once("XTMLTableSupport.class.php");
	
	/**
	 *
	 */
	class cTagLib
		extends XTMLTag
	{
		private $tables;
		private $tablesIndex;
		private $cacheId;
		
		/**
		 * @ignore
		 */
		function cTagLib($xtml)
		{
			parent::XTMLTag($xtml);
			
			$this->tables = array();
			$this->tablesIndex = 0;
			$this->cacheId = 0;
		}

		/**
		 * @ignore
		 */
		function copyright()
		{
			return "Core - The XTML Core Tag Library\n" .
				"Copyright 2005, 2006 by The Classes Are Code Group (see AUTHORS file for contributor list).\n" .
				"Released under the GNU GPL v2\n" .
				"http://xtml.classesarecode.net/"
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
		 * 
		 */
		function _getElementPath($element, $path)
		{
			if ($element->parentNode != null)
			{
				return $this->_getElementPath($element->parentNode, "/" . $element->tagName . $path); 
			}
			
			return $element->tagName . $path; 
		}

		/**
		 * 
		 */
		function getElementPath($element)
		{
			return $this->_getElementPath($element, "");
		}
		
		/**
		 * This tag omit's all content enclosed in it
		 */
		function c_colon_omit($element)
		{
			return "";
		}
		
		/**
		 * 
		 */
		function c_colon_cache($element)
		{
			$this->cacheId++;
			
			if ($this->xtml->isCacheEnabled())
			{
				// check cache
				$cacheDir = $this->xtml->getCacheDir() . "/" . md5($this->xtml->getScriptPath());
				$cacheFileName = $cacheDir . "/" . $this->cacheId;
				$ttl = $element->getAttribute("ttl");
				$params = $element->getAttribute("params");
				
				if ($params)
				{
					$params = explode(",", $element->getAttribute("params"));
					
					if (count($params) > 0)
					{
						$tmp = "";
	
						foreach ($params as $param)
						{
							$tmp .= $_REQUEST[$param];
						}
						
						$cacheFileName .= "-" . md5($tmp);
					}
				}
								
				$siCacheFile = @stat($cacheFileName);
				$siXML = @stat($this->xtml->getDocument());

				if ($siCacheFile !== FALSE &&
					$siXML !== FALSE &&
					$siCacheFile['mtime'] > $siXML['mtime'] &&					
					$siCacheFile['mtime'] + $ttl > time())
				{
					$content = "<!-- cached -->". file_get_contents($cacheFileName);
				}
				else
				{
					// create content
					$content = $this->xtml->process($element->firstChild);
					
					if (!($cacheFile = @fopen($cacheFileName, "w")))
					{
						mkdir($cacheDir);
						$cacheFile = @fopen($cacheFileName, "w");
					}
					
					if ($cacheFile)
					{
						fwrite($cacheFile, $content);
						fclose($cacheFile);
					}
				}
			}
			else
			{
				$content = $this->xtml->process($element->firstChild);
			}
			
			return $content;
		}
		
		/**
		 * 
		 */
		function c_colon_include($element)
		{
			$file = $this->xtml->_getAttributeOrBody($element, "file");
			$attrfile = $element->getAttribute("file");
			
			if ($file == null || $file == "")
			{
				print "<pre>Include file($attrfile) evaluates to null</pre>"; die();
			}
			
			$xtml = new XTMLProcessor($file, null, $this->xtml); 
			
			return $xtml->doinclude();
		}
		
		/**
		 * 
		 */
		function c_colon_set($element)
		{
			$var = $element->getAttribute("var");
			$value = $this->xtml->_getAttributeOrBody($element, "value", XTFLAG_DISCARD_WS_TEXT_NODES | XTFLAG_EVALUATE);

			$this->xtml->getDataModel()->set($var, $value);
			
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
						$a[] = $this->xtml->processElement($child, XTFLAG_DISCARD_WS_TEXT_NODES | XTFLAG_EVALUATE);
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
			if ($this->xtml->isPreviewModeEnabled())
			{
				return $this->xtml->process($element->firstChild);
			}
			else
			{
				return $this->xtml->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_ifset($element)
		{
			$var = $element->getAttribute("var");
			
			if ($this->xtml->getDataModel()->notNull($var))
			{
				return $this->xtml->process($element->firstChild);
			}
			else
			{
				return $this->xtml->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_if($element)
		{
			$result = $this->xtml->_evaluateExpression($element->getAttribute("test"));

			if ($result)
			{
				return $this->xtml->process($element->firstChild);
			}
			else
			{
				return $this->xtml->processElse($element->firstChild);
			}
		}

		/**
		 * 
		 */
		function c_colon_table($element)
		{
			$output = "";
			$this->tables[$this->tablesIndex++] = new XTMLTableSupport();
			$table = $this->getTable();
			
			$rowClasses = $element->getAttribute("row-classes");
			
			if ($rowClasses)
			{
				$element->removeAttribute("row-classes");
				$table->setRowClasses(explode(",", $rowClasses));
			}
			
			$output .= $this->xtml->_totext($element);
			$output .= $this->xtml->process($element->firstChild);
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
			
			$output .= $this->xtml->_totext($element);
			$output .= $this->xtml->process($element->firstChild);
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
			
			$output .= $this->xtml->_totext($element);
			$output .= $this->xtml->process($element->firstChild);
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
		function c_colon_foreach($element)
		{
			$output = "";
			$data = $element->getAttribute("value");
			$asname = $element->getAttribute("as");
			$limit = $element->getAttribute("limit");
			$count = 0;
		
			if (!$asname)
			{
				$asname = '@';
			}

			if ($data{0} == '(')
			{
				$_code = "\$data = array" . $data . ";";
				eval($_code);
			}
			else if ($data{0} == '$')
			{
				$data = $this->xtml->evaluate($data);
			}

			//print "<pre>" . $element->getAttribute("value") . "=" . gettype($data) . "(" . $data . ")";

			$firstChild = $element->firstChild;

			if (is_array($data))
			{
				// foreach support for arrays
				reset($data);
				
				while (list($key, $tmp) = each($data))			
				{
					if ($limit && $count++ == $limit)
					{
						break;
					}
					
					$this->xtml->getDataModel()->push($asname, $tmp);
					$this->xtml->getDataModel()->push("#$asname", $key);
					$output .= $this->xtml->process($firstChild);
					$this->xtml->getDataModel()->pop("#$asname");
					$this->xtml->getDataModel()->pop($asname);
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
						if ($limit && $count++ == $limit)
						{
							break;
						}
					
						$this->xtml->getDataModel()->push($asname, $tmp);
						$output .= $this->xtml->process($firstChild);
						$this->xtml->getDataModel()->pop($asname);
					}
				}
				else if ($type == "pgsql result")
				{
					// PostgreSQL result resource support for loops			
					while ($tmp = pg_fetch_object($data))
					{
						if ($limit && $count++ == $limit)
						{
							break;
						}
					
						$this->xtml->getDataModel()->push($asname, $tmp);
						$output .= $this->xtml->process($firstChild);
						$this->xtml->getDataModel()->pop($asname);
					}
				}
			}

			return $output;
		}

		/**
		 * 
		 */
		function c_colon_redirect($element)
		{
			$to = $value = $this->xtml->_getAttributeOrBody($element, "to");
			header("Location: $to");
		}
		
		/**
		 * 
		 */
		function c_colon_out($element)
		{
			$value = $this->xtml->_getAttributeOrBody($element);
			return $this->xtml->evaluate($value);
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

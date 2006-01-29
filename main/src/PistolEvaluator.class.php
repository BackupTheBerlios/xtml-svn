<?php
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

	ini_set('include_path', ".:" . ini_get('include_path'));
	require_once("Pistol.class.php");
	
	/**
	 * 
	 */
	class PistolEvaluator 
	{
		/**
		 * 
		 */
		private $pistol;
		
		/**
		 * 
		 */
		private $expression;
		
		/**
		 * 
		 */
		private $expressionLen;
		
		/**
		 * 
		 */
		private $pos;
		
		/**
		 * 
		 */
	    function PistolEvaluator($pistol, $expression) 
	    {
	    	$this->pistol = $pistol;
    	}
    	
    	/**
    	 * 
    	 */
    	function getToken()
    	{
    		$tok = "";

    		while ($this->pos < $this->expressionLen &&
    			$this->expression{$this->pos} == ' ')
    		{
    			$this->pos++;
    		}
    		
    		if ($this->pos >= $this->expressionLen)
    		{
    			return $tok;
    		}
    		
    		switch ($this->expression{$this->pos})
    		{
    			case '(':
    				$this->pos++;
    				return "(";
    			break;
    			
    			case ')':
    				$this->pos++;
    				return ")";
    			break;
    			
    			case '[':
    				$this->pos++;
    				return "[";
    			break;
    			
    			case ']':
    				$this->pos++;
    				return "]";
    			break;
    			
    			case '.':
    				$this->pos++;
    				return ".";
    			break;
    			
    			case '=':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return "==";
    					break;
    				}
    				
    				return "==";
    			break;
    			
    			case '&':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '&':
    						$this->pos++;
    						return "&&";
    					break;
    				}
    				
    				return "&";
    			break;
    			
    			case '!':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return "!=";
    					break;
    				}
    				
    				return "!";
    			break;
    			
    			case '>':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return ">=";
    					break;
    				}
    				
    				return ">";
    			break;
    			
    			case '<':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return "<=";
    					break;
    				}
    				
    				return "<";
    			break;
    			
    			case "'":
    				$this->pos++;
    				
		    		while ($this->expression{$this->pos} != "'")
		    		{
		    			if ($this->expression{$this->pos} == '\\')
		    			{
		    				$tok .= $this->expression{$this->pos++};
		    			}
		    			
		    			$tok .= $this->expression{$this->pos++};
		    		}

    				$this->pos++;
    			break;
    			
    			case '"':
    				$this->pos++;
    				
		    		while ($this->expression{$this->pos} != '"')
		    		{
		    			if ($this->expression{$this->pos} == '\\')
		    			{
		    				$tok .= $this->expression{$this->pos++};
		    			}
		    			
		    			$tok .= $this->expression{$this->pos++};
		    		}

    				$this->pos++;
    			break;
    			
    			default:
		    		while ($this->pos < $this->expressionLen && 
		    			strstr(" !%^&|?\"'()[]-+~.", $this->expression{$this->pos}) == false)
		    		{
		    			$tok .= $this->expression{$this->pos++};
		    		}
		    	break;
    		}
    		
    		return $tok;
    	}
    	
    	/**
    	 * 
    	 */
    	function _evaluate()
    	{
    		while ($tok = $this->getToken())
    		{
    			print "$tok\n";
    		}
    	}

    	/**
    	 * 
    	 */
    	function evaluate($expression)
    	{
	    	$this->pos = 0;
	    	$this->expression = $expression;
	    	$this->expressionLen = strlen($this->expression);

    		return $this->_evaluate();
    	}
	}
	
	print "Starting\n";
	$p = new Pistol();
	$e = new PistolEvaluator($p);
	$e->evaluate("a > 10 && a < 20");
	$e->evaluate("product.description['short']");
?>
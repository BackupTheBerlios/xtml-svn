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
	
	$tokid = 0;
	define('TOK_EMPTY', $tokid++);
	define('TOK_IDENT', $tokid++);
	define('TOK_NUMBER', $tokid++);
	define('TOK_LPAREN', $tokid++);
	define('TOK_RPAREN', $tokid++);
	define('TOK_DOT', $tokid++);
	define('TOK_LT', $tokid++);
	define('TOK_LTE', $tokid++);
	define('TOK_GT', $tokid++);
	define('TOK_GTE', $tokid++);
	define('TOK_EQ', $tokid++);
	define('TOK_NEQ', $tokid++);
	define('TOK_NOT', $tokid++);
	define('TOK_AND', $tokid++);
	define('TOK_OR', $tokid++);
	define('TOK_BITAND', $tokid++);
	define('TOK_BITOR', $tokid++);
	define('TOK_COMPLEMENT', $tokid++);
	define('TOK_LBRACKET', $tokid++);
	define('TOK_RBRACKET', $tokid++);
	define('TOK_STRING', $tokid++);
	
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
    			return array(TOK_EMPTY);
    		}
    		
    		switch ($this->expression{$this->pos})
    		{
    			case '(':
    				$this->pos++;
    				return array(TOK_LPAREN);
    			break;
    			
    			case ')':
    				$this->pos++;
    				return array(TOK_RPAREN);
    			break;
    			
    			case '[':
    				$this->pos++;
    				return array(TOK_LBRACKET);
    			break;
    			
    			case ']':
    				$this->pos++;
    				return array(TOK_RBRACKET);
    			break;
    			
    			case '.':
    				$this->pos++;
    				return array(TOK_DOT);
    			break;
    			
    			case '=':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return array(TOK_EQ);
    					break;
    				}
    				
    				return array(TOK_EQ);
    			break;
    			
    			case '&':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '&':
    						$this->pos++;
    						return array(TOK_AND);
    					break;
    				}
    				
    				return array(TOK_BITAND);
    			break;
    			
    			case '!':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return array(TOK_NEQ);
    					break;
    				}
    				
    				return array(TOK_NOT);
    			break;
    			
    			case '>':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return array(TOK_GTE);
    					break;
    				}
    				
    				return array(TOK_GT);
    			break;
    			
    			case '<':
    				$this->pos++;
    				
    				switch ($this->expression{$this->pos})
    				{
    					case '=':
    						$this->pos++;
    						return array(TOK_LTE);
    					break;
    				}
    				
    				return array(TOK_LT);
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
    				
    				return array(TOK_STRING, $tok);
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
    				
    				return array(TOK_STRING, $tok);
    			break;
    			
    			default:
    				if (strstr("0123456789", $this->expression{$this->pos}) == true)
    				{
			    		while ($this->pos < $this->expressionLen && 
			    			strstr("0123456789.", $this->expression{$this->pos}) == true)
			    		{
			    			$tok .= $this->expression{$this->pos++};
			    		}
			    		
			    		return array(TOK_NUMBER, $tok);
    				}
    				else
    				{
			    		while ($this->pos < $this->expressionLen && 
			    			strstr(" !%^&|?\"'()[]-+~.", $this->expression{$this->pos}) == false)
			    		{
			    			$tok .= $this->expression{$this->pos++};
			    		}
			    		
			    		return array(TOK_IDENT, $tok);
    				}
		    	break;
    		}
    		
    		return array(TOK_EMPTY);
    	}
    	
    	/**
    	 * 
    	 */
    	function _evaluate()
    	{
    		while ($tok = $this->getToken())
    		{
    			print "$tok[0]\n";
    			
    			if ($tok[0] == TOK_EMPTY)
    			{
    				break;
    			}
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
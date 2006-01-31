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
	class Token
	{
		public $type;
		public $value;
		
		public function Token($type, $value=null)
		{
			$this->type = $type;
			$this->value = $value;
		}
	}
	
	/**
	 * 
	 */
	class XTMLExpressionEvaluator 
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
	    function XTMLExpressionEvaluator($pistol) 
	    {
	    	$this->pistol = $pistol;
    	}
    	
    	/**
    	 * 
    	 */
    	function getToken()
    	{
    		while ($this->pos < $this->expressionLen &&
    			$this->expression{$this->pos} == ' ')
    		{
    			$this->pos++;
    		}
    		
    		if ($this->pos >= $this->expressionLen)
    		{
    			return new Token(TOK_EMPTY);
    		}

    		switch ($this->expression{$this->pos})
    		{
    			case '(':
    				$this->pos++;
    				return new Token(TOK_LPAREN);
    			break;
    			
    			case ')':
    				$this->pos++;
    				return new Token(TOK_RPAREN);
    			break;
    			
    			case '[':
    				$this->pos++;
    				return new Token(TOK_LBRACKET);
    			break;
    			
    			case ']':
    				$this->pos++;
    				return new Token(TOK_RBRACKET);
    			break;
    			
    			case '.':
    				$this->pos++;
    				return new Token(TOK_DOT);
    			break;
    			
    			case '=':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return new Token(TOK_EQ);
    				}
    				
    				return new Token(TOK_EQ);
    			break;
    			
    			case '&':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == "&")
    				{
  						$this->pos++;
   						return new Token(TOK_AND);
    				}
    				    				
    				return new Token(TOK_BITAND);
    			break;
    			
    			case '!':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return new Token(TOK_NEQ);
    				}
    				
    				return new Token(TOK_NOT);
    			break;
    			
    			case '>':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return new Token(TOK_GTE);
    				}
    				
    				return new Token(TOK_GT);
    			break;
    			
    			case '<':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return new Token(TOK_LTE);
    				}
    				
    				return new Token(TOK_LT);
    			break;
    			
    			case "'":
    			case "\"":
    				$quot = $this->expression{$this->pos};
    				$this->pos++;
    				
		    		while (($c = $this->expression{$this->pos}) != $quot)
		    		{
		    			if ($c == '\\')
		    			{
		    				$tok .= $c; 
		    				$c = $this->expression{$this->pos++};
		    			}
		    			
		    			$tok .= $c;
		    		}

    				$this->pos++;
    				
    				return new Token(TOK_STRING, $tok);
    			break;
    			
    			default:
		    		$tok = "";
    				$c = $this->expression{$this->pos};
    				
    				if (($c >= '0' && $c <= '9'))
    				{
			    		while ($this->pos < $this->expressionLen &&
			    			($c >= '0' && $c <= '9') || $c == '.')
			    		{
			    			$tok .= $this->expression{$this->pos++};
			    			
			    			if ($this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
			    		
			    		return new Token(TOK_NUMBER, $tok);
    				}
    				else
    				{
			    		while ($this->pos < $this->expressionLen && 
			    			$c != ' ')
			    		{
			    			$tok .= $this->expression{$this->pos++};

			    			if ($this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
			    		
			    		return new Token(TOK_IDENT, $tok);
    				}
    		}
    		
    		return new Token(TOK_EMPTY);
    	}
    	
    	/**
    	 * 
    	 */
    	function _evaluate()
    	{
    		while ($tok = $this->getToken())
    		{
    			//print "$tok[0]\n";
    			
    			if ($tok->type == TOK_EMPTY)
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
	$e = new XTMLExpressionEvaluator($p);
	
	$started = microtime(true);
	$iterations = 10000;
	$count = 0;

	$started = microtime(true);
	$count = 0;

	for ($i=0; $i < $iterations; $i++)
	{
		$e->evaluate("a > 10 && a < 20");
		$count++;

		//$e->evaluate("product.description['short']");
		//$count++;
	}

	$finished = microtime(true);
	$renderTime = ($finished - $started) * 1000;
	$perIterationRenderTime = (($finished - $started) * 1000) / $count;

	print "Tokenising $count iterations took " . sprintf("%0.2f", $renderTime) . "ms, " . sprintf("%0.2f", $perIterationRenderTime) . "ms per iteration\n";
?>
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
		public $value;
		
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
    			return TOK_EMPTY;
    		}

    		switch ($this->expression{$this->pos})
    		{
    			case '(':
    				$this->pos++;
    				return TOK_LPAREN;
    			break;
    			
    			case ')':
    				$this->pos++;
    				return TOK_RPAREN;
    			break;
    			
    			case '[':
    				$this->pos++;
    				return TOK_LBRACKET;
    			break;
    			
    			case ']':
    				$this->pos++;
    				return TOK_RBRACKET;
    			break;
    			
    			case '.':
    				$this->pos++;
    				return TOK_DOT;
    			break;
    			
    			case '=':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return TOK_EQ;
    				}
    				
    				return TOK_EQ;
    			break;
    			
    			case '&':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == "&")
    				{
  						$this->pos++;
   						return TOK_AND;
    				}
    				    				
    				return TOK_BITAND;
    			break;
    			
    			case '!':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return TOK_NEQ;
    				}
    				
    				return TOK_NOT;
    			break;
    			
    			case '>':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return TOK_GTE;
    				}
    				
    				return TOK_GT;
    			break;
    			
    			case '<':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == '=')
    				{
   						$this->pos++;
   						return TOK_LTE;
    				}
    				
    				return TOK_LT;
    			break;
    			
    			case "'":
    			case "\"":
    				$this->value = "";
    				$quot = $this->expression{$this->pos};
    				$this->pos++;
    				
		    		while (($c = $this->expression{$this->pos}) != $quot)
		    		{
		    			if ($c == '\\')
		    			{
		    				$this->value .= $c; 
		    				$c = $this->expression{$this->pos++};
		    			}
		    			
		    			$this->value .= $c;
		    		}

    				$this->pos++;
    				
    				return TOK_STRING;
    			break;
    			
    			default:
		    		$this->value = "";
    				$c = $this->expression{$this->pos};
    				
    				if (($c >= '0' && $c <= '9'))
    				{
			    		while ($this->pos < $this->expressionLen &&
			    			($c >= '0' && $c <= '9') || $c == '.')
			    		{
			    			$this->value .= $this->expression{$this->pos++};
			    			
			    			if ($this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
			    		
			    		return TOK_NUMBER;
    				}
    				else
    				{
			    		while ($this->pos < $this->expressionLen && 
			    			$c != ' ')
			    		{
			    			$this->value .= $this->expression{$this->pos++};

			    			if ($this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
			    		
			    		return TOK_IDENT;
    				}
    		}
    		
    		return TOK_EMPTY;
    	}
    	
    	/**
    	 * 
    	 */
    	function _evaluate()
    	{
    		while ($tok = $this->getToken())
    		{
    			//print "$tok[0]\n";
    			
    			if ($tok == TOK_EMPTY)
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
	/*
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
	*/
	
	print "Starting\n";
	
	$started = microtime(true);
	$iterations = 1000000;
	$count = 0;

	$started = microtime(true);
	$count = 0;

	for ($i=0; $i < $iterations; $i++)
	{
		$tokens = token_get_all("<? a > 10 && a < 20 && s=='CPN'?>");

		foreach ($tokens as $tok)
		{
			if (is_string($tok))
			{
				print "$tok\n";
			}
			else
			{
				list($id, $value) = $tok;
				print token_name($id) . " -- $value\n";
			}
		}
		
		$count++;
	}

	$finished = microtime(true);
	$renderTime = ($finished - $started) * 1000;
	$perIterationRenderTime = (($finished - $started) * 1000) / $count;
	
	print "Tokenising $count iterations took " . sprintf("%0.2f", $renderTime) . "ms, " . sprintf("%0.2f", $perIterationRenderTime) . "ms per iteration\n";
?>

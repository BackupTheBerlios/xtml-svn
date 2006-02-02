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
	 * @copyright 2005, 2006 by John Allen and others (see AUTHORS file for contributor list).
	 * @author John Allen <john.allen@classesarecode.net>
	 * @version 0.99
	 * 
	 */

	ini_set('include_path', ".:" . ini_get('include_path'));
	require_once("XTMLProcessor.class.php");
	
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
		private $xtml;
		
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
	    function XTMLExpressionEvaluator($xtml) 
	    {
	    	$this->xtml = $xtml;
    	}
    	
    	/**
    	 * 
    	 */
    	private function getToken()
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
    	private function expected($tokens, $found)
    	{
    		die("Expected %, found $found");
    	}
    	
    	/**
    	 * 
    	 */
    	private function expect($tokens)
    	{
    		$tok = $this->getToken();
    		
    		if (!isset($tokens[$tok]))
    		{
    			$this->expected($tokens, $tok);
    		}
    		
    		return $tok;
    	}
    	
    	/**
    	 * 
    	 */
    	private function _evaluate_ident($name)
    	{
    		$value = "";

    		while (($tok = $this->expect(
    			array(
    				TOK_IDENT => TOK_IDENT, 
    				TOK_LPAREN => TOK_DOT, 
    				TOK_RPAREN => TOK_RPAREN, 
    				TOK_EMPTY => TOK_EMPTY))) != TOK_EMPTY)
    		{
    			switch ($tok)
    			{
    				case TOK_IDENT:
    				{
    				}
    				break;
    				
    				case TOK_LPAREN:
    				{
    					$this->_evaluate_group();
    				}
    				break;
    				
    				case TOK_RPAREN:
    				{
    					// evaluation finished.
    				}
    				break;
    			}
    		}
    	}

    	/**
    	 * 
    	 */
    	private function _evaluate_group()
    	{
    		$value = "";

    		while (($tok = $this->expect(
    			array(
    				TOK_IDENT => TOK_IDENT, 
    				TOK_LPAREN => TOK_LPAREN, 
    				TOK_RPAREN => TOK_RPAREN, 
    				TOK_EMPTY => TOK_EMPTY))) != TOK_EMPTY)
    		{
    			switch ($tok)
    			{
    				case TOK_IDENT:
    				{
    				}
    				break;
    				
    				case TOK_LPAREN:
    				{
    					$this->_evaluate_group();
    				}
    				break;
    				
    				case TOK_RPAREN:
    				{
    					// evaluation finished.
    				}
    				break;
    			}
    		}
    	}

    	/**
    	 * 
    	 */
    	private function _evaluate()
    	{
    		$value = "";
    		
    		while (($tok = $this->expect(
    			array(
    				TOK_IDENT => TOK_IDENT, 
    				TOK_LPAREN => TOK_LPAREN, 
    				TOK_EMPTY => TOK_EMPTY))) != TOK_EMPTY)
    		{
    			switch ($tok)
    			{
    				case TOK_IDENT:
    				{
    				}
    				break;
    				
    				case TOK_LPAREN:
    				{
    					$this->_evaluate_group();
    				}
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
	$p = new XTMLProcessor();
	$e = new XTMLExpressionEvaluator($p);
	
	$e->evaluate("(a > 10 && a < 20) || s=='CPN'");
	die();
	
	$started = microtime(true);
	$iterations = 1000;
	$count = 0;

	$started = microtime(true);
	$count = 0;

	for ($i=0; $i < $iterations; $i++)
	{
		$e->evaluate("(a > 10 && a < 20) || s=='CPN'");
		$count++;

		//$e->evaluate("product.description['short']");
		//$count++;
	}

	$finished = microtime(true);
	$renderTime = ($finished - $started) * 1000;
	$perIterationRenderTime = (($finished - $started) * 1000) / $count;

	print "Tokenising $count iterations took " . sprintf("%0.2f", $renderTime) . "ms, " . sprintf("%0.2f", $perIterationRenderTime) . "ms per iteration\n";
?>

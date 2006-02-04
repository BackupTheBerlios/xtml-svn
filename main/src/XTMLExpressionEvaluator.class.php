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
	
	define('TOK_EMPTY', "empty");
	define('TOK_IDENT', "identifier");
	define('TOK_WS', "whitespace");
	define('TOK_NUMBER', "number");
	define('TOK_LPAREN', "(");
	define('TOK_RPAREN', ")");
	define('TOK_DOT_OPERATOR', ".");
	define('TOK_ARROW_OPERATOR', "->");
	define('TOK_PLUS', "+");
	define('TOK_MINUS', "-");
	define('TOK_LT', "<");
	define('TOK_LTE', "<=");
	define('TOK_GT', ">");
	define('TOK_GTE', ">=");
	define('TOK_EQ', "==");
	define('TOK_NEQ', "!=");
	define('TOK_NOT', "!");
	define('TOK_AND', "&&");
	define('TOK_OR', "||");
	define('TOK_BIT__AND', "&");
	define('TOK_BIT__OR', "|");
	define('TOK_COMPLEMENT', "~");
	define('TOK_LBRACKET', "[");
	define('TOK_RBRACKET', "]");
	define('TOK_STRING', "string");
	
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
		private $eaten;
		
		/**
		 * 
		 */
		public $token;
		
		/**
		 * 
		 */
		public $saved_token;
		
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
    	private function peekToken()
    	{
    		$pos = $this->pos;
    		$tok = $this->getToken();
    		$this->eaten = $this->pos;
    		$this->pos = $pos;
    		
    		return $tok;
    	}
    	
    	/**
    	 * 
    	 */
    	private function eatToken()
    	{
    		$this->pos = $this->eaten;
    	}
    	
    	/**
    	 * 
    	 */
    	private function getToken()
    	{
    		$tok = $this->_getToken();
    		
    		//print $tok . ": " . $this->token . "\n";
    		
    		return $tok;
    	}
    	
    	/**
    	 * 
    	 */
    	private function _getToken()
    	{
			$this->token = "";
			
    		if ($this->pos >= $this->expressionLen)
    		{
    			return TOK_EMPTY;
    		}

    		switch ($this->expression{$this->pos})
    		{
    			case " ":
    			case "\t":
    			{
    				$c = $this->expression{$this->pos};
    				
    				if ($c == ' ' || $c == '\t')
    				{
			    		while ($this->pos < $this->expressionLen &&
			    			($c == ' ' || $c == '\t'))
			    		{
			    			$this->token .= $this->expression{$this->pos++};
			    			
			    			if ($this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
			    		
			    		return TOK_WS;
    				}
    			}
    			
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
    				return TOK_DOT_OPERATOR;
    			break;
    			
    			case '-':
    				$this->pos++;

    				if ($this->expression{$this->pos} == '>')
    				{
   						$this->pos++;
   						return TOK_ARROW_OPERATOR;
    				}
    				
    				return TOK_MINUS;
    			break;
    			
    			case '+':
    				$this->pos++;
    				return TOK_PLUS;
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
    				    				
    				return TOK_BIT_AND;
    			break;
    			
    			case '|':
    				$this->pos++;
    				
    				if ($this->expression{$this->pos} == "|")
    				{
  						$this->pos++;
   						return TOK_OR;
    				}
    				    				
    				return TOK_BIT_OR;
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
    				$quot = $this->expression{$this->pos};
    				$this->pos++;
    				
		    		while (($c = $this->expression{$this->pos}) != $quot)
		    		{
		    			if ($c == '\\')
		    			{
		    				$c = $this->expression{$this->pos++};
		    				$this->token .= $c; 
		    			}
		    			
	    				$c = $this->expression{$this->pos++};
		    			$this->token .= $c;
		    		}

    				$this->pos++;
    				
    				return TOK_STRING;
    			break;
    			
    			default:
    				$c = $this->expression{$this->pos};
    				
    				if (($c >= '0' && $c <= '9'))
    				{
			    		while ($this->pos < $this->expressionLen &&
			    			($c >= '0' && $c <= '9') || $c == '.')
			    		{
			    			$this->token .= $this->expression{$this->pos++};
			    			
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
			    			(
			    				($c >= 'A' && $c <= 'Z') ||
			    				($c >= 'a' && $c <= 'z') ||
			    				($c >= '0' && $c <= '9') ||
			    				$c == '_'
			    			))
			    		{
			    			$this->token .= $this->expression{$this->pos++};

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
    	private function expected($tokens, $tok)
    	{
			print "Found " . $tok . "(" . $this->token . "), expected";
			
			foreach ($tokens as $key => $value)
			{
				print " $value";
			}
			
			print "\n";
			
    		die();
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
		private function getValue($key)
		{
			return $this->xtml->_getVarWithArrayKey($key); 
		}
		 
    	/**
    	 * 
    	 */
    	private function getIdent()
    	{
    		$key = $this->_getIdent($index = array());
    		
    		return $this->getValue($key);
    	}
    	
    	/**
    	 * 
    	 */
    	private function _getIdent($index)
    	{
    		array_push($index, $this->token);

    		for (;;)
    		{
    			$tok = $this->peekToken();
    			
    			switch ($tok)
    			{
    				case TOK_DOT_OPERATOR:
    				case TOK_ARROW_OPERATOR:
    				{
    					$this->eatToken();
    					$tok = $this->peekToken();
    					
    					if ($tok == TOK_IDENT)
    					{
    						$this->eatToken();
    						$tok = $this->_getIdent($index);
    					}
    				}
    				break;
    				
    				case TOK_WS:
    				{
    					// ignore ws
    					$this->eatToken();
    				}
    				break;
    				
    				default:
    				{
    					return $index;
    				}
    			}
    		}
    		
    		return $index;
    	}

    	/**
    	 * 
    	 */
    	private function _evaluate($terminal = TOK_EMPTY)
    	{
    		$value = "";
    		
    		while (($tok = $this->expect(
    			array(
    				TOK_EMPTY => "empty", 
    				TOK_WS => "whitespace", 
    				TOK_IDENT => "identifier", 
    				TOK_LPAREN => "(",
    				$terminal => $terminal))) != TOK_EMPTY)
    		{
    			switch ($tok)
    			{
    				case TOK_IDENT:
    				{
    					$value = $this->getIdent();
    					print "$value\n";
    				}
    				break;
    				
    				case TOK_LPAREN:
    				{
    					$this->_evaluate(TOK_RPAREN);
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
	$p->setVar("a", "15");
	$p->setVar("s", "CPN");
	$e = new XTMLExpressionEvaluator($p);
	
	$e->evaluate("(a.b->z > 10 && a < 20) || s=='CPN'");
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

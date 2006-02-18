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

	define('TOK_EMPTY', "empty");
	define('TOK_IDENT', "identifier");
	define('TOK_WS', "whitespace");
	define('TOK_NUMBER', "number");
	define('TOK_BOOLEAN', "boolean");
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
	define('TOK_BIT_AND', "&");
	define('TOK_BIT_OR', "|");
	define('TOK_COMPLEMENT', "~");
	define('TOK_LBRACKET', "[");
	define('TOK_RBRACKET', "]");
	define('TOK_STRING', "string");
	define('TOK_OBJECT', "object");
	
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
		public $token;
		
		/**
		 * 
		 */
		public $text;

		/**
		 * 
		 */
		private $lvalue;
		
		/**
		 * 
		 */
		private $stack;
		
		/**
		 * 
		 */
		private $cache;
		
		/**
		 * 
		 */
	    function XTMLExpressionEvaluator($xtml) 
	    {
	    	$this->xtml = $xtml;
			$this->expression = null;
			$this->expressionLen = null;
			$this->pos = null;
			$this->token = null;
			$this->text = null;
			$this->stack = array();
			$this->lvalue = null;
    	}
    	
    	/**
    	 * 
    	 */
    	private function getToken()
    	{
    		if ($this->token == null)
    		{
    			$this->token = $this->_getToken();
    		}
    		    		
    		return $this->token;
    	}
    	
    	/**
    	 * 
    	 */
    	private function consume()
    	{
    		$this->token = null;
    		$this->getToken();
    	}
    	
    	/**
    	 * 
    	 */
    	private function _getToken()
    	{
    		if ($this->pos < $this->expressionLen)
    		{
	    		// skip ws
	    		switch (($c = $this->expression{$this->pos}))
	    		{
	    			case " ":
	    			case "\t":
	    			{
	    				$c = $this->expression{$this->pos};
	    				
			    		while ($this->pos < $this->expressionLen &&
			    			($c == ' ' || $c == '\t'))
			    		{
			    			if (++$this->pos < $this->expressionLen)
			    			{
			    				$c = $this->expression{$this->pos};
			    			}
			    		}
	    			}
	    		}
    		}
    			
			$this->text = "";
			
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
		    				$this->text .= $c; 
		    			}
		    			
	    				$c = $this->expression{$this->pos++};
		    			$this->text .= $c;
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
			    			$this->text .= $this->expression{$this->pos++};
			    			
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
			    			$this->text .= $this->expression{$this->pos++};

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
		private function _expect($terminal)
		{
			if ($this->token == $terminal)
			{
				$this->consume();
			}
			else
			{
				print_r($this->expression);
				print_r($this->stack);
				die("found " . $this->token . "(" . $this->text . ") expected $terminal");
			}
		}

		/**
		 * 
		 */
		function isOperator()
		{
			switch ($this->token)
			{
				case TOK_PLUS:
				case TOK_MINUS:
				case TOK_LT:
				case TOK_LTE:
				case TOK_GT:
				case TOK_GTE:
				case TOK_EQ:
				case TOK_NEQ:
				case TOK_AND:
				case TOK_OR:
				case TOK_BIT_AND:
				case TOK_BIT_OR:
					return true;
			}
			
			return false;
		}
		
		/**
		 * 
		 */
		function _ident()
		{
			//print ">_ident($this->token($this->text))\n";
   			
    		switch ($this->token)
    		{
    			case TOK_IDENT:
    			{
    				$ident = $this->text;
    				$this->consume();
    				
    				if ($this->token == TOK_DOT_OPERATOR || $this->token == TOK_ARROW_OPERATOR)
    				{
    					$this->consume();
    					$this->_ident();
    					$this->push(TOK_IDENT, $ident);
    					$this->push(TOK_DOT_OPERATOR, TOK_DOT_OPERATOR);
    				}
    				else
    				{
	       				$this->push(TOK_IDENT, $ident);
    				}
    			}
    			break;
    		}
			
			//print "<_ident($this->token($this->text))\n";
		}
		
    	/**
    	 * 
    	 */
    	private function _expr()
    	{
			//print ">_expr($this->token($this->text))\n";
   			//print "A: " . $this->token . " " . $this->text . "\n";
   			
    		switch ($this->token)
    		{
    			case TOK_LPAREN:
    			{
    				$this->_expect(TOK_LPAREN);
    				$this->push(TOK_LPAREN, "(");
    				$this->_expr();
    				$this->_expect(TOK_RPAREN); 
    				$this->push(TOK_RPAREN, ")");
    				
    				if ($this->isOperator())
    				{
    					$operator = $this->token;
    					$this->consume();
    					$this->_expr();
    					$this->push($operator, $operator);
    				}
    			}
    			break;
    			
    			case TOK_IDENT:
    			{
    				$this->_ident();
    				
    				if ($this->isOperator())
    				{
    					$operator = $this->token;
    					$this->consume();
    					$this->_expr();
    					$this->push($operator, $operator);
    				}
    			}
    			break;
    			
    			case TOK_NUMBER:
    			case TOK_STRING:
    			{
    				$this->push($this->token, $this->text);
    				$this->consume();
    				
    				if ($this->isOperator())
    				{
    					$operator = $this->token;
    					$this->consume();
    					$this->_expr();
    					$this->push($operator, $operator);
    				}
    			}
    			
    			case TOK_EMPTY:
    			{
    				return TOK_EMPTY; 
    			}
    			
    			default:
    			{
    			}
    		}	
			//print "<_expr($this->token($this->text))\n";
    	}
    	
    	/**
    	 * 
    	 */
    	function push($tok, $value)
    	{
    		array_push($this->stack, array($tok, $value));
    	}
    	
    	/**
    	 * 
    	 */
    	function pop()
    	{
    		return array_pop($this->stack);
    	}
    	
    	/**
    	 *  
    	 */
    	function parse($expression)
    	{
	    	$this->pos = 0;
	    	$this->expression = $expression;
	    	$this->expressionLen = strlen($this->expression);
			$this->token = null;
			$this->stack = array();

			$this->getToken();

    		$this->_expr();
    		
    		return $this->stack;
    	}
    	
    	/**
    	 *  
    	 */
    	function execute()
    	{
    		while (count($this->stack) > 0)
    		{
    			$tok = $this->pop();

    			switch ($tok[0])
    			{
    				case TOK_PLUS:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_NUMBER, $lvalue[1] + $rvalue[1]);
    				}
    				break;
    				
    				case TOK_MINUS:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_NUMBER, $lvalue[1] - $rvalue[1]);
    				}
    				break;
    				
    				case TOK_OR:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_BOOLEAN, $lvalue[1] || $rvalue[1] ? 1:0);
    				}
    				break;
    				
    				case TOK_EQ:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_BOOLEAN, $lvalue[1] == $rvalue[1] ? 1:0);
    				}
    				break;

    				case TOK_GT:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_BOOLEAN, $lvalue[1] > $rvalue[1] ? 1:0);
    				}
    				break;

    				case TOK_LT:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    					$this->push(TOK_BOOLEAN, $lvalue[1] < $rvalue[1] ? 1:0);
    				}
    				break;

    				case TOK_AND:
    				{
    					$rvalue = $this->execute();
    					$lvalue = $this->execute();
    				}
    				break;

					// NOTE: covers the -> also, as it is transposed during stack creation  
					case TOK_DOT_OPERATOR:
					{
						$key = array();
						$tok = $this->pop();
						array_push($key, $tok[1]);
						$tok = $this->pop();
						
						while ($tok[0] == TOK_DOT_OPERATOR)
						{
							$tok = $this->pop();
							array_push($key, $tok[1]);
							$tok = $this->pop();
						}
						
						array_push($key, $tok[1]);

						$v = $this->xtml->_getVarWithArrayKey(array($tok[1]));
						
    					if (is_numeric($v))
    					{
    						return array(TOK_NUMBER, $v);
    					}
    					else if (is_string($v))
    					{
    						return array(TOK_STRING, $v);
    					}
					}
					break;
					
					case TOK_RPAREN:
					{
						return $this->execute();
					}
					break;

					case TOK_LPAREN:
					{
						$tok = $this->pop();
						
						return $tok;
					}
					break;
    				
    				case TOK_STRING:
    				case TOK_NUMBER:
    				case TOK_BOOLEAN:
    				{
    					return $tok;
    				}
    				break;
    				
    				case TOK_IDENT:
    				{
    					$v = $this->xtml->_getVarWithArrayKey(array($tok[1]));
    					
    					if (is_numeric($v))
    					{
    						return array(TOK_NUMBER, $v);
    					}
    					else if (is_string($v))
    					{
    						return array(TOK_STRING, $v);
    					}
    					else if (is_object($v))
    					{
    						return array(TOK_OBJECT, $v);
    					}
    				}
    				break;
    				
    				default:
    					print_r($this->stack);
    					die("Unexpected instruction " . $tok[0] . "\n");
    			}
    		}

			return;
    	}
    	
    	/**
    	 *  
    	 */
    	function evaluate($expression)
    	{
    		if (!isset($this->cache[$expression]))
    		{
	    		$stack = $this->parse($expression);
    			$this->cache[$expression] = $stack;
    		}
    		else
    		{
    			$this->stack = $this->cache[$expression];
    		}
    		
    		$result = $this->execute();
    		
    		return $result[1];
    	}
	}

	/*
	 * TODO: Remove this test code
	include "XTMLProcessor.class.php";
	
	$x = new XTMLProcessor();
	$x->setVar("a", "15");
	$x->setVar("b", "23");
	$x->setVar("c", "9");
	$x->setVar("s", "CPN");

	$e = new XTMLExpressionEvaluator($x);

	$started = microtime(true);
	$iterations = 1000;
	$count = 0;

	$started = microtime(true);
	$count = 0;

	for ($i=0; $i < $iterations; $i++)
	{
		print $e->evaluate("(a > 10 && a < 14) || s=='CPN'") . "\n";
		$count++;
	}
	
	$finished = microtime(true);
	$renderTime = ($finished - $started) * 1000;
	$perIterationRenderTime = (($finished - $started) * 1000) / $count;

	print "Executing $count iterations took " . sprintf("%0.2f", $renderTime) . "ms, " . sprintf("%0.2f", $perIterationRenderTime) . "ms per iteration\n";

	*/
?>

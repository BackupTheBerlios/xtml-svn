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
	define('TOK_BIT_AND', "&");
	define('TOK_BIT_OR', "|");
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
    	private function expected($line, $msg)
    	{
			die("($line): found '" . $this->token . "', expected $msg\n");
    	}
    	
		/**
		 * 
		 */
		private function getValue($key)
		{
			return $this->xtml->x_getVarWithArrayKey($key); 
		}
		 
    	/**
    	 * 
    	 */
    	private function getIdent()
    	{
    		$key = $this->x_getIdent($index = array());
    		
    		return $this->getValue($key);
    	}
    	
    	/**
    	 * 
    	 */
    	private function x_getIdent($index)
    	{
    		array_push($index, $this->text);
			$this->consume();

    		for (;;)
    		{
    			$tok = $this->getToken();

    			switch ($tok)
    			{
    				case TOK_DOT_OPERATOR:
    				case TOK_ARROW_OPERATOR:
    				{
    					$this->consume();
    					$tok = $this->getToken();
    					
    					if ($tok == TOK_IDENT)
    					{
    						$this->consume();
    						$tok = $this->x_getIdent($index);
    					}
    				}
    				break;
    				
    				case TOK_WS:
    				{
    					// ignore ws
    					$this->consume();
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
    	private function x_expression()
    	{
    		while ($tok = $this->getToken())
    		{
				switch ($tok = $this->getToken())
				{
    				case TOK_IDENT:
    				case TOK_NUMBER:
    				case TOK_STRING:
    				{
    					$this->lvalue = $this->x_expression();
    				}
    				break;
    				
					case TOK_EMPTY:
					{
						return $this->lvalue;
					}
					break;
					
					case TOK_RPAREN:
					{
						return $this->lvalue;
					}
					break;
					
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
					{
						$operator = $tok;
						$this->consume();
						
						switch ($tok = $this->getToken())
						{
		    				case TOK_IDENT:
		    				case TOK_NUMBER:
		    				case TOK_STRING:
		    				{
		    					$this->consume();
		    					
		    					if ($tok == TOK_IDENT)
		    					{
		    						$rvalue = $this->getIdent();
		    					}
		    					else
		    					{
		    						$rvalue = $this->text;
		    					}
		    					
		    					switch ($operator)
		    					{
									case TOK_PLUS:
									{
										$lvalue = $lvalue + $rvalue;
									}
									break;
									
									case TOK_MINUS:
									{
										$lvalue = $lvalue - $rvalue;
									}
									break;
									
									case TOK_LT:
									{
										$lvalue = $lvalue < $rvalue;
									}
									break;
									
									case TOK_LTE:
									{
										$lvalue = $lvalue <= $rvalue;
									}
									break;
									
									case TOK_GT:
									{
										$lvalue = $lvalue > $rvalue;
									}
									break;
									
									case TOK_GTE:
									{
										$lvalue = $lvalue >= $rvalue;
									}
									break;
									
									case TOK_EQ:
									{
										$lvalue = $lvalue == $rvalue;
									}
									break;
									
									case TOK_NEQ:
									{
										$lvalue = $lvalue != $rvalue;
									}
									break;
									
									case TOK_AND:
									{
										$lvalue = $lvalue && $rvalue;
									}
									break;
									
									case TOK_OR:
									{
										$lvalue = $lvalue || $rvalue;
									}
									break;
									
									case TOK_BIT_AND:
									{
										$lvalue = $lvalue & $rvalue;
									}
									break;
									
									case TOK_BIT_OR:
									{
										$lvalue = $lvalue | $rvalue;
									}
									break;
		    					}
		    				}
		    				break;
		    				
		    				case TOK_WS:
		    				{
		    					$this->consume();
		    				}
		    				break;
		    				
		    				default:
		    				{
		    					$this->expected(__LINE__, "ident, number, string");
		    				}
						}
					}
					break;
				}
    		}
    		
    		return $lvalue;
    	}
    	
    	/**
    	 * 
    	 */
    	private function x_evaluate($terminal = TOK_EMPTY)
    	{
    		while ($tok = $this->getToken())
    		{
				//print "$tok: " . $this->text . "\n";
    			
    			switch ($tok)
    			{
    				case TOK_IDENT:
    				case TOK_NUMBER:
    				case TOK_STRING:
    				{
    					$this->lvalue = $this->x_expression();
    				}
    				break;
    				
    				case TOK_LPAREN:
    				{
    					$this->consume();
    					$this->x_evaluate(TOK_RPAREN);
    				}
    				break;
    				
    				case TOK_RPAREN:
    				{
    					$this->consume();
    					
						if ($terminal != $tok)
						{
							$this->expected(__LINE__, "identifier (");
						}
    				}
    				break;
    				
    				case TOK_WS:
    				{
    					$this->consume();
    				}
    				break;
    				
    				case TOK_EMPTY:
    				{
    				}
    				break;
    				
    				default:
    				{
    					$this->expected(__LINE__, "identifier (");
    				}
    			}
    		}
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
			print ">_ident($this->token($this->text))\n";
   			//print "A: " . $this->token . " " . $this->text . "\n";
   			
    		switch ($this->token)
    		{
    			case TOK_IDENT:
    			{
    				$this->push($this->text);
    				$this->consume();
    				
    				if ($this->token == TOK_DOT_OPERATOR || $this->token == TOK_ARROW_OPERATOR)
    				{
    					$this->push($this->token);
    					$this->consume();
    					$this->_ident();
    				}
    			}
    			break;
    		}
			print "<_ident($this->token($this->text))\n";
		}
		
    	/**
    	 * 
    	 */
    	private function _expr()
    	{
			print ">_expr($this->token($this->text))\n";
   			//print "A: " . $this->token . " " . $this->text . "\n";
   			
    		switch ($this->token)
    		{
    			case TOK_LPAREN:
    			{
    				$this->_expect(TOK_LPAREN);
    				$this->push(TOK_LPAREN);
    				$this->_expr();
    				$this->_expect(TOK_RPAREN); 
    				$this->push(TOK_RPAREN);
    				
    				if ($this->isOperator())
    				{
    					$operator = $this->token;
    					$this->consume();
    					$this->_expr();
    					$this->push($operator);
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
    					$this->push($operator);
    				}
    			}
    			break;
    			
    			case TOK_NUMBER:
    			case TOK_STRING:
    			{
    				$this->push($this->text);
    				$this->consume();
    				
    				if ($this->isOperator())
    				{
    					$operator = $this->token;
    					$this->consume();
    					$this->_expr();
    					$this->push($operator);
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
			print "<_expr($this->token($this->text))\n";
    	}
    	
    	/**
    	 * 
    	 */
    	function push($value)
    	{
    		array_push($this->stack, $value);
    	}
    	
    	/**
    	 * 
    	 */
    	function pop()
    	{
    		array_pop($this->stack);
    	}
    	
    	/**
    	 * goal = stmt | <empty>
    	 * stmt = identifer 
    	 * operator = && || & | + -   
    	 * identifier = ident | ident -> | ident . 
    	 * expr = identifier | stmt operator expr | ( stmt )
    	 *
    	 *  
    	 */
    	function evaluate($expression)
    	{
	    	$this->pos = 0;
	    	$this->expression = $expression;
	    	$this->expressionLen = strlen($this->expression);
			$this->token = null;
			$this->stack = array();

			$this->getToken();

    		$this->_expr();
    		print "$expression\n";
    		print_r($this->stack);
    		
    		return "";
    	}
	}
	
	print "Starting\n";
	$p = new XTMLProcessor();
	$p->setVar("a", "15");
	$p->setVar("s", "CPN");
	$e = new XTMLExpressionEvaluator($p);
	
	//$e->evaluate("a > 10 && a < 20");
	//$e->evaluate("a + b + c");
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

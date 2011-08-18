<?php
/* Driver template for the PHP_ComponentrGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class ComponentyyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof ComponentyyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof ComponentyyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->_string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof ComponentyyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof ComponentyyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class ComponentyyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "ComponentParser.class.y"


#line 102 "ComponentParser.class.php"

// declare_class is output here
#line 6 "ComponentParser.class.y"
abstract class __ComponentParser#line 107 "ComponentParser.class.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 17 "ComponentParser.class.y"

/* ?><?php */

    protected $_component_specs = array();
    protected $_component_specs_stack = null;
    protected $_properties_stack = array();
    protected $_result;
    protected $_view_code = null;

    public $transTable =
        array(
                1  => self::OPEN_COMPONENT_TAG,
                2  => self::CLOSE_COMPONENT_TAG,
                3  => self::SHORT_COMPONENT_TAG,
                4  => self::ANYTHINGELSE,
                5  => self::OPEN_PROPERTY_TAG,
                6  => self::CLOSE_PROPERTY_TAG,
        );

    protected $_token_names =
        array(
                self::OPEN_COMPONENT_TAG  => 'Component open tag',
                self::CLOSE_COMPONENT_TAG => 'Component close tag (</comp:[component]>)',
                self::SHORT_COMPONENT_TAG => 'Component tag',
                self::ANYTHINGELSE        => 'Any character',
                self::OPEN_PROPERTY_TAG   => 'Property open tag',
                self::CLOSE_PROPERTY_TAG  => 'Property close tag within the [component] component',
        );

    private function _getTokenName($token_id) {
        $return_value = $token_id;
        if(key_exists($token_id, $this->_token_names)) {
            $return_value = $this->_token_names[$token_id];
            $current_component_spec = $this->_getCurrentComponentSpec();
            if($current_component_spec != null) {
                $return_value = str_replace('[component]', $current_component_spec->getTag(), $return_value);
            }
        }
        return $return_value;
    }        

    public function __construct(__View &$view)
    {
        $this->_view_code = $view->getCode();
        $this->_component_specs_stack  = new __Stack();
        $this->_properties_stack = new __Stack();
    }

    public function getResult() {
        return $this->_result;
    }
    
    protected function _getTagName($tag_expression) {
    	$return_value = null;
        if(preg_match('/\<\/?comp\:([A-Za-z_][A-Za-z_0-9]*)/i', $tag_expression, $matched)) {
        	$return_value = $matched[1];
        }
        return $return_value;
    }

    //todo: improve this method!
    protected function _getAttributeList($tag_expression) {
        preg_match('/<\/?comp\:[A-Za-z_][A-Za-z_0-9]*((\s+\w+(\s*=\s*(?:\"[^\"]*\"|\'[^\']*\'|[^\'\">\s]+))?)+\s*|\s*)\/?>/i', $tag_expression, $matched);
        $params_str = trim($matched[1]);
        $return_value = $this->_splitParams($params_str);
        return $return_value;
    }
    
    protected function _getComponentPropertyName($tag_expression) {
    	$return_value = null;
	    if(preg_match('/\<comp\-property\s+name\s*\=\s*\"([A-Za-z_][A-Za-z_0-9]*)\"\s*>/i', $tag_expression, $matched)) {
	        $return_value = $matched[1];
	    }
	    return $return_value;
    }

	/**
	 * This rather nasty looking function takes the parameters
	 * inside our opening tag and puts them into an associative array.
	 * 
	 * @param string $params_str The string to split into parameters
	 * @return array An associative array of pairs [key, value]
	 */ 
	protected function _splitParams($params_str) {
	    $params_str = trim($params_str);
	    $length = strlen($params_str);
	    $return_value = array();
	    $key = '';
	    $val = '';
	    $single_quote_hex = "\x27";
	    $double_quote_hex = "\x22";
	    $done = false;
	    $remove_spaces = false;
	    /* $mode:
	      0 - search for key
	      1 - search for =
	      2 - search for end
	      3 - search for end with quotes */
	    $mode = 0;
		$quote_character = null;
	    for ($x = 0; $x < $length; $x++) {
	        $chr = substr($params_str, $x, 1);
	        if(preg_match('/\s/', $chr)) {
	        	$is_space = true;
	        }
	        else {
	        	$is_space = false;
	        }
			if ($remove_spaces == false || !$is_space) {
				$remove_spaces = false; //in any case
		        switch ($mode) {
		        	//initial state:
		            case 0:
		                if ( !$is_space ) {
		                	$key .= $chr;
		                }
		                $mode = 1;
		                break;
		            //scan the key:
		            case 1:
		            	if ($chr != '=') {
		            		if($is_space) {
		            			$remove_spaces = true;	
		            		}
		            		else {
		                		$key .= $chr;
		            		}
		                }
		                else {
		                	$mode = 2;
		                	$remove_spaces = true;
		                }
		            	break;
		            //scan the value:
		            case 2:
		                if (($chr == "\"" || $chr == "'") && ($val == '')) {
		                    $quote_character = $chr;
		                    $mode = 3;
		                } else {
		                    if ( $is_space || ($x == ($length - 1)) ) {
		                        $done = true;
		                        if (! $is_space ) {
		                            $val .= $chr;
		                        }
		                    } else {
		                        $val .= $chr;
		                    }
		                }
		                break;
		            case 3:
		                if (($chr == $quote_character)||($x == ($length - 1))) {
		                    $done = true;
		                    if ($chr != $quote_character) {
		                        $val .= $chr;
		                    }
		                } else {
		                    $val .= $chr;
		                }
		            break;
		        }
			}
			
	        if ($done == true) {
	            $mode = 0;
	            $done = false;
                //remove delimiter quotes if exists: 
                $key = trim(strtolower($key));
                switch($quote_character) {
                    case '"':
                        $val = trim($val, $double_quote_hex);
                        break;
                    case "'":
                        $val = trim($val, $single_quote_hex);
                        break;
                }
	            $return_value[$key] = $val;
	            $key = '';
	            $val = '';
	        }
	    }
	    return $return_value;
	}
    
    
    protected function _pushComponentSpec(__ComponentSpec &$component_spec) {
        return $this->_component_specs_stack->push($component_spec);
    }
    
    protected function &_popComponentSpec() {
        $return_value = $this->_component_specs_stack->pop();
        return $return_value;
    }    
    
    protected function _registerComponentSpec(__ComponentSpec &$component_spec) {
        if($this->_properties_stack->count() == 0 && $this->_component_specs_stack->count() > 0) {
            $current_component_spec  = $this->_component_specs_stack->peek();
            $current_component_class = $current_component_spec->getClass();
            if(!is_subclass_of($current_component_class, '__IContainer')) {
                throw __ExceptionFactory::getInstance()->createException('ERR_UI_COMPONENT_IS_NOT_CONTAINER', array($current_component_spec->getTag(), $component_spec->getTag()));
            }
        }
        $this->_component_specs[$component_spec->getId()] =& $component_spec;            
    }

    protected function &_getCurrentComponentSpec() {
        $return_value = null;
        if($this->_component_specs_stack->count() > 0) {
            $return_value = $this->_component_specs_stack->peek();
        }
        return $return_value;
    }    
    
    protected function _pushProperty($property) {
        $this->_properties_stack->push($property);
    }

    protected function _popProperty() {
        return $this->_properties_stack->pop();
    }
    
    protected function _getCurrentProperty() {
        $return_value = null;
        if($this->_properties_stack->count() > 0) {
            $return_value = $this->_properties_stack->peek();
        }
        return $return_value;
    }
    
    abstract protected function _getStartRenderCode();

    abstract protected function _getComponentSingleTagCode(__ComponentSpec &$component_spec);
    
    abstract protected function _getComponentBeginTagCode(__ComponentSpec &$component_spec);

    abstract protected function _getComponentEndTagCode(__ComponentSpec &$component_spec);
    
    abstract protected function _getComponentPropertyTagCode($property, $value);
    
    abstract protected function _getEndRenderCode();
    
#line 353 "ComponentParser.class.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const SHORT_COMPONENT_TAG            =  1;
    const OPEN_COMPONENT_TAG             =  2;
    const CLOSE_COMPONENT_TAG            =  3;
    const OPEN_PROPERTY_TAG              =  4;
    const CLOSE_PROPERTY_TAG             =  5;
    const ANYTHINGELSE                   =  6;
    const YY_NO_ACTION = 42;
    const YY_ACCEPT_ACTION = 41;
    const YY_ERROR_ACTION = 40;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 34;
static public $yy_action = array(
 /*     0 */    17,   16,   18,    6,   19,   20,    5,   14,    7,   11,
 /*    10 */     6,   12,   19,   20,    2,   15,   21,   13,    4,    8,
 /*    20 */     9,    3,   10,    9,   40,   19,   20,   41,    1,   40,
 /*    30 */    40,    7,   40,    6,
    );
    static public $yy_lookahead = array(
 /*     0 */    10,   11,   12,   13,    1,    2,   16,    4,   11,    6,
 /*    10 */    13,    3,    1,    2,    9,   18,    5,   15,   17,   14,
 /*    20 */    19,   17,    6,   19,   20,    1,    2,    8,    9,   20,
 /*    30 */    20,   11,   20,   13,
);
    const YY_SHIFT_USE_DFLT = -1;
    const YY_SHIFT_MAX = 9;
    static public $yy_shift_ofst = array(
 /*     0 */    -1,    3,    3,   11,   24,   -1,   -1,   -1,    8,   16,
);
    const YY_REDUCE_USE_DFLT = -11;
    const YY_REDUCE_MAX = 8;
    static public $yy_reduce_ofst = array(
 /*     0 */    19,  -10,  -10,   -3,   20,    4,    5,    1,    2,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(),
        /* 1 */ array(1, 2, 4, 6, ),
        /* 2 */ array(1, 2, 4, 6, ),
        /* 3 */ array(1, 2, 5, ),
        /* 4 */ array(1, 2, ),
        /* 5 */ array(),
        /* 6 */ array(),
        /* 7 */ array(),
        /* 8 */ array(3, ),
        /* 9 */ array(6, ),
        /* 10 */ array(),
        /* 11 */ array(),
        /* 12 */ array(),
        /* 13 */ array(),
        /* 14 */ array(),
        /* 15 */ array(),
        /* 16 */ array(),
        /* 17 */ array(),
        /* 18 */ array(),
        /* 19 */ array(),
        /* 20 */ array(),
        /* 21 */ array(),
);
    static public $yy_default = array(
 /*     0 */    26,   22,   31,   40,   35,   38,   26,   38,   40,   36,
 /*    10 */    37,   39,   30,   27,   33,   32,   24,   23,   25,   28,
 /*    20 */    29,   34,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 21;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 22;
    const YYNRULE = 18;
    const YYERRORSYMBOL = 7;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array( 
  '$',             'SHORT_COMPONENT_TAG',  'OPEN_COMPONENT_TAG',  'CLOSE_COMPONENT_TAG',
  'OPEN_PROPERTY_TAG',  'CLOSE_PROPERTY_TAG',  'ANYTHINGELSE',  'error',       
  'start',         'ui_code',       'anychar',       'component_tag',
  'component_property',  'r_open_component_tag',  'component_body',  'r_close_component_tag',
  'r_open_property_tag',  'property_value',  'r_close_property_tag',  'literal',     
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= ui_code",
 /*   1 */ "ui_code ::= ui_code anychar",
 /*   2 */ "ui_code ::= ui_code component_tag",
 /*   3 */ "ui_code ::= ui_code component_property",
 /*   4 */ "ui_code ::=",
 /*   5 */ "component_tag ::= r_open_component_tag component_body r_close_component_tag",
 /*   6 */ "component_tag ::= SHORT_COMPONENT_TAG",
 /*   7 */ "r_open_component_tag ::= OPEN_COMPONENT_TAG",
 /*   8 */ "r_close_component_tag ::= CLOSE_COMPONENT_TAG",
 /*   9 */ "component_body ::= ui_code",
 /*  10 */ "component_property ::= r_open_property_tag property_value r_close_property_tag",
 /*  11 */ "r_open_property_tag ::= OPEN_PROPERTY_TAG",
 /*  12 */ "r_close_property_tag ::= CLOSE_PROPERTY_TAG",
 /*  13 */ "property_value ::= property_value component_tag property_value",
 /*  14 */ "property_value ::= literal",
 /*  15 */ "literal ::= literal ANYTHINGELSE",
 /*  16 */ "literal ::=",
 /*  17 */ "anychar ::= ANYTHINGELSE",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param ComponentyyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ComponentyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ComponentyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new ComponentyyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 8, 'rhs' => 1 ),
  array( 'lhs' => 9, 'rhs' => 2 ),
  array( 'lhs' => 9, 'rhs' => 2 ),
  array( 'lhs' => 9, 'rhs' => 2 ),
  array( 'lhs' => 9, 'rhs' => 0 ),
  array( 'lhs' => 11, 'rhs' => 3 ),
  array( 'lhs' => 11, 'rhs' => 1 ),
  array( 'lhs' => 13, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 12, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 18, 'rhs' => 1 ),
  array( 'lhs' => 17, 'rhs' => 3 ),
  array( 'lhs' => 17, 'rhs' => 1 ),
  array( 'lhs' => 19, 'rhs' => 2 ),
  array( 'lhs' => 19, 'rhs' => 0 ),
  array( 'lhs' => 10, 'rhs' => 1 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        2 => 1,
        3 => 1,
        15 => 1,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        17 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        16 => 16,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 262 "ComponentParser.class.y"
    function yy_r0(){ 
    $this->_result = $this->_getStartRenderCode() . $this->yystack[$this->yyidx + 0]->minor . $this->_getEndRenderCode();
    }
#line 1029 "ComponentParser.class.php"
#line 267 "ComponentParser.class.y"
    function yy_r1(){
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1034 "ComponentParser.class.php"
#line 282 "ComponentParser.class.y"
    function yy_r4(){
    $this->_retvalue = '';
    }
#line 1039 "ComponentParser.class.php"
#line 287 "ComponentParser.class.y"
    function yy_r5(){
    $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1044 "ComponentParser.class.php"
#line 292 "ComponentParser.class.y"
    function yy_r6(){ 
    //Setup, validate and register current component:
    $tag_name  = $this->_getTagName($this->yystack[$this->yyidx + 0]->minor);
    $attribute_list = $this->_getAttributeList($this->yystack[$this->yyidx + 0]->minor);
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($tag_name);
    $component_spec->setDefaultValues($attribute_list);
    $this->_registerComponentSpec($component_spec);  
    if($this->_getCurrentProperty() == null) {
        $this->_retvalue = $this->_getComponentSingleTagCode($component_spec);
    }
    else {
        $this->_retvalue = $component;
    }
    }
#line 1060 "ComponentParser.class.php"
#line 308 "ComponentParser.class.y"
    function yy_r7(){
    //Setup and validate current component:
    $tag_name  = $this->_getTagName($this->yystack[$this->yyidx + 0]->minor);
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($tag_name);
    $attribute_list = $this->_getAttributeList($this->yystack[$this->yyidx + 0]->minor);
    $component_spec->setDefaultValues($attribute_list);
    $this->_registerComponentSpec($component_spec);
    $this->_pushComponentSpec($component_spec);
    $this->_retvalue = $this->_getComponentBeginTagCode($component_spec);
    }
#line 1072 "ComponentParser.class.php"
#line 320 "ComponentParser.class.y"
    function yy_r8(){
    //Retrieve the current component and perform validations:
    $tag_name = $this->_getTagName($this->yystack[$this->yyidx + 0]->minor);
    $component_spec =& $this->_popComponentSpec();
    if(strtoupper($component_spec->getTag()) != strtoupper($tag_name)) {
        throw __ExceptionFactory::getInstance()->createException('ERR_UI_UNEXPECTED_CLOSE_TAG', array($tag_name, $component_spec->getTag()));
    }
    if($this->_getCurrentProperty() == null) {
        $this->_retvalue = $this->_getComponentEndTagCode($component_spec);
    }
    else {
        $this->_retvalue = $component;
    }
    }
#line 1088 "ComponentParser.class.php"
#line 336 "ComponentParser.class.y"
    function yy_r9(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1093 "ComponentParser.class.php"
#line 344 "ComponentParser.class.y"
    function yy_r10(){
    if($this->_getCurrentComponentSpec() == null) {
        throw __ExceptionFactory::getInstance()->createException('ERR_UI_UNEXPECTED_PROPERTY_TAG');
    }
    else {
        $property_name  = $this->yystack[$this->yyidx + -2]->minor;
        //Now will parse the property value:
        $value_is_string = false;
        $property_value = null;
        $property_value_as_string = '';
        $property_value_array = $this->yystack[$this->yyidx + -1]->minor;
        foreach($property_value_array as $property_value_part) {
            if(is_string($property_value_part)) {
                $property_value_as_string .= $property_value_part;
                if(trim($property_value_part) != '') {
                    $value_is_string = true;
                }
            }
            else if($property_value_part instanceof __IComponent) {
                $property_value_as_string .= $property_value_part->__toString();
                if($component_as_value == null) {
                    $property_value = $property_value_part;
                }
                else {
                    $value_is_string = true;
                }
            }
        }
        if($value_is_string) {
            $property_value = $property_value_as_string;
        }
        $component_spec = $this->_getCurrentComponentSpec();
        $this->_retvalue = $this->_getComponentPropertyTagCode($property_name, $property_value);
    }
    }
#line 1130 "ComponentParser.class.php"
#line 381 "ComponentParser.class.y"
    function yy_r11(){
	$property_name = $this->_getComponentPropertyName($this->yystack[$this->yyidx + 0]->minor);
    $this->_pushProperty($property_name);
    $this->_retvalue = $property_name;
    }
#line 1137 "ComponentParser.class.php"
#line 388 "ComponentParser.class.y"
    function yy_r12(){
    $this->_popProperty();
    $this->_retvalue = '';
    }
#line 1143 "ComponentParser.class.php"
#line 394 "ComponentParser.class.y"
    function yy_r13(){
	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
	$this->_retvalue[] = $this->yystack[$this->yyidx + -1]->minor;
	$this->_retvalue[] = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1150 "ComponentParser.class.php"
#line 401 "ComponentParser.class.y"
    function yy_r14(){
	$this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1155 "ComponentParser.class.php"
#line 411 "ComponentParser.class.y"
    function yy_r16(){ 
    $this->_retvalue = '';
    }
#line 1160 "ComponentParser.class.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //ComponentyyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new ComponentyyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 8 "ComponentParser.class.y"

    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = $this->_getTokenName($token);
    }
    throw new __UIComponentException('Error parsing the template: Unexpected ' . $this->_getTokenName($this->tokenName($yymajor)) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
#line 1281 "ComponentParser.class.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
#line 257 "ComponentParser.class.y"

    return $this->_result;
#line 1303 "ComponentParser.class.php"
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new ComponentyyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
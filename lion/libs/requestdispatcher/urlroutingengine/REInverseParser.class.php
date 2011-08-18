<?php
/* Driver template for the PHP_REInverserGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class REInverseyyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof REInverseyyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof REInverseyyToken) {
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
                $x = ($value instanceof REInverseyyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof REInverseyyToken) {
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
class REInverseyyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "REInverseParser.class.y"


class __ExpressionVariable {

    private $_varname  = null;
    private $_varvalue = null;

    public function __construct($varname) {
        $this->_varname = $varname;
    }
    
    public function isOptional() {
        return false;
    }
    
    public function getVariableName() {
        return $this->_varname;
    }

    public function setVariableValue($value) {
        $this->_varvalue = $value;
    }

    public function getVariableValue() {
        return $this->_varvalue;
    }

    public function toString() {
        $return_value = $this->_varvalue;
        return $return_value;
    }

}

class __ExpressionTemplate {

    private $_variables   = array();
    private $_optional_variables = array();
    private $_expressions = array();
    private $_optional    = false;
    
    public function __clone() {
        foreach($this->_expressions as &$expression) {
            if(is_object($expression)) {
                $expression = clone $expression;
            }
        }
    }    
    
    public function __construct($expression = null) {
        if($expression != null) {
            if($expression instanceof __ExpressionTemplate) {
                $this->_variables   = $expression->_variables;
                $this->_optional_variables = $expression->_optional_variables;
                $this->_expressions = $expression->_expressions;
                $this->_optional    = $expression->_optional;
            }
            else {
                $this->addExpression($expression);
            }
        }
    }
    
    private function _analyzeExpression($expression) {
        if($expression instanceof __ExpressionTemplate) {
            $this->_variables = array_merge($expression->_variables, $this->_variables);
            if($this->isOptional() && !$expression->isOptional()) {
                $this->_optional_variables = array_merge($expression->_variables, $this->_optional_variables);
            }
            else {
                $this->_optional_variables = array_merge($expression->_optional_variables, $this->_optional_variables);
            }
        }
        else if($expression instanceof __ExpressionVariable) {
            $var_name = $expression->getVariableName();
            if(!empty($var_name)) {
                $this->_variables[$expression->getVariableName()] = $expression->getVariableValue();
            }
            if($this->isOptional()) {
                $this->_optional_variables[$expression->getVariableName()] = $expression->getVariableValue();
            }
        }
        else if(is_array($expression)) {
            foreach($expression as $subexpression) {
                $this->_analyzeExpression($subexpression);
            }
        }
    }
    
    public function addExpression($expression) {
        if(!empty($expression)) {
            if($expression instanceof __ExpressionTemplate && !$expression->isOptional()) {
                $expressions = $expression->getExpressions();
                $this->_variables = array_merge($expression->_variables, $this->_variables);
                if($this->isOptional()) {
                    $this->_optional_variables = array_merge($expression->_variables, $this->_optional_variables);
                }
                else {
                    $this->_optional_variables = array_merge($expression->_optional_variables, $this->_optional_variables);
                }
                foreach($expressions as $expression) {
                    $this->addExpression($expression);
                }
            }
            else {
                $this->_analyzeExpression($expression);
                if(is_string($expression) && count($this->_expressions) > 0 && is_string($this->_expressions[count($this->_expressions)-1])) {
                    $this->_expressions[count($this->_expressions)-1] .= $expression;
                }
                else {
                    $this->_expressions[] = $expression;
                    $this->_isString = false;
                }
            }
        }
    }

    public function isString() {
        return $this->_isString;        
    }
    
    public function addVariable($varname) {
        $this->_variables[$varname] = null;
        if($this->isOptional()) {
            $this->_optional_variables[$varname] = null;
        }
        $this->_isString = false;
    }

    public function setVariableValue($varname, $varvalue) {
        foreach($this->_expressions as &$expression) {
            if($expression instanceof __ExpressionTemplate && $expression->hasVariable($varname)) {
                $expression->setVariableValue($varname, $varvalue);
            }
            else if($expression instanceof __ExpressionVariable && $expression->getVariableName() == $varname) {
                $expression->setVariableValue($varvalue);
            }
        }
        $this->_variables[$varname] = $varvalue;
        if($this->isOptional()) {
            $this->_optional_variables[$varname] = $varvalue;
        }
    }

    public function isOptionalVariable($varname) {
        $return_value = false;
        if(key_exists($varname, $this->_optional_variables)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setVariableDefaultValue($varname, $varvalue) {
        foreach($this->_expressions as &$expression) {
            if($expression instanceof __ExpressionTemplate && $expression->hasVariable($varname)) {
                $expression->setVariableDefaultValue($varname, $varvalue);
            }
            else if($expression instanceof __ExpressionVariable && $expression->getVariableName() == $varname && $this->isOptional() == false) {
                $expression->setVariableValue($varvalue);
            }
        }
    }
    
    public function getVariables() {
        return $this->_variables;
    }

    public function getOptionalVariables() {
        return $this->_optional_variables;
    }
    
    public function getVariableValue($variable_name) {
        $return_value = null;
        if(key_exists($variable_name, $this->_variables)) {
            $return_value = $this->_variables[$variable_name];
        }
        return $return_value;
    }
    
    public function getExpressions() {
        return $this->_expressions;
    }
    
    public function hasVariable($varname) {
        return key_exists($varname, $this->_variables);
    }

    public function setOptional($optional) {
        $this->_optional = $optional;
        if($optional == true) {
            $this->_optional_variables = $this->_variables;
        }
    }

    public function isOptional() {
        return $this->_optional;
    }

    public function __toString() {
        $return_value = $this->getREInverse();
        if($return_value == null) {
            $return_value = "";
        }
        return $return_value;
    }

    public function toString() {
        return $this->getREInverse();
    }

    public function getREInverse() {
        $return_value = "";
        foreach($this->_expressions as $expression) {
            if(is_array($expression)) {
                $expression = $this->_selectSubExpression($expression);
            }
            if(!is_string($expression)) {
                if($expression->toString() == null) {
                    if($this->isOptional() && !$expression->isOptional()) {
                        return null;
                    }
                    else {
                        $expression = "";
                    }
                }
                else {
                    $expression = $expression->toString();
                }
            }
            $return_value .= $expression;
        }
        return $return_value;
    }

    private function _selectSubExpression($expression) {
        $return_value = null;
        $candidate_values = array();
        foreach($expression as $subexpression) {
            $candidate_value = (string) $subexpression;
            if($candidate_value != null) {
                $candidate_values[] = $candidate_value;
            }
        }
        //take the first candidate element always:
        if(count($candidate_values) > 0) {
            $return_value = end($candidate_values);
        }
        return $return_value;
    }
    
}



class __NamedGroup {

    private $_name = null;
    private $_pattern = null;

    public function __construct($name) {
        $this->_name = $name;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function setPattern($pattern) {
        $this->_pattern = $pattern;
    }

    public function getName() {
        return $this->_name;
    }

    public function getPattern() {
        return $this->_pattern;
    }
    
}

#line 381 "REInverseParser.class.php"

// declare_class is output here
#line 285 "REInverseParser.class.y"
class __REInverseParser#line 386 "REInverseParser.class.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 295 "REInverseParser.class.y"

/* ?><?php */

    public function __construct($lexer)
    {
        $this->_lexer = $lexer;
    }

    public function getResult() {
        return $this->_result;
    }

    private $_lexer;
    private $_namedgroups = array();
    private $_result = "";

    public $transTable =
        array(
                1  => self::ESCSEQ,
                2  => self::CARET,
                3  => self::DOLLAR,
                4  => self::DOT,
                5  => self::COMMA,
                6  => self::LBRACKET,
                7  => self::RBRACKET,
                8  => self::PIPE,
                9  => self::LPAR,
                10 => self::RPAR,
                11 => self::QUESTIONMARK,
                12 => self::ASTERISK,
                13 => self::PLUS,
                14 => self::LBRACE,
                15 => self::RBRACE,
                16 => self::SUB,
                17 => self::NONGROUPINGGRP,
                18 => self::NAMEDGROUPDEF,
                19 => self::NAMEDGROUP,
                20 => self::COMMENT,
                21 => self::MATCHIFNEXT,
                22 => self::MATCHIFNOTNEXT,
                23 => self::POSILOOKBEHIND,
                24 => self::NEGLOOKBEHIND,
                25 => self::INTEGER,
                26 => self::ANYCHAR,
                27 => self::URLVAR,
        );

    
#line 440 "REInverseParser.class.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const PIPE                           =  1;
    const ASTERISK                       =  2;
    const QUESTIONMARK                   =  3;
    const PLUS                           =  4;
    const LBRACE                         =  5;
    const RBRACE                         =  6;
    const INTEGER                        =  7;
    const COMMA                          =  8;
    const URLVAR                         =  9;
    const LPAR                           = 10;
    const RPAR                           = 11;
    const NAMEDGROUP                     = 12;
    const NONGROUPINGGRP                 = 13;
    const MATCHIFNEXT                    = 14;
    const MATCHIFNOTNEXT                 = 15;
    const COMMENT                        = 16;
    const NAMEDGROUPDEF                  = 17;
    const POSILOOKBEHIND                 = 18;
    const NEGLOOKBEHIND                  = 19;
    const DOT                            = 20;
    const DOLLAR                         = 21;
    const CARET                          = 22;
    const ESCSEQ                         = 23;
    const ANYCHAR                        = 24;
    const LBRACKET                       = 25;
    const RBRACKET                       = 26;
    const SUB                            = 27;
    const YY_NO_ACTION = 137;
    const YY_ACCEPT_ACTION = 136;
    const YY_ERROR_ACTION = 135;

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
    const YY_SZ_ACTTAB = 110;
static public $yy_action = array(
 /*     0 */    64,   26,   27,   28,   29,   30,   45,   62,   20,   73,
 /*    10 */    42,   21,    5,   32,   13,   24,   39,   48,    8,   60,
 /*    20 */    44,   66,   67,   72,   63,   65,   25,   50,   58,   59,
 /*    30 */    69,    7,   31,    4,  136,   61,   54,   15,    3,    9,
 /*    40 */    37,   47,   10,   22,   49,   68,   70,    2,   14,   54,
 /*    50 */    15,    3,    9,   52,   51,   46,   11,   49,   68,   70,
 /*    60 */    17,   18,   33,   34,   35,   36,   40,   43,   23,   53,
 /*    70 */    15,    3,    9,   16,    1,   38,   24,   49,   68,   70,
 /*    80 */    24,   71,   55,    3,    9,   57,  109,   12,  109,   49,
 /*    90 */    68,   70,    7,   31,  109,    6,    7,   31,   20,   56,
 /*   100 */   109,   19,    5,   32,   20,  109,  109,   41,    5,   32,
    );
    static public $yy_lookahead = array(
 /*     0 */     1,    2,    3,    4,    5,    6,    6,    8,   40,   10,
 /*    10 */    11,   43,   44,   45,    8,    7,   26,    9,   10,   20,
 /*    20 */    21,   22,   23,   24,   25,   26,   27,   11,   20,   21,
 /*    30 */    22,   23,   24,   25,   29,   30,   31,   32,   33,   34,
 /*    40 */    11,    7,   27,   26,   39,   40,   41,    1,   30,   31,
 /*    50 */    32,   33,   34,    2,    3,    4,    5,   39,   40,   41,
 /*    60 */    37,   12,   13,   14,   15,   16,   17,   18,   19,   31,
 /*    70 */    32,   33,   34,    7,   38,   40,    7,   39,   40,   41,
 /*    80 */     7,   42,   32,   33,   34,    3,   46,   35,   46,   39,
 /*    90 */    40,   41,   23,   24,   46,   22,   23,   24,   40,   36,
 /*   100 */    46,   43,   44,   45,   40,   46,   46,   43,   44,   45,
);
    const YY_SHIFT_USE_DFLT = -11;
    const YY_SHIFT_MAX = 21;
    static public $yy_shift_ofst = array(
 /*     0 */     8,    8,    8,    8,   73,   69,   69,   -1,   49,   51,
 /*    10 */    69,   66,   82,   34,   16,   46,    6,    0,   29,  -10,
 /*    20 */    15,   17,
);
    const YY_REDUCE_USE_DFLT = -33;
    const YY_REDUCE_MAX = 12;
    static public $yy_reduce_ofst = array(
 /*     0 */     5,   18,   38,   50,  -32,   64,   58,   39,   36,   52,
 /*    10 */    35,   23,   63,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(7, 9, 10, 20, 21, 22, 23, 24, 25, ),
        /* 1 */ array(7, 9, 10, 20, 21, 22, 23, 24, 25, ),
        /* 2 */ array(7, 9, 10, 20, 21, 22, 23, 24, 25, ),
        /* 3 */ array(7, 9, 10, 20, 21, 22, 23, 24, 25, ),
        /* 4 */ array(7, 22, 23, 24, ),
        /* 5 */ array(7, 23, 24, ),
        /* 6 */ array(7, 23, 24, ),
        /* 7 */ array(1, 2, 3, 4, 5, 6, 8, 10, 11, 20, 21, 22, 23, 24, 25, 26, 27, ),
        /* 8 */ array(12, 13, 14, 15, 16, 17, 18, 19, ),
        /* 9 */ array(2, 3, 4, 5, ),
        /* 10 */ array(7, 23, 24, ),
        /* 11 */ array(7, ),
        /* 12 */ array(3, ),
        /* 13 */ array(7, ),
        /* 14 */ array(11, ),
        /* 15 */ array(1, ),
        /* 16 */ array(8, ),
        /* 17 */ array(6, ),
        /* 18 */ array(11, ),
        /* 19 */ array(26, ),
        /* 20 */ array(27, ),
        /* 21 */ array(26, ),
        /* 22 */ array(),
        /* 23 */ array(),
        /* 24 */ array(),
        /* 25 */ array(),
        /* 26 */ array(),
        /* 27 */ array(),
        /* 28 */ array(),
        /* 29 */ array(),
        /* 30 */ array(),
        /* 31 */ array(),
        /* 32 */ array(),
        /* 33 */ array(),
        /* 34 */ array(),
        /* 35 */ array(),
        /* 36 */ array(),
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
        /* 41 */ array(),
        /* 42 */ array(),
        /* 43 */ array(),
        /* 44 */ array(),
        /* 45 */ array(),
        /* 46 */ array(),
        /* 47 */ array(),
        /* 48 */ array(),
        /* 49 */ array(),
        /* 50 */ array(),
        /* 51 */ array(),
        /* 52 */ array(),
        /* 53 */ array(),
        /* 54 */ array(),
        /* 55 */ array(),
        /* 56 */ array(),
        /* 57 */ array(),
        /* 58 */ array(),
        /* 59 */ array(),
        /* 60 */ array(),
        /* 61 */ array(),
        /* 62 */ array(),
        /* 63 */ array(),
        /* 64 */ array(),
        /* 65 */ array(),
        /* 66 */ array(),
        /* 67 */ array(),
        /* 68 */ array(),
        /* 69 */ array(),
        /* 70 */ array(),
        /* 71 */ array(),
        /* 72 */ array(),
        /* 73 */ array(),
);
    static public $yy_default = array(
 /*     0 */   135,  135,  135,   79,  135,  130,  135,  135,  102,   81,
 /*    10 */   135,  135,   90,   87,  135,   77,   86,  135,  135,  135,
 /*    20 */   133,  135,  128,  101,  110,  127,  123,  122,  124,  125,
 /*    30 */   126,  109,  132,   95,   96,   97,   98,   94,  134,  129,
 /*    40 */    99,  131,  121,  100,  114,   85,   84,   88,   91,   93,
 /*    50 */    92,   83,   82,   76,   75,   78,   80,   89,  103,  104,
 /*    60 */   115,   74,  116,  117,  119,  118,  113,  112,  106,  105,
 /*    70 */   107,  108,  111,  120,
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
    const YYNOCODE = 47;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 74;
    const YYNRULE = 61;
    const YYERRORSYMBOL = 28;
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
  '$',             'PIPE',          'ASTERISK',      'QUESTIONMARK',
  'PLUS',          'LBRACE',        'RBRACE',        'INTEGER',     
  'COMMA',         'URLVAR',        'LPAR',          'RPAR',        
  'NAMEDGROUP',    'NONGROUPINGGRP',  'MATCHIFNEXT',   'MATCHIFNOTNEXT',
  'COMMENT',       'NAMEDGROUPDEF',  'POSILOOKBEHIND',  'NEGLOOKBEHIND',
  'DOT',           'DOLLAR',        'CARET',         'ESCSEQ',      
  'ANYCHAR',       'LBRACKET',      'RBRACKET',      'SUB',         
  'error',         'start',         're',            'union',       
  'concat',        'quant',         'group',         'quantifier',  
  'greedy',        'bound',         'qmod',          'term',        
  'char',          'set',           'escaped',       'setitems',    
  'setitem',       'range',       
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= re",
 /*   1 */ "re ::= union",
 /*   2 */ "union ::= concat PIPE union",
 /*   3 */ "union ::= concat",
 /*   4 */ "concat ::= quant concat",
 /*   5 */ "concat ::= quant",
 /*   6 */ "quant ::= group quantifier greedy",
 /*   7 */ "quant ::= group",
 /*   8 */ "quantifier ::= ASTERISK",
 /*   9 */ "quantifier ::= QUESTIONMARK",
 /*  10 */ "quantifier ::= PLUS",
 /*  11 */ "quantifier ::= LBRACE bound RBRACE",
 /*  12 */ "bound ::= INTEGER",
 /*  13 */ "bound ::= INTEGER COMMA",
 /*  14 */ "bound ::= INTEGER COMMA INTEGER",
 /*  15 */ "greedy ::= QUESTIONMARK",
 /*  16 */ "greedy ::=",
 /*  17 */ "group ::= URLVAR",
 /*  18 */ "group ::= LPAR qmod re RPAR",
 /*  19 */ "group ::= term",
 /*  20 */ "group ::= LPAR NAMEDGROUP RPAR",
 /*  21 */ "qmod ::= NONGROUPINGGRP",
 /*  22 */ "qmod ::= MATCHIFNEXT",
 /*  23 */ "qmod ::= MATCHIFNOTNEXT",
 /*  24 */ "qmod ::= COMMENT",
 /*  25 */ "qmod ::= NAMEDGROUPDEF",
 /*  26 */ "qmod ::= POSILOOKBEHIND",
 /*  27 */ "qmod ::= NEGLOOKBEHIND",
 /*  28 */ "qmod ::=",
 /*  29 */ "term ::= DOT",
 /*  30 */ "term ::= DOLLAR",
 /*  31 */ "term ::= CARET",
 /*  32 */ "term ::= char",
 /*  33 */ "term ::= set",
 /*  34 */ "char ::= ESCSEQ escaped",
 /*  35 */ "char ::= ANYCHAR",
 /*  36 */ "char ::= INTEGER",
 /*  37 */ "escaped ::= ANYCHAR",
 /*  38 */ "escaped ::= ESCSEQ",
 /*  39 */ "escaped ::= CARET",
 /*  40 */ "escaped ::= DOLLAR",
 /*  41 */ "escaped ::= DOT",
 /*  42 */ "escaped ::= COMMA",
 /*  43 */ "escaped ::= LBRACKET",
 /*  44 */ "escaped ::= RBRACKET",
 /*  45 */ "escaped ::= PIPE",
 /*  46 */ "escaped ::= LPAR",
 /*  47 */ "escaped ::= RPAR",
 /*  48 */ "escaped ::= QUESTIONMARK",
 /*  49 */ "escaped ::= ASTERISK",
 /*  50 */ "escaped ::= PLUS",
 /*  51 */ "escaped ::= LBRACE",
 /*  52 */ "escaped ::= RBRACE",
 /*  53 */ "escaped ::= SUB",
 /*  54 */ "set ::= LBRACKET setitems RBRACKET",
 /*  55 */ "set ::= LBRACKET CARET setitems RBRACKET",
 /*  56 */ "setitems ::= setitem",
 /*  57 */ "setitems ::= setitem setitems",
 /*  58 */ "setitem ::= range",
 /*  59 */ "setitem ::= char",
 /*  60 */ "range ::= char SUB char",
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
     * @param REInverseyyParser
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
                        $x = new REInverseyyStackEntry;
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
                        $x = new REInverseyyStackEntry;
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
        $yytos = new REInverseyyStackEntry;
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
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 33, 'rhs' => 3 ),
  array( 'lhs' => 33, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 3 ),
  array( 'lhs' => 37, 'rhs' => 1 ),
  array( 'lhs' => 37, 'rhs' => 2 ),
  array( 'lhs' => 37, 'rhs' => 3 ),
  array( 'lhs' => 36, 'rhs' => 1 ),
  array( 'lhs' => 36, 'rhs' => 0 ),
  array( 'lhs' => 34, 'rhs' => 1 ),
  array( 'lhs' => 34, 'rhs' => 4 ),
  array( 'lhs' => 34, 'rhs' => 1 ),
  array( 'lhs' => 34, 'rhs' => 3 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 0 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 40, 'rhs' => 2 ),
  array( 'lhs' => 40, 'rhs' => 1 ),
  array( 'lhs' => 40, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 41, 'rhs' => 3 ),
  array( 'lhs' => 41, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 44, 'rhs' => 1 ),
  array( 'lhs' => 44, 'rhs' => 1 ),
  array( 'lhs' => 45, 'rhs' => 3 ),
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
        2 => 2,
        3 => 3,
        5 => 3,
        7 => 3,
        4 => 4,
        6 => 6,
        8 => 8,
        9 => 8,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        17 => 17,
        18 => 18,
        19 => 19,
        32 => 19,
        33 => 19,
        34 => 19,
        35 => 19,
        36 => 19,
        20 => 20,
        24 => 24,
        25 => 25,
        29 => 29,
        30 => 30,
        31 => 30,
        37 => 37,
        38 => 38,
        39 => 39,
        40 => 40,
        41 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        47 => 47,
        48 => 48,
        49 => 49,
        50 => 50,
        51 => 51,
        52 => 52,
        53 => 53,
        54 => 54,
        55 => 55,
        56 => 56,
        58 => 56,
        57 => 57,
        59 => 59,
        60 => 60,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 348 "REInverseParser.class.y"
    function yy_r0(){ $this->_result = new __ExpressionTemplate($this->yystack[$this->yyidx + 0]->minor);     }
#line 1337 "REInverseParser.class.php"
#line 351 "REInverseParser.class.y"
    function yy_r1(){ 
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1342 "REInverseParser.class.php"
#line 356 "REInverseParser.class.y"
    function yy_r2(){ 
    if(is_array($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
    else {
        $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);
    }
    $this->_retvalue[] = $this->yystack[$this->yyidx + -2]->minor;
    }
#line 1353 "REInverseParser.class.php"
#line 365 "REInverseParser.class.y"
    function yy_r3(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;         }
#line 1356 "REInverseParser.class.php"
#line 368 "REInverseParser.class.y"
    function yy_r4(){ 
    if(is_string($this->yystack[$this->yyidx + -1]->minor) && is_string($this->yystack[$this->yyidx + 0]->minor)) {
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor;
    }
    else {
        $this->_retvalue = new __ExpressionTemplate();
        $this->_retvalue->addExpression($this->yystack[$this->yyidx + -1]->minor);
        $this->_retvalue->addExpression($this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1368 "REInverseParser.class.php"
#line 381 "REInverseParser.class.y"
    function yy_r6(){
    //By default, the expression is mandatory (optional means that the expression 
    //could be ignored if contained variables are not used when building the url.
    $optional = false;
    if($this->yystack[$this->yyidx + -1]->minor == 0 && $this->yystack[$this->yyidx + -2]->minor instanceof __ExpressionTemplate && count($this->yystack[$this->yyidx + -2]->minor->getVariables()) > 0) {
        $this->yystack[$this->yyidx + -1]->minor = 1;
        $optional = true;
        
    }
    if($this->yystack[$this->yyidx + -1]->minor > 0) {
        if($optional || !is_string($this->yystack[$this->yyidx + -2]->minor)) {
            $this->_retvalue = new __ExpressionTemplate();
            $this->_retvalue->setOptional(true);
            for($i = 0; $i < $this->yystack[$this->yyidx + -1]->minor; $i++) {
                $this->_retvalue->addExpression($this->yystack[$this->yyidx + -2]->minor);
            }
        }
        else {
            $this->_retvalue = "";
            for($i = 0; $i < $this->yystack[$this->yyidx + -1]->minor; $i++) {
                $this->_retvalue .= $this->yystack[$this->yyidx + -2]->minor;
            }
        }
    }
    else {
        $this->_retvalue = "";
    }
    }
#line 1398 "REInverseParser.class.php"
#line 412 "REInverseParser.class.y"
    function yy_r8(){ $this->_retvalue = 0;         }
#line 1401 "REInverseParser.class.php"
#line 414 "REInverseParser.class.y"
    function yy_r10(){ $this->_retvalue = 1;         }
#line 1404 "REInverseParser.class.php"
#line 415 "REInverseParser.class.y"
    function yy_r11(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;         }
#line 1407 "REInverseParser.class.php"
#line 418 "REInverseParser.class.y"
    function yy_r12(){ $this->_retvalue = (int)$this->yystack[$this->yyidx + 0]->minor;        }
#line 1410 "REInverseParser.class.php"
#line 419 "REInverseParser.class.y"
    function yy_r13(){ $this->_retvalue = (int)$this->yystack[$this->yyidx + -1]->minor;        }
#line 1413 "REInverseParser.class.php"
#line 420 "REInverseParser.class.y"
    function yy_r14(){ $this->_retvalue = (int)$this->yystack[$this->yyidx + -2]->minor;        }
#line 1416 "REInverseParser.class.php"
#line 427 "REInverseParser.class.y"
    function yy_r17(){ 
    $this->_retvalue = new __ExpressionTemplate(new __ExpressionVariable($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1421 "REInverseParser.class.php"
#line 430 "REInverseParser.class.y"
    function yy_r18(){ 
    if ($this->yystack[$this->yyidx + -2]->minor == "comment") {
        $this->_retvalue = null;
    }
    else {
        if( $this->yystack[$this->yyidx + -2]->minor instanceof __NamedGroup ) {
            $this->yystack[$this->yyidx + -2]->minor->setPattern($this->yystack[$this->yyidx + -1]->minor);
            $this->_namedgroups[$this->yystack[$this->yyidx + -2]->minor->getName()] = $this->yystack[$this->yyidx + -2]->minor;
        }
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
    }
#line 1435 "REInverseParser.class.php"
#line 442 "REInverseParser.class.y"
    function yy_r19(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;        }
#line 1438 "REInverseParser.class.php"
#line 443 "REInverseParser.class.y"
    function yy_r20(){
    preg_match('/\?P\=([A-Za-z_][A-Za-z_0-9]*)/', $this->yystack[$this->yyidx + -1]->minor, $matched);
    $group_name = $matched[1];
    if(key_exists($group_name, $this->_namedgroups)) {
        $named_group = $this->_namedgroups[$group_name];
        $this->_retvalue = $named_group->getPattern();
    }
    else {
        $this->_retvalue = null;
    }
    }
#line 1451 "REInverseParser.class.php"
#line 463 "REInverseParser.class.y"
    function yy_r24(){ $this->_retvalue = "comment";     }
#line 1454 "REInverseParser.class.php"
#line 464 "REInverseParser.class.y"
    function yy_r25(){ 
    preg_match('/\?P\<([A-Za-z_][A-Za-z_0-9]*)\>/', $this->yystack[$this->yyidx + 0]->minor, $matched);
    $group_name = $matched[1];
    $this->_retvalue = new __NamedGroup($group_name); 
    }
#line 1461 "REInverseParser.class.php"
#line 480 "REInverseParser.class.y"
    function yy_r29(){ $this->_retvalue = "a";      }
#line 1464 "REInverseParser.class.php"
#line 481 "REInverseParser.class.y"
    function yy_r30(){ $this->_retvalue = "";       }
#line 1467 "REInverseParser.class.php"
#line 492 "REInverseParser.class.y"
    function yy_r37(){
    switch($this->yystack[$this->yyidx + 0]->minor) {
        case '$this->yystack[$this->yyidx + 0]->minor': //any middle char
             $this->_retvalue = "a";
             break;
        case '$this->_retvalue': //begin of a word
             $this->_retvalue = "a";
             break;
        case 'Z': //end of a word
             $this->_retvalue = "a";
             break;
        case 'd': //any decimal digit
             $this->_retvalue = "1";
             break;
        case 'D': //any non decimal digit
             $this->_retvalue = "a";
             break;
        case 's': //any white character
             $this->_retvalue = " ";
             break;
        case 'S': //any non white character
             $this->_retvalue = "a";
             break;
        case 'w': //any word character
             $this->_retvalue = "a";
             break;
        case 'W': //any non word character
             $this->_retvalue = ",";
             break;
        case 't':
        case 'n':
        case 'r':
        case 'f':
        case 'a':
        case 'e':
        case 'l':
        case 'u':
        case 'L':
        case 'U':
        case 'E':
        case 'Q':
            $this->_retvalue = "\\" . $this->yystack[$this->yyidx + 0]->minor;
            break;       
        default:
            $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
            break;
    }

    }
#line 1518 "REInverseParser.class.php"
#line 542 "REInverseParser.class.y"
    function yy_r38(){ $this->_retvalue = "\\";     }
#line 1521 "REInverseParser.class.php"
#line 543 "REInverseParser.class.y"
    function yy_r39(){ $this->_retvalue = "^";      }
#line 1524 "REInverseParser.class.php"
#line 544 "REInverseParser.class.y"
    function yy_r40(){ $this->_retvalue = "$";      }
#line 1527 "REInverseParser.class.php"
#line 545 "REInverseParser.class.y"
    function yy_r41(){ $this->_retvalue = ".";      }
#line 1530 "REInverseParser.class.php"
#line 546 "REInverseParser.class.y"
    function yy_r42(){ $this->_retvalue = ",";      }
#line 1533 "REInverseParser.class.php"
#line 547 "REInverseParser.class.y"
    function yy_r43(){ $this->_retvalue = "[";      }
#line 1536 "REInverseParser.class.php"
#line 548 "REInverseParser.class.y"
    function yy_r44(){ $this->_retvalue = "]";      }
#line 1539 "REInverseParser.class.php"
#line 549 "REInverseParser.class.y"
    function yy_r45(){ $this->_retvalue = "|";      }
#line 1542 "REInverseParser.class.php"
#line 550 "REInverseParser.class.y"
    function yy_r46(){ $this->_retvalue = "(";      }
#line 1545 "REInverseParser.class.php"
#line 551 "REInverseParser.class.y"
    function yy_r47(){ $this->_retvalue = ")";      }
#line 1548 "REInverseParser.class.php"
#line 552 "REInverseParser.class.y"
    function yy_r48(){ $this->_retvalue = "?";      }
#line 1551 "REInverseParser.class.php"
#line 553 "REInverseParser.class.y"
    function yy_r49(){ $this->_retvalue = "*";      }
#line 1554 "REInverseParser.class.php"
#line 554 "REInverseParser.class.y"
    function yy_r50(){ $this->_retvalue = "+";      }
#line 1557 "REInverseParser.class.php"
#line 555 "REInverseParser.class.y"
    function yy_r51(){ $this->_retvalue = "{";      }
#line 1560 "REInverseParser.class.php"
#line 556 "REInverseParser.class.y"
    function yy_r52(){ $this->_retvalue = "}";      }
#line 1563 "REInverseParser.class.php"
#line 557 "REInverseParser.class.y"
    function yy_r53(){ $this->_retvalue = "-";      }
#line 1566 "REInverseParser.class.php"
#line 582 "REInverseParser.class.y"
    function yy_r54(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor[0];     }
#line 1569 "REInverseParser.class.php"
#line 583 "REInverseParser.class.y"
    function yy_r55(){ 
    $curr_char = 33; //!
    $found = false;
    while($found == false) {
        if(in_array(chr($curr_char), $this->yystack[$this->yyidx + -1]->minor)) {
            $curr_char = $curr_char + 1;
        }
        else {
            $found = true;
        }
    }
    $this->_retvalue = chr($curr_char);
    }
#line 1584 "REInverseParser.class.php"
#line 597 "REInverseParser.class.y"
    function yy_r56(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1587 "REInverseParser.class.php"
#line 598 "REInverseParser.class.y"
    function yy_r57(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1590 "REInverseParser.class.php"
#line 602 "REInverseParser.class.y"
    function yy_r59(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1593 "REInverseParser.class.php"
#line 605 "REInverseParser.class.y"
    function yy_r60(){ 
    $min = ord($this->yystack[$this->yyidx + -2]->minor);
    $max = ord($this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue = array();
    if($min > $max) {
        $tmp = $min;
        $min = $max;
        $max = $tmp;
    }
    for($i = $min; $i <= $max; $i++) {
        $this->_retvalue[] = chr($i);
    }
    }
#line 1608 "REInverseParser.class.php"

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
        //REInverseyyStackEntry $yymsp;            /* The top of the parser's stack */
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
                $x = new REInverseyyStackEntry;
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
#line 287 "REInverseParser.class.y"

    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Error on regular expression: Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
#line 1728 "REInverseParser.class.php"
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
#line 344 "REInverseParser.class.y"

    return $this->_result;
#line 1750 "REInverseParser.class.php"
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
            $x = new REInverseyyStackEntry;
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
<?

/**
 * JSON class. output zichzelf naar een valide JSON object.
 * @package pork
 */ 
define('SERVICES_JSON_SLICE',   1);
define('SERVICES_JSON_IN_STR',  2);
define('SERVICES_JSON_IN_ARR',  3);
define('SERVICES_JSON_IN_OBJ',  4);
define('SERVICES_JSON_IN_CMT', 5);
define('SERVICES_JSON_LOOSE_TYPE', 16);
define('SERVICES_JSON_SUPPRESS_ERRORS', 32);

class jsObject
{
	var $xml, $properties;

	function __construct()
	{
		$this->properties = array();
	}

	function __set($key, $value)
	{
		$this->properties[$key] = $value;
	}

	function __get($key)
	{
		if(array_key_exists($key,$this->properties))
		{
			return ($this->properties[$key]);
		}		
	}

	function display()
	{
		foreach ($this->properties as $key=>$val)
		{
		
			$output .= ($output != '') ? ', ' : '';
			$output .= "{$key}: {$this->encode($val)}";
		}
		die("{{$output}}");


   function utf162utf8($utf16)
    {
                        
                return chr((0xF0 & (ord($utf8{0}) << 4))
                         | (0x0F & (ord($utf8{1}) >> 2)))
                     . chr((0xC0 & (ord($utf8{1}) << 6))
                         | (0x7F & ord($utf8{2})));
        }

        
        return '';
    }

    function encode($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'integer':
                return (int) $var;

            case 'double':
            case 'float':
                return (float) $var;

            case 'string':
                
                $ascii = '';
                $strlen_var = strlen($var);

               /*
                * Iterate over every character in the string,
                * escaping with a slash or encoding to UTF-8 where necessary
                */
                for ($c = 0; $c < $strlen_var; ++$c) {

                    $ord_var_c = ord($var{$c});

                    switch (true) {
                        case $ord_var_c == 0x08: $ascii .= '\b'; break;
                        case $ord_var_c == 0x09: $ascii .= '\t'; break;
                        case $ord_var_c == 0x0A:                           $ascii .= '\n';
                            break;
                        case $ord_var_c == 0x0C:
                            $ascii .= '\f';
                            break;
                        case $ord_var_c == 0x0D:
                            $ascii .= '\r';
                            break;

                        case $ord_var_c == 0x22:
                        case $ord_var_c == 0x2F:
                        case $ord_var_c == 0x5C:
                            
                            $ascii .= '\\'.$var{$c};
                            break;

                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                            
                            $ascii .= $var{$c};
                            break;

                        case (($ord_var_c & 0xE0) == 0xC0):
                            
                            
                            $char = pack('C*', $ord_var_c, ord($var{$c + 1}));
                            $c += 1;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF0) == 0xE0):
                            
                            
                            $char = pack('C*', $ord_var_c,ord($var{$c + 1}),ord($var{$c + 2}));
                            $c += 2;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF8) == 0xF0):
                            $char = pack('C*', $ord_var_c,ord($var{$c + 1}),ord($var{$c + 2}),ord($var{$c + 3}));
                            $c += 3;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFC) == 0xF8):
                            $char = pack('C*', $ord_var_c,ord($var{$c + 1}),ord($var{$c + 2}),ord($var{$c + 3}),ord($var{$c + 4}));
                            $c += 4;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFE) == 0xFC):
                            $char = pack('C*', $ord_var_c,ord($var{$c + 1}),ord($var{$c + 2}),ord($var{$c + 3}),ord($var{$c + 4}),ord($var{$c + 5}));
                            $c += 5;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;
                    }
                }

                return '"'.$ascii.'"';

            case 'array':
           
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                    $properties = array_map(array($this, 'name_value'),   array_keys($var),   array_values($var));

                 
                    return '{' . join(',', $properties) . '}';
                }

                
                $elements = array_map(array($this, 'encode'), $var);

                

                return '[' . join(',', $elements) . ']';

            case 'object':
                $vars = get_object_vars($var);
                $properties = array_map(array($this, 'name_value'), array_keys($vars), array_values($vars));

                foreach($properties as $property) 
				{
                  
                }

                return '{' . join(',', $properties) . '}';

            default:
                return ($this->use & SERVICES_JSON_SUPPRESS_ERRORS) ? 'null' : gettype($var)." can not be encoded as JSON string";
        }
    }

    function name_value($name, $value)
    {
        $encoded_value = $this->encode($value);

     
        return $this->encode(strval($name)) . ':' . $encoded_value;
    }

    function reduce_string($str)
    {
        $str = preg_replace(array(
                '#^\s*//(.+)$#m',                
                '#^\s*/\*(.+)\*/#Us',                
                '#/\*(.+)\*/\s*$#Us'
            ), '', $str);

        
        return trim($str);
    }

	 function utf82utf16($utf8)
     {
         if(function_exists('mb_convert_encoding')) { // oh please oh please oh please oh please oh please
             return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
         } 
         switch(strlen($utf8)) {
             case 1: // this case should never be reached, because we are in ASCII range
                 return $utf8; 
             case 2: // return a UTF-16 character from a 2-byte UTF-8 char
                 return chr(0x07 & (ord($utf8{0}) >> 2)) . chr((0xC0 & (ord($utf8{0}) << 6)) | (0x3F & ord($utf8{1})));
             case 3: // return a UTF-16 character from a 3-byte UTF-8 char
                 return chr((0xF0 & (ord($utf8{0}) << 4)) | (0x0F & (ord($utf8{1}) >> 2))) . chr((0xC0 & (ord($utf8{1}) << 6)) | (0x7F & ord($utf8{2})));
         } 
         // ignoring UTF-32 for now, sorry
         return '';
     }


}
    
?>

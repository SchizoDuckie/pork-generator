<?

/*
 * FormValidator class. Valideert form input a.d.h.v. regexes
 * @package pork
 */

class FormValidator
{
	static function Validate($object)
	{
		global $_TPL;
		$validations = Array(
			'date' => "^[0-9]{1,2}[-/][0-9]{1,2}[-/][0-9]{4}$",
			'email' => "^[0-9a-zA-Z._-]*[@][0-9a-zA-Z._-]*[.][a-z]{2,4}$", 
			'amount' => "^[-]?[0-9]+$",
			'number' => "^[-]?[0-9,]+$",
			'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+$",
			'not_empty' => "[a-z0-9A-Z]+",
			'words' => "^[A-Za-z]+[A-Za-z \\s]*$",
			'phone' => "^[0-9\-]{10,}$",
			'zipcode' => "^[1-9][0-9]{3}[ ]?[a-zA-Z]{2}$",
			'plate' => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}$",
			'price' => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?$",
			'2digitopt' => "^\d+(\,\d{2})?$",
			'2digitforce' => "^\d+\,\d\d$",
			'anything' => "^[\d\D]{1,}$");
		$classname = get_class($object);
		
		if(array_key_exists($classname, $_SESSION['validators']))
		{
			$form = $_SESSION['validators'][$classname];
			$_TPL['js']->script .= 'var Validator = new FormValidator();';
			$_TPL['js']->editorStatus = '';
			$returns = array();
			foreach($form->validations as $key=>$val)
			{
				$return = array();
				$elementvalidations = (strpos($val, '|') !== false) ? explode('|', $val) : array($val);
				if(strpos ($val, "not_empty") !== false || $_POST[$key] != '')
				{
					foreach($elementvalidations as $validation)
					{
						$return[] = preg_match("!".$validations[$validation]."!", $_POST[$key]) ? true : false;
					}
				}
				
				$_TPL['js']->script .= array_search(false, $return, true) !== false ? "Validator.addMessage('{$classname}_{$key}_Element', '{$val}');" : "Validator.clearMessage('{$classname}_{$key}_Element');";
				$returns[] = (array_search(false, $return, true) === false);	
			}	
			if(array_search(false, $returns, true) !== false) $_TPL['js']->display(); 
		}	
		else
		{
		
		}
		return true;
	}
}
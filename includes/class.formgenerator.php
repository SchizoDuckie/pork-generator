<?php


class formGenerator // generates a form from a dbObject
{
	var $targetObject, $elements, $formType, $displayType, $title;
	function __construct($targetObject,$title='Wijzigen', $displayType = 'formWrapper') 
	{
		$this->title =$title;
		$this->targetObject = $targetObject;
		$this->formType = 'POST';
		$this->displayType = $displayType;
	}

	function __get($property)
	{
		if (array_key_exists($property, $this->elements)) 
		{
			return ($this->elements[$property]);
		}
		else
		{
			return  false;
		}
	}


	function __set($property, $object)
	{
		if (is_subclass_of($object, 'formElement')) 
		{
			$object->parent = $this->targetObject;
			$object->property = $property;
			$this->elements[$property] = $object;
		}
	}

	function setType($formtype='POST')
	{
		switch (strtoupper($formtype))
		{
			case 'POST':
			case 'GET':
			case 'AJAX':
				$this->formType = strtoupper($formtype);
			break;
			default:
				$this->formType = 'POST';
			break;

		}
	}

	function display($targetscript='index.php', $params=array())
	{
		$cat = (!$cat) ?$_GET['cat'] : $cat;
		$action = (!$action) ? $_GET['action'] : $action;
		$parameters = array_merge(array('cat'=> $cat, 'action' => $action, 'ID' => $this->targetObject->ID, 'parentClass'=>get_class($this->targetObject)), $params);
		$wrapper = new $this->displayType($this->formType, $targetscript, $this->title,  $parameters);
		$wrapper->title = $this->title;
		$output .= $wrapper->init();
		foreach ($this->elements as $key => $obj)
		{
			$output .= $wrapper->wrap($obj->getLabel(), $obj->display());
		}
		$output .= $wrapper->finalize();
		return($output);

	}

}



class formWrapper // vouwt een <form> met daarin een <table> om een formGenerator object heen, 
{
	var $output, $title;
	function __construct($type, $targetscript, $title='', $parameters=array())
	{
		$this->title = $title;
		$this->output = "<form method='{$type}' action='{$targetscript}' enctype='multipart/form-data'>";
		if ($this->title != '')
		{
			$this->output .= "<fieldset><legend>{$this->title}</legend>";
		}
		foreach ($parameters as $key=>$val)
		{	
			$this->output .= "<input type='hidden' name='{$key}' value='{$val}'>";
		}
	}

	function init()
	{
		return ($this->output.'<table>');
	}

	function wrap($label, $value)
	{
		return("<tr><td valign='top'>{$label}</td><td>{$value}</td></tr>");
	}

	function finalize()
	{
		if ($this->title != '')
		{
			$out= '</fieldset>';
		}
		else
		{
			$out = "";
		}
		return ("<tr><td colspan='2' align='right'><input type='submit' class='submit' value='Save'></td></tr></table>{$out}</form>");

	}
}

class AjaxWrapper extends formWrapper// vouwt een <form> met daarin een <table> om een formGenerator object heen, 
{
	var $output, $title;
	function __construct($type, $targetscript, $title='', $parameters=array())
	{
		$this->title = $title;
		$formid = (array_key_exists('parentClass', $parameters)) ? "{$parameters['parentClass']}_{$parameters['ID']}" : "form_".rand(10000, 15000);
		$this->output = "<form method='{$type}' name='$formid' action='{$targetscript}' enctype='multipart/form-data' onsubmit='new iframe(this); return false;'>";
		if ($this->title != '')
		{
			$this->output .= "<fieldset><legend>{$this->title}</legend>";
		}
		foreach ($parameters as $key=>$val)
		{	
			if($key == 'ID' && $val == '') { $val = 'new'; }
			$this->output .= "<input type='hidden' name='{$key}' value='{$val}'>";
		}
	}
}

class formElement
{
	var $element, $validation, $value, $property, $parent;
	
	function __construct($label, $validation='')
	{
		$this->label = $label;
		$this->validation = $validation;
	}

	function getLabel()
	{
		return ("<label for='{$this->property}_Element'>{$this->label}</label>");
	}

	function display()
	{
		$value = htmlentities($this->parent->{$this->property});
		return("<input type='text' name='{$this->property}' id='{$this->property}_Element' value='{$value}'>");
	}
}

class dateEditor extends formElement
{
	function display()
	{
		global $_TPL;
		$_TPL['scripts'][]= './includes/popkalender.js';
		return ("<input type='text' name='{$this->property}' id='{$this->property}_Element' value='{$this->parent->{$this->property}}'>&nbsp;<img src='./images/calendar.gif' onclick='show_calendar(\"{$this->property}\", document.getElementById(\"{$this->property}_Element\").value, \"{$this->property}_Element\", this);' alt='Kalender' style='position:relative;' />");		
	}
}

class hiddenInput extends formElement
{
	function display()
	{
		return ("<input type='hidden' name='{$this->property}' id='{$this->property}_Element' value='{$this->parent->{$this->property}}'>");
	}
}

class textInput extends formElement
{
	function display($width=false)
	{
		$value = htmlspecialchars($this->parent->{$this->property}, ENT_QUOTES);
		$width = ($width == false) ? "" : " style='width:{$width}px'";
		return ("<input type='text' name='{$this->property}' id='{$this->property}_Element' value='{$value}'{$width}>");
	}
}

class Select extends formElement
{
	var $default, $selected, $properties, $property, $validation;

	function __construct($label, $properties=array(), $selected='', $validation='')
	{
		$this->label = $label;
		$this->properties = $properties;
		$this->validation = $validation;
		$this->selected = $selected;

	}
	

	function display()
	{

		$prop = $this->property;
		$this->selected = $this->parent->$prop;
		foreach ($this->properties as $key=>$val)
		{
			$selected = ($this->selected == $key) ? " selected" : '';
			$options .= "<option value='{$key}'{$selected}>{$val}</option>";
		}
		$output .= "<select name='{$this->property}' id='{$this->property}_Element'>{$options}</select>";
		return($output);
	}
}

class ajaxSelect extends Select
{
	// still working on this :P
}

class enumSelect extends Select
{
	function display()
	{
		
		$description = $this->parent->db->fetchRow("describe {$this->parent->databaseInfo->table} ".$this->parent->fieldForProperty($this->property));
		ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$description->Type,$fieldTypeSplit);
		
		$fields = explode("','", substr($fieldTypeSplit[3],1,-1));

		foreach ($fields as $key=>$values)
		{
			if ($value == $values) $this->selected = $value;
			$this->properties[$values] = $values;
		}
		return(parent::display());
	}
	
	// still working on this :P
}

class querySelect extends Select
{
	// still working on this :P
}

class setSelect extends Select
{
	// still working on this :P
}

class checkBox extends formElement
{
	// still working on this :P
}

class checkboxGroup extends setSelect
{

}

class radioInput extends formElement
{

}

class radioArrayInput extends formElement
{

}

class integerInput extends formElement
{

}

class passwordInput extends formElement
{

}
class passwordConfirmInput extends formElement
{

}

class textAreaInput extends formElement
{

}

class fileInput extends formElement
{
	function display()
	{
		$output .= "Huidige bestand: <a href='../{$this->parent->{$this->property}}'>{$this->parent->{$this->property}}</a><br>
		<input type='file' name='{$this->property}' id='{$this->property}_Element'>";
		return($output);
	}	
}

class imageInput extends fileInput
{
	function display()
	{
	
		$output .= "<input type='file' name='{$this->property}' id='{$this->property}_Element'>";
			if ($this->parent->ID != '')
		{
			if ($this->parent->hasProperty('Thumb'))
			{
				$thumbsrc = "../".$this->parent->Thumb;
			}
			$size = getImageSize($thumbsrc);
			$output .= "<div style='float:left;'>Huidige afbeelding:<br><img src='../{$this->parent->{$this->property}}' {$size[3]}'></div>";
		}
		return($output);
	}		
}


class browseImage extends formElement
{

}

class fckInput extends formElement
{
	var $width, $height;
	function __construct($label, $width='550', $height='250')
	{
		$this->label = $label;
		$this->width = $width;
		$this->height = $height;
	}

	function getLabel() {
		return("<label for='{$this->property}_{$this->parent->ID}'>{$this->label}</label>");
	}
	function display()
	{
		global $js, $_TPL;
		$scripts = './includes/fckeditor/fckeditor.js';
		$script = "function {$this->property}_{$this->parent->ID}_init() { 
			var {$this->property}_{$this->parent->ID}FCKeditor = new FCKeditor( '{$this->property}_{$this->parent->ID}_Element' ,  '{$this->width}', '{$this->height}') ;
			{$this->property}_{$this->parent->ID}FCKeditor.BasePath	= './includes/fckeditor/';
			{$this->property}_{$this->parent->ID}FCKeditor.ToolbarSet = 'Default';
			{$this->property}_{$this->parent->ID}FCKeditor.ReplaceTextarea() ;
			} ";
		if (strtolower(get_class($js)) == 'jsobject')
		{
			$js->scripts[] = $scripts;
			$js->script .= $script."{$this->property}_{$this->parent->ID}_init();";
		}
		else
		{
			$_TPL['scripts'][] = $scripts;
			$_TPL['script'] .= $script;
			$_TPL['onload'] .= "{$this->property}_{$this->parent->ID}_init();";
		}
		return ("<textarea name='{$this->property}' id='{$this->property}_{$this->parent->ID}_Element'>{$this->parent->{$this->property}}</textarea>");
	}
}




?>
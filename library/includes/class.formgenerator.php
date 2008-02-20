<?

/**
 *  Formgenerator. Genereert forms die rechtstreeks gelinked zitten aan een dbObject.
 *  @package pork
 */

class formGenerator // genereert een <form> vanuit een databaseObject
{
	var $targetObject, $elements, $formType, $displayType, $title, $validations;
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
		if (is_subclass_of($object, 'Element') || get_class($object) == 'Element') 
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

	function display($targetscript='index.php', $options=false)
	{
		$cat = (!$cat) ?$_GET['cat'] : $cat;
		$action = (!$action) ? $_GET['action'] : $action;

		$wrapper = new $this->displayType($this->formType, $targetscript, $this->title,  $options);
		$wrapper->title = $this->title;
		$output .= $wrapper->init();
		foreach ($this->elements as $key => $obj)
		{
			$output .= (is_object($obj) &&  array_search ('getLabel', get_class_methods($obj)) !== false) ? $wrapper->wrap($obj->getLabel(), $obj->display(), $this->validations[$key]) : $wrapper->wrapcustom($obj->display());
		}

//(is_array($this->validations) && array_key_exists( $key, $this->validations ))

		$output .= $wrapper->finalize(sizeof($this->validations) > 0);
		return($output);

	}

	function addValidations($validations) 
	{
		$this->validations = $validations;
		$_SESSION['validators'][get_class($this->targetObject)] = $this;
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

	function wrap($label, $value, $valid)
	{
		$stripped = substr($valid, 0, 10-count($valid));
		$valid = ($valid && $stripped == 'not_empty' ) ? "<span class='mandatory'> * </span>" : "";
		return("<tr><td valign='top'>{$label}{$valid}</td><td>{$value}</td></tr>");
	}

	function wrapcustom($value) {
		return("<tr><td colspan='2'>{$value}</td></tr>");
	}

	function finalize($validations)
	{
		if ($this->title != '')
		{
			$out= '</fieldset>';
		}
		else
		{
			$out = "";
		}

		$mandatoryMessage = ($validations) ? "Velden met een * zijn verplicht!" : "";
		return ("<tr><td colspan='2' align='right'><input type='submit' class='submit' value='Save'></td></tr><tr><td><br/><span class='mandatory'>{$mandatoryMessage}</span></td></tr></table>{$out}</form>");

	}
}

class noFormWrapper extends formWrapper
{
	var $width;
	function __construct($type, $targetscript, $title='', $width='', $parameters=array(), $submitText=false)
	{
		$this->title = $title;
		$this->submitText = $submitText != false ? $submitText : 'Save';
		$this->width = $width;
	}

	function init()
	{
		return ($this->output."<table width='{$this->width}'>");
	}

	function finalize($validations)
	{
		return ("<tr></tr></table>{$out}");
	}
}

class AjaxWrapper extends formWrapper// vouwt een <form> met daarin een <table> om een formGenerator object heen, 
{
	var $output, $title;
	function __construct($type, $targetscript, $title='', $options=false)
	{
		$this->title = $title;
		$options = (!$options) ? '' : ", {$options}";

		$this->output = "<form method='{$type}' name='{$formid}' action='{$targetscript}' enctype='multipart/form-data' onsubmit='new Ajax(this.action, {postBody: this.toQueryString(){$options}}).request(); return false;'>";
		if ($this->title != '')
		{
			$this->output .= "<fieldset><legend>{$this->title}</legend>";
		}
	}
}

class IframeWrapper extends formWrapper// vouwt een <form> met daarin een <table> om een formGenerator object heen, 
{
	var $output, $title;
	function __construct($type, $targetscript, $title='', $options=false)
	{
		$this->title = $title;
		$options = (!$options) ? '' : ", {{$options}}";

		$this->output = "<form method='{$type}' name='{$formid}' action='{$targetscript}' enctype='multipart/form-data' onsubmit='new iframe(this{$options}); return false;'>";
		if ($this->title != '')
		{
			$this->output .= "<fieldset><legend>{$this->title}</legend>";
		}
	}

	function finalize($hasArray) 
	{
		$output = parent::finalize($hasArray);
		$output .= "<div id='editorStatus'>&nbsp;</div>";
		return($output);
	}
}


class Element {
	var $element, $validation, $value, $property, $parent;
	
	function __construct($label='', $value='')
	{
		$this->label = $label;
		$this->value= $value;
	}
	
	function getLabel() 
	{
		return($this->label);	
	}

	function display()
	{		
		return($this->value);
	}

}


class formElement extends Element
{
	function __construct($label, $width=false)
	{
		$this->label = $label;
		$this->width = $width;
	}

	function getLabel()
	{
		return ("<label for='".get_class($this->parent)."_{$this->property}_Element'>{$this->label}</label>");
	}
	
	function display()
	{
		$value = htmlentities($this->parent->{$this->property});
		return("<input type='text' class='text' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$value}'>");
	}
}

class dateEditor extends formElement
{
	function display()
	{
		$val = preg_match("!^[0\-]{10}$!", $this->parent->{$this->property}) ? "" : $this->parent->{$this->property};
		//$val = $this->parent->{$this->property};
		return ("<input type='text' class='date' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$val}'>
		 <script type='text/javascript'>window.addEvent('domready', function() { myCal = new Calendar({ ".get_class($this->parent)."_{$this->property}_Element: 'd-m-Y' }); }); </script>");		
	}
}
class dateInput extends dateEditor
{
}

class hiddenInput extends formElement
{
	function display()
	{
		return ("<input type='hidden' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$this->parent->{$this->property}}'>");
	}
}

class textInput extends formElement
{
	function display()
	{
		$value = htmlspecialchars($this->parent->{$this->property}, ENT_QUOTES);
		$width = ($this->width == false) ? "" : " style='width:{$this->width}px'";
		return ("<input type='text' class='text' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$value}'{$width}>");
	}
}

class CustomtextInput extends Element
{
	var $name, $width;
	function __construct($label='', $value='', $name='', $width=false)
	{
		$this->label = $label;
		$this->value = $value;
		$this->name = $name;
		$this->width = $width;
	}

	function display()
	{
		$value = htmlspecialchars($this->parent->{$this->property}, ENT_QUOTES);
		$width = ($this->width == false) ? "" : " style='width:{$this->width}px'";
		return ("<input type='text' class='text' name='{$this->name}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$this->value}'{$width}>");
	}
}

class passwordInput extends formElement
{
	function display()
	{
		$value ='';
		return("<input type='password' value='' name='{$this->property}'>");
	}
}

class Select extends formElement
{
	var $default, $selected, $properties, $property, $validation, $toelichting;

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
		if($this->parent->$prop != '')
		{
			$this->selected = $this->parent->$prop;
		}
		foreach ($this->properties as $key=>$val)
		{
			$selected = ($this->selected == $key) ? " selected" : '';
			$options .= "<option value='{$key}'{$selected}>{$val}</option>";
		}
		$output .= "<select name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element'>{$options}</select> {$this->toelichting}";
		return($output);
	}
}

class dropdownSelect extends Select
{
	var $label, $classname, $properties=array();
	function __construct($label, $classname)
	{
		$this->label = $label;
		$this->classname = $classname;
	}

	function display()
	{
		$value = $this->classname;
		$result = dbObject::Search($this->classname, array(), array( "ORDER BY ".$this->classname) );
		foreach( $result as $class )
		{
			$this->properties[$class->ID] = $class->$value;
		}

		foreach ($this->properties as $key=>$val)
		{
			$options .= "<option value='{$val}'>{$val}</option>";
		}
		$value = htmlspecialchars($this->parent->{$this->property}, ENT_QUOTES);
		$output .= ("<input type='text' class='text' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$value}' {$width}><br/>");

		$output .= "Eerder gekozen {$this->classname}: <select name='dropdown_for_{$this->property}' id='dropdown_for_{$this->property}' onchange='".get_class($this->parent)."_{$this->property}_Element.value=this.value'>{$options}</select>";
		return($output);
	}
}

class ajaxSelect extends Select
{
	var $label, $onchange;
	function __construct($label, $onchange='')
	{
		$this->label = $label;
		$this->onchange = $onchange;
	}

	function display() 
	{
		$description = dbConnection::getInstance()->fetchRow("describe {$this->parent->databaseInfo->table} ".$this->parent->fieldForProperty($this->property));
		ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$description->Type,$fieldTypeSplit);
		
		$fields = explode("','", substr($fieldTypeSplit[3],1,-1));
		$this->properties[] = 'Maak uw keuze';

		foreach ($fields as $key=>$values)
		{
			if ($value == $values) $this->selected = $value;
			$this->properties[$values] = $values;
		}

		$prop = $this->property;
		if($this->parent->$prop != '')
		{
			$this->selected = $this->parent->$prop;
		}
		foreach ($this->properties as $key=>$val)
		{
			$selected = ($this->selected == $key) ? " selected" : '';
			$options .= "<option value='{$key}'{$selected}>{$val}</option>";
		}
		$output .= "<select name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' onchange='{$this->onchange}'>{$options}</select>";
		return($output);
	}
}

class enumSelect extends Select
{
	function display()
	{
		
		$description = dbConnection::getInstance()->fetchRow("describe {$this->parent->databaseInfo->table} ".$this->parent->fieldForProperty($this->property));
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
	function display()
	{
		
		$description = dbConnection::getInstance()->fetchRow("describe {$this->parent->databaseInfo->table} ".$this->parent->fieldForProperty($this->property));
		ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$description->Type,$fieldTypeSplit);
		$fields = explode("','", substr($fieldTypeSplit[3],1,-1));

		foreach ($fields as $key=>$values)
		{
			if ($value == $values) $this->selected = $value;
			$this->properties[$values] = $values;
		}
		
		if($description->Default != '' && $value == '')
		{
			$this->selected = $description->Default;
		}
		return(parent::display());
	}
	
	// still working on this :P
}

class checkBox extends formElement
{
	
	function display($width=false)
	{
		$checked  = $this->parent->{$this->property} == '1' ? " checked" : "";
		return ("<input type='checkbox' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' value='{$this->parent->{$this->property}}'{$checked}{$width}>");
	}
}

class checkboxGroup extends setSelect
{

}

class radioInput extends formElement
{

}

class AjaxWebserviceTabel extends Element
{
	var $properties, $width, $title, $page, $onclick, $params, $nextpageloc;

	function __construct($properties, $page, $nextpageloc, $title='', $width='100%', $onclick='', $params=array())
	{
		$this->properties = $properties;
		$this->page = $page;
		$this->title = $title;
		$this->width = $width;
		$this->onclick = $onclick;
		$this->params = $params;
		$this->nextpageloc = $nextpageloc;
	}
	
	function display()
	{
		$i = 0;
		$output .= "<table class='ajaxtable' width='{$this->width}' border='1' style='border-collapse: collapse;' align='center'>";
		foreach ($this->properties as $property )
		{
			if( $i == 0 )
			{
				$colspan = count($property);
				$output .= "<thead><tr><th colspan='{$colspan}'>{$this->title}</th><tr>";

				foreach( $property as $key=>$val )
				{
					$output .= "<th>{$key}</th>";
				}

				$output .= "</thead><tbody>";
				$i++;
			}

			$parameters = '';
			$params = array();
			foreach( $this->params as $parameter )
			{
				$params[] = $property[$parameter];
			}
			$parameters = implode(",",$params);			
			$output .= "<tr onclick='{$this->onclick}(\"{$parameters}\")'>";
			foreach( $property as $key=>$val )
			{
				$output .= "<td>{$val}</td>";
			}
			$output .= "</tr>";
		}
		$page = "Pagina: ".$this->page;
		$nextpage = $this->page+1;
		$nextpage = "<a href='./{$this->nextpageloc}/{$nextpage}/'>Volgende</a>";
		if( $this->page != 1 )
		{
			$prevpage = $this->page-1;
			$prevpage = "<a href='./{$this->nextpageloc}/{$prevpage}/'>Vorige</a>";
		}
		$output .= "<tfoot><tr><td colspan='{$colspan}'>{$prevpage} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {$page} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {$nextpage}</td></tr></tfoot>";
		$output .= "</table><br/>";

		return($output);
	}
}

class ArrayInput extends Element
{
	var $properties, $width, $title;

	function __construct($properties=array(), $title='', $width='100%')
	{
		$this->properties = $properties;
		$this->width = $width;
		$this->title = $title;
	}

	function display()
	{
		$i = 0;
		$output .= "<table class='ajaxtable' width='{$this->width}' border='0' style='border-collapse: collapse;' align='center'>";
		foreach ($this->properties as $property )
		{
			if( $i == 0 )
			{
				$colspan = count($property);
				$output .= "<thead><tr><th colspan='{$colspan}'>{$this->title}</th><tr>";

				foreach( $property as $key=>$val )
				{
					$output .= "<th>{$key}</th>";
				}

				$output .= "</thead><tbody><tr>";
				$i++;
			}

			foreach( $property as $key=>$val )
			{
				$output .= "<td>{$val}</td>";
			}
			$output .= "</tr>";
		}
		$output .= "</table><br/>";

		return($output);
	}
}

class checkboxArrayInput extends Element
{
	var $properties, $width, $title;

	function __construct($properties=array(), $title='', $width='100%')
	{
		$this->properties = $properties;
		$this->width = $width;
		$this->title = $title;
	}

	function display()
	{
		$i = 0;
		$output .= "<table class='ajaxtable' width='{$this->width}' border='0' style='border-collapse: collapse;' align='center'>";
		foreach ($this->properties as $property )
		{
			if( $i == 0 )
			{
				$output .= "<thead><tr><th></th>";

				foreach( $property as $key=>$val )
				{
					$output .= "<th>{$key}</th>";
				}

				$output .= "</tr></thead><tbody><tr>";
				$i++;
			}

			$element=0;
			foreach( $property as $key=>$val )
			{
				if( $element == 0 )
				{
					$output .= "<td><input type='checkbox' name='AgendaID' value='".$val."'></td>";
				}
				$element=1;
				$output .= "<td>{$val}</td>";
			}
			$output .= "</tr>";
		}
		$output .= "</table><br/>";

		return($output);
	}
}

class radioArrayInput extends Element
{
	var $properties, $width, $title;

	function __construct($properties=array(), $title='', $width='100%')
	{
		$this->properties = $properties;
		$this->width = $width;
		$this->title = $title;
	}

	function display()
	{
		$i = 0;
		$output .= "<table class='ajaxtable' width='{$this->width}' border='0' style='border-collapse: collapse;' align='center'>";
		foreach ($this->properties as $property )
		{
			if( $i == 0 )
			{
				$output .= "<thead><tr><th></th>";

				foreach( $property as $key=>$val )
				{
					$output .= "<th>{$key}</th>";
				}

				$output .= "</tr></thead><tbody><tr>";
				$i++;
			}

			$element=0;
			foreach( $property as $key=>$val )
			{
				if( $element == 0 )
				{
					$output .= "<td><input type='radio' name='AgendaID' value='".$val."'></td>";
				}
				$element=1;
				$output .= "<td>{$val}</td>";
			}
			$output .= "</tr>";
		}
		$output .= "</table><br/>";

		return($output);
	}
}

class integerInput extends formElement
{

}


class passwordConfirmInput extends formElement
{

}

class textAreaInput extends formElement
{
	function display($width=false)
	{
		$value = htmlspecialchars($this->parent->{$this->property}, ENT_QUOTES);
		$width = ($width == false) ? "" : " style='width:{$width}px'";
		return ("<textarea name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element'>{$value}</textarea>");
	}
}

class fileInput extends formElement
{
	function display()
	{
		$output .= "Huidige bestand: <a href='{$this->parent->{$this->property}}'>{$this->parent->{$this->property}}</a><br>
		<input type='hidden' name='{$this->property}' value='{$this->parent->{$this->property}}' id='".get_class($this->parent)."_{$this->property}_Element_Shadow'/>
		<input type='file' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element' onchange='$(this.id+\"_Shadow\").value = this.value;'>";
		return($output);
	}	
}

class imageInput extends fileInput
{
	function display()
	{
	
		$output .= "<input type='file' name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element'>";
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
		return("<label for='".get_class($this->parent)."_{$this->property}_Element'>{$this->label}</label>");
	}
	function display()
	{
		global $_TPL;
		$uniq = time();
		$script = "

		setTimeout(function() {
		if(typeof(window.FCKeditorAPI) =='object' || typeof(window.__FCKeditorNS) == 'object')
		{
			window.FCKeditorAPI = false;
			window.__FCKeditorNS = false;
		}

		Editor_{$this->property}_{$uniq} = new FCKeditor('".get_class($this->parent)."_{$this->property}_Element' ,  '{$this->width}', '{$this->height}') ;
		Editor_{$this->property}_{$uniq}.BasePath	= './includes/fckeditor/';
		Editor_{$this->property}_{$uniq}.Config.BaseHref = '{$_TPL['baseDir']}../';		
		Editor_{$this->property}_{$uniq}.ToolbarSet = 'Default';
		Editor_{$this->property}_{$uniq}.ReplaceTextarea();
		}, 500);
	
		";
		
		return ("<textarea name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_Element'> {$this->parent->{$this->property}}</textarea>
		<script type='text/javascript'>{$script}</script>");
	}

}

class wysiwyginput extends formElement
{
	var $width, $height;
	function __construct($label, $width='550', $height='250')
	{
		$this->label = $label;
		$this->width = $width;
		$this->height = $height;
	}

	function getLabel() {
		return("<label for='".get_class($this->parent)."_{$this->property}_{$this->parent->ID}'>{$this->label}</label>");
	}
	function display()
	{
		global $_TPL;
		$uniq = time();
		$script = "<script type='text/javascript'> generate_wysiwyg('{$this->property}_'); </script>";
		
		return ("<div id='Editor_{$uniq}_Container'></div> <textarea name='{$this->property}' id='".get_class($this->parent)."_{$this->property}_'> {$this->parent->{$this->property}}</textarea>
		<script type='text/javascript'>{$script}</script>");
	}
}




?>
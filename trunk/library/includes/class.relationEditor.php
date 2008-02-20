<?

/*	
 * Pork.RelationEditor
 * Lists, adds edits and deletes relations.
 * @package pork
 */

class RelationEditor
{
	var $relationType, $parent, $editTypes, $dontShowAdders, $dontshow, $validations;
	function __construct($parent, $reset=true)
	{
		$this->parent = $parent;
		$this->editOptions = array('ADD', 'EDIT', 'REMOVE');
		$this->dontShowAdders = array();
		$this->dontshow = array();
	}

	function dontShow($object)
	{
		$this->dontshow[] = strtolower($object);
	}

	function dontShowAdders($fields = array()) 
	{
		$this->dontShowAdders = $fields;
	}

	static function displayRelation($input, $parent, $editOptions = array('ADD', 'EDIT', 'REMOVE', 'EDITCONN')) 
	{
		$editlinks = array();
		$targetclass = strtolower(get_class($input));
		$parentclass = strtolower(get_class($parent));
		if(array_search('EDIT', $editOptions))
		{	
			$editlinks[] = "<a class='editorlink' href='#' onclick=\"RelationEditor.edit(this, '{$parentclass}', '{$targetclass}', {$parent->ID}, {$input->ID}); return false;\">Wijzigen</a>"; 
		}
		if(array_search('EDITCONN', $editOptions))
		{	
			$editlinks[] = "<a class='editorlink' href='#' onclick=\"RelationEditor.editconn('{$targetclass}', {$parent->ID}, {$input->ID}); return false;\">Verbinding&nbsp;bewerken</a>"; 
		}
		if(array_search('REMOVE', $editOptions))
		{	
			$editlinks[] = "<a class='editorlink' href='#' onclick=\"RelationEditor.remove(this, '{$parentclass}', '{$targetclass}', {$parent->ID}, {$input->ID}); return false;\">Verwijderen</a>"; 
		}
		if(!is_object($input))
		{
			return;
		}
		
		$links = implode("&nbsp;|&nbsp;", $editlinks);
		$output .= "<span class='relationRow'>{$input->displayShort()}</span>&nbsp;<span class='links'>{$links}</span>";
		return($output);
	}			
	

	function displayRelationsFor($classname)
	{
		if($this->parent->ID != false)
		{
			$input = $this->parent->Find($classname);
		}
		if($input == false)
		{
			$output .= "<li>Nog geen {$classname} gekoppeld.</li>";
		}
		else
		{
			for($i=0; $i<sizeof($input); $i++)
			{
				$output .= "<li>".$this->displayRelation($input[$i], $this->parent, $this->editOptions)."</li>";
			}
		}

		return($output);
	}



	function getRelations()
	{
		$options= $this->editOptions;			
		$currentclass= strtolower(get_class($this->parent));
		foreach($this->parent->relations as $key=>$val)
		{
			$targetclass = strtolower($val->className);
			if(array_search($targetclass, $this->dontshow)) continue;

				$output .= "<li><strong>{$targetclass}</strong><ul id='{$currentclass}_{$this->parent->ID}_{$targetclass}_connected'>";
				$output .= $this->displayRelationsFor($targetclass);

				$add = '';			
				foreach($this->editOptions as $value)
				{
					if($value == 'ADD' && array_search(ucFirst($targetclass), $this->dontShowAdders) === false)
					{
						$add = "&nbsp;<a class='editorlink' href='#' onclick=\"RelationEditor.add(this, '{$currentclass}','{$targetclass}', {$this->parent->ID}); return false;\">Nieuw(e) {$key} Toevoegen</a>";
					}
				}
				
				$output .= "</ul><a class='editorlink' href='#' onclick=\"RelationEditor.connect(this, '{$currentclass}','{$targetclass}', {$this->parent->ID}); return false;\">Koppelen</a>{$add}<ul id='{$currentclass}_{$this->parent->ID}_{$targetclass}_notconnected' class='notconnected'></ul></li>";
		
			
		}
		return($output);
	}

	static function displayConnectors($class, $target, $id)
	{
		$object = new $class($id);
		$emptyObject = new $class();
		$trg = new $target();
		$pri = $trg->databaseInfo->primary;
		$pri2 = $object->databaseInfo->primary;
		$keys = array();

		$input = $object->Find($target, array(), array(), array($pri));
		if($input)
		{
			foreach($input as $key=>$val)
			{
				$keys[$val->ID]= $val->ID;
			}
		}
		$trg->analyzeRelations();
		$object->analyzeRelations();
		if(sizeof($keys) > 0) $filters [] = "{$trg->databaseInfo->primary} not in (".implode(",", $keys).")"; 
		$objects = dbObject::Search($target, $filters);
		
		if($objects)
		{
			foreach($objects as $ob)
			{
				$output .= "<li onclick='RelationEditor.makeConnection(this, \"{$class}\", \"{$target}\", \"{$id}\", \"{$ob->ID}\")'>{$ob->displayShort()}</li>";
			}
		}
		else
		{
			$output .= "<li>Geen {$target} gevonden! toevoegen?</li>";
		}
		
		return($output);
	}

	function display($title='Relaties')
	{
		if($this->parent->ID != false && sizeof($this->parent->relations) != 0)
		{
			$output = "<fieldset><legend>{$title}</legend><ul class='relations'>";
			$output .= $this->getRelations();		
			$output .= "</ul>
			</fieldset>";
		}
		return($output);
	}

}

?>
<?

/**
 * Standaard Ajax table. Geef deze class een classname en hij tovert er een browse-bare lijst van.
 * @package pork
 */

class AjaxTable
{
	var $targetObject, $page = 0, $start = 0, $maxPP, $className, $properties, $hasSearchBox, $items, $orderby, $id, $linkurl, $linkprop, $title, $booleanProperties;
	
	function __construct($className)
	{
		if (!$className instanceof dbObject)
		{
			$className = new $className();
		}
		$this->targetObject = $className;
		$this->properties = $this->targetObject->databaseInfo->Fields;
		$this->id = get_class($this->targetObject);
		$this->maxPP = 20;
		$this->booleanProperties = array();
		$this->start = 0;
		$this->count = false;
		$this->search = '';
		$data ='';
		$this->title =false;
		$this->page = 0;
		$this->orderby = ($className->orderProperty != '') ? "order by {$className->orderProperty} {$className->orderDirection}" : "";
		$this->filter = array();
		if(array_key_exists($this->id, $_SESSION) && !empty($_SESSION[$this->id]))
		{
			$this->addSearch($_SESSION[$this->id]);
		}
		else
		{
			$this->addSearch('');
		}


	}

	function setTitle($title) 
	{
		$this->title = $title;
	}

	function setBoolean($property) 
	{
		$this->booleanProperties[] = $property;
	}


	function showProperties($propertyarray)
	{
		$this->properties = $propertyarray;
	}
	
	
	function setOrderBy($orderby)
	{
		$this->orderby = "order by {$orderby}";
	}
	
	function setMaxPP($maxpp)
	{
		$this->maxPP = $maxpp;

	}

	function showSearch($bool)
	{
		$this->hasSearchBox = true;

	}

	function processActions($array)
	{
		if ($array['ajaxAction'] == 'page')
		{
			$this->page = $array['page'];
			$this->start = $this->maxPP * $this->page;
			$js = new jsObject();
			$tableid = $this->id.'_table';
			$js->$tableid = $this->display(true);
			$js->display();

		}
		if ($array['ajaxAction'] == 'showsearch')
		{
			$js = new jsObject();
			$tableid = get_class($this->targetObject).'_search';
			$js->$tableid = $this->createSearch();
			$js->display();
		}
		if($array['ajaxAction'] == 'search') 
		{
			$js = new jsObject();
			$tableid = $this->id.'_table';
			$field = $this->targetObject->fieldForProperty($_POST['searchfield']);
			$this->setFilter("{$field} LIKE '%{$_POST['searchvalue']}%'");
			$js->$tableid = $this->display(true);
			$js->display();
			
		}

	}


	function getCount() 
	{
		if($this->count == false)
		{
			if($this->search && array_search($this->search, $this->filter) === false)
			{
				$this->filter[] = $this->search;
			}
			if(sizeof($this->filter) > 0)
			{
				$where = "where ".implode(' AND ',$this->filter);
			}
			$this->count = dbConnection::getInstance()->fetchOne("select count(*) from {$this->targetObject->databaseInfo->table} {$where}");		
		}
		return($this->count);
	}

	function getData() 
	{
		if($this->search != '')
		{
			$this->filter[] = $this->search;
		}
		if($this->orderby === false && $this->targetObject->orderProperty != '')
		{
			$this->setOrderBy($this->targetObject->orderProperty.' '. $this->targetObject->orderDirection);
		}
		$input = $this->targetObject->Find(get_class($this->targetObject), $this->filter , array($this->orderby, "limit {$this->start}, {$this->maxPP}"));
		$this->data = $input;

		return($input);
	}


	function setFilter($field, $value=false) 
	{
		if($value !== false)
		{
			$field = $this->targetObject->fieldForProperty($field);
		}
		$this->filter[] = ($value == false) ? $field : "{$field} like  '%{$value}%'";
	}

	function addSearch($search)
	{
		$this->search = $search;
		$_SESSION[$this->id] = $this->filter;
	}

	function setCustomFilter($field) 
	{
		$this->filter = array($field);
	}

	function setLink($linkurl, $linkprop) 
	{
		$this->linkurl = $linkurl;
		$this->linkprop = $linkprop;
	}

	function createPagination() 
	{
		global $_TPL;
		$count = $this->getCount();
		$output = 'Kies pagina : ';
		if($this->page > 0) 
		{
			$output .= "<a href='#' onclick=\"new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=page&page=".($this->page -1)."'}).request(); return false\">&lt;&lt;- vorige</a>&nbsp;";
		}

		if($count / $this->maxPP > 15)
		{
			for($i=$this->page; $i<= ($this->page + 8); $i++) 
			{
				$output .= "<a href='#' onclick=\"new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=page&page=".($i )."'}).request(); return false\">".($i+1)."</a>&nbsp;";
			}			
		}
		else
		{
			for($i=0; $i< $count/$this->maxPP; $i++) 
			{
				$output .= "<a href='#' onclick=\"new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=page&page=".($i)."'}).request(); return false\">".($i+1)."</a>&nbsp;";
			
			}
		}

		if($this->page < abs($count/$this->maxPP) -1)
		{
			$output .= "<a href='#' onclick=\"new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=page&page=".($this->page +1)."'}).request(); return false\">Volgende -&gt;&gt;</a>";		
		}


		return($output);
	
	}

	function createSearch() 
	{
		if(sizeof($this->properties) > 0)
		{
		foreach($this->properties as $key=>$val)
		{
			$selected = $_SESSION['filterfield'] == $key ? ' selected' : '';
			$output .= "<option value='{$key}'{$selected}>{$val}</option>";
		}
		return("<fieldset><legend>Search</legend>Search for: <input type='text' name='search' value='' onkeyup=\"this.search = new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=search&type={$this->id}&searchfield='+$('{$this->id}_searchcolumns').value+'&searchvalue='+this.value}).request();   \"> in <select name='searchfield' id='{$this->id}_searchcolumns'>{$output}</select></fieldset>");
		}
	}



	function display($wrapper=false)
	{
		
		if(!$wrapper) 
		{
			$output = "<fieldset id='{$this->id}_wrapper' class='ajaxtable'>";
			if($this->title) 
			{
				$output .= "<legend>{$this->title}</legend>";			
			}
		
			$output .= "<div id='{$this->id}_search' class='searchbox'><a href='#' onclick=\"new Ajax('{$_SERVER['REQUEST_URI']}', {updateMultiple:true, postBody:'ajaxAction=showsearch'}).request(); return false;\" class='searchButton'>Search -></a></div>";
			$output .= "<div id='{$this->id}_table'>";
		}
		$output .= "<table border=1 style='border-collapse:collapse;'><thead>";
		$output .= "<tr><th>".implode("</th><th>", array_values($this->properties))."</th></tr></thead><tbody>";
		
		$input = $this->getData();
		
		$property = $this->linkprop;

		foreach($input as $object)
		{
			$output .= "<tr onclick=\"gotoUrl('".sprintf($this->linkurl, $object->$property)."')\">";
			foreach (array_keys($this->properties) as $item)
			{
				$val = $object->$item;
				if (array_search($item, $this->booleanProperties) !== false)
				{
					$val = ($val == 0) ? 'Nee' : 'Ja';
				}
				$output .= "<td><a href='".sprintf($this->linkurl, $object->$property)."'>".$val."</a></td>";
			}
			$output .= "</tr>";
			
		}
	$output .= "</tbody>";
		if($this->getCount() > 1)
		{
		$output .= "<tfoot><tr><td colspan='".sizeof($this->properties)."'>{$this->createPagination()}</td></tr></tfoot>";
		}
		$output .= "</table>";
		
		$output .= (!$wrapper) ? "<div></fieldset>" : "";
		return($output);


	}

}
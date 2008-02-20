
		function displayEditor($title="", $relations=true, $options="updateMultiple:true")
		{
            $title = ($this->ID == false) ? "Add new ".ucFirst(get_class($this)) : "Edit existing ".ucFirst(get_class($this));  
			$editor = new formGenerator($this, $title, 'IframeWrapper');
			@editorProperties@

			@validatorProperties@
			
			
			if($relations)
			{
				$relationEditor = new RelationEditor($this);
				$output .= $relationEditor->display();
			}

			$output = $editor->display('./ajax/saveObject/'.get_class($this).'/'.$this->ID, $options).$output;
			
			return($output);
			
		}
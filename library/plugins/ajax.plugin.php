<?

/**
 *
 *  Please note that this is *meant* to be back-end code these functions are as abstract as possible so they allow all kinds of url trickery that is not checked.
 *  !!! DO NOT USE THIS CODE IN A LIVE ENVIRONMENT WITH LIVE USERS !!!
 * 
 */

global $_URI;

switch($_URI[0])
{
	case 'ajax':

		switch($_URI[1])
		{
			case 'connect':
				$class = $_URI[2];
				$id = $_URI[4];
				$target= $_URI[3];
		

				die(RelationEditor::displayConnectors($class, $target, $id));

			break;
			case 'makeconnection':
				$target = $_URI[3];
				$source = $_URI[2];
				$src = new $source($_URI[4]);
				$trg = new $target($_URI[5]);
				$src->Connect($trg);
				$editor = new RelationEditor($src);
				;
				$divId= "{$source}_{$_URI[4]}_{$target}_connected";
				$_TPL['js']->$divId = $editor->displayRelationsFor($target);
				$_TPL['js']->display();
			break;
			case 'removeconnection':
				$target = $_URI[3];
				$source = $_URI[2];
				$src = new $source($_URI[4]);
				$trg = new $target($_URI[5]);
				$src->Disconnect($trg);

				$editor = new RelationEditor($src, false);
				
				$divId= "{$source}_{$_URI[4]}_{$target}_connected";
				$_TPL['js']->$divId = $editor->displayRelationsFor($target);
				$_TPL['js']->display();
			break;
			case 'editobject':
				$target = $_URI[3];
				$source = $_URI[2];
				$src = new $source($_URI[4]);
				$trg = new $target($_URI[5]);
				die($trg->displayEditor("Edit {$target}", false, "updateMultiple:true, onComplete: function() { RelationEditor.editDone(\"{$source}\", \"{$target}\", {$_URI[4]}, {$_URI[5]} ); }", false));
			break;
			case 'getobjectrelations':
				$target = $_URI[3];
				$source = $_URI[2];
				$src = new $source($_URI[4]);
				$trg = new $target($_URI[5]);
				$editor = new RelationEditor($src, false);
				$divId= "{$source}_{$_URI[4]}_{$target}_connected";
				$_TPL['js']->$divId = $editor->displayRelationsFor($target);
				$_TPL['js']->display();
			break;
			case 'saveobject':
				$target = $_URI[2];
				$id = $_URI[3];
				
				if(!is_numeric($id)) $id=false;
				$el = new $target($id);
				$validated = array_key_exists(get_class($el), $_SESSION['validators']) ? FormValidator::Validate($el) : true;
if($validated)
				{
					foreach($_POST as $key=>$val)
					{
						if($el->hasProperty($key))
						{
							$el->$key = $val;
						}
					}
					$el->Save();
					
					if(	$_SESSION['addAndConnect'] == true)
					{
						$_TPL['js']->newID = $el->ID;
						$_SESSION['addAndConnect'] == false;
					}
					elseif($id == false)
					{
						$complete = ",{ onComplete:function(){ RelationEditor.switchToEditor('{$el->ID}', '{$target}'); } }";
					}
					
					$_TPL['js']->script .= "window.Growl('".ucFirst("{$target} saved")."'{$complete});";
					
				}
			break;
			case 'add':
				$source = $_URI[3];
				$target = $_URI[2];
				$_SESSION['addAndConnect'] = true;
				$trg = new $target(false);
				die($trg->displayEditor("Add new {$target}", false, "onComplete: function(iframe) { RelationEditor.addDone(\"{$source}\", \"{$target}\", {$_URI[4]}, iframe); }", false));
			
			break;
		}






	break;

}
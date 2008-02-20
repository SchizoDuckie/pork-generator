<?php
global $_TPL, $_URI;
$_TPL['menu']['@cat@'][] = array('Add @cat@ ', '@cat@', 'add');
$_TPL['menu']['@cat@'][] = array('Edit @cat@', '@cat@', 'listedit');
$_TPL['menu']['@cat@'][] = array('Delete @cat@', '@cat@', 'listdelete');


switch ($_URI[0])
{
	case '@cat@':
		switch ($_URI[1])
		{
			case 'listedit':
				$table = new ajaxtable(new @name@()); 
				$table->showProperties(Array(@fields@));
				$table->setTitle("Edit @name@");
				$table->setOrderBy("@primaryKey@ desc");
				$table->setMaxPP(25);
				$table->showSearch(true);
				$table->setLink("./@name@/edit/%s", "ID");
				$table->processActions($_POST);

				$_TPL['body'] .= $table->display();

			break;
			case 'listdelete':
			
				$table = new ajaxtable(new @name@()); 
				$table->showProperties(Array(@fields@));
				$table->setTitle("Delete @name@");
				$table->setOrderBy("@primaryKey@ desc");
				$table->setMaxPP(25);
				$table->showSearch(true);
				$table->setLink("./@name@/delete/%s", "ID");
				$table->processActions($_POST);
				$_TPL['body'] .= $table->display();

			break;
			case 'add':
				$_SESSION['addAndConnect'] = false;
				$@name@ = new @name@();
				$_TPL['body'] .= $@name@->displayEditor();
			break;
			case 'edit':
				$_SESSION['addAndConnect'] = false;
				$@name@ = new @name@($_URI[2]);
				$_TPL['body'] .= $@name@->displayEditor();
			break;
			case 'delete':
				$obj = new $_URI[0]($_URI[2]);
				$_TPL['body'] .= "<fieldset><legend>Confirm</legend>
				Are you sure you want to delete <br>{$obj->displayShort()}<br> ?<br>
				<input type='button' onclick='history.go(-1)' value='No'> &nbsp;&nbsp; <input type='button' onclick='gotoUrl(\"./{$_URI[0]}/dodelete/{$_URI[2]}\")' value='Yes'></fieldset>";
			break;
			case 'dodelete':
				$obj = new $_URI[0]($_URI[2]);
				$obj->deleteYourSelf();
				$_TPL['body'] .= "<fieldset><legend>Deleted</legend>{$_URI[0]} has been deleted.<br><input type='button' onclick='history.go(-2)' value='Back to delete overview'></fieldset>";
			break;
			
		}
	break;
}



















?>
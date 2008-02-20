<?php

global $analyzer, $_URI;

switch ($_URI[0])
{
	case 'ajax':
		$js = new jsObject();
				
		switch ($_URI[1])
		{
			case 'tableeditor':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);
				die( $analyzer->displayTable($_URI[3]));
				
			break;
			case 'generateclass':
				echo("<div class='windowMenu'>
			<ul>
				<li><a href='#' onclick='return false'>Options</a></strong>
				<ul>
					<li><a href='#' class='ajaxlink' onclick='S(\"source\"); return false'>Select source</a></li>
					<li><a href='#' class='ajaxlink' onclick='downloadClass(\"{$_URI[2]}\", \"{$_URI[3]}\"); return false'>Download Class</a></li>
				</ul>		
				</li>
			</ul>
			</div>
			<div id='source'>");
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);
				
				
				die(highlight_string($analyzer->virtuals[$_URI[3]]->createClass(), true).'</div>');
				
				
			break;
			case 'generateplugin':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);
				
				$analyzer->virtuals[$_URI[3]]->createClass();
				die(highlight_string( $generator->createPlugin(), true));
			break;
			case 'savemappings':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);

				$analyzer->virtuals[$_URI[3]]->propertymappings = $_POST;
				$analyzer->storeToSession();
				die();
			break;
			case 'saveeditortypes':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);

				$analyzer->virtuals[$_URI[3]]->editortypes = $_POST['editortypes'];
				$analyzer->virtuals[$_URI[3]]->validations = $_POST['validations'];
				$analyzer->virtuals[$_URI[3]]->mandatory = $_POST['mandatory'];
				
				$analyzer->storeToSession();
				die();
				
			break;
			case 'selectdb':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);
				die($analyzer->display());

			break;
			case 'generationoptions':
				$analyzer = DatabaseAnalyzer::getInstance($_URI[2]);
				$analyzer->generateoptions['display'] = array_key_exists('display', $_POST) ? true : false;
				$analyzer->generateoptions['displayshort'] =  array_key_exists('displayshort', $_POST) ? true : false;
				$analyzer->generateoptions['displayeditor']=  array_key_exists('displayeditor', $_POST) ? true : false;
				$analyzer->storeToSession();
				die();
			break;
			case 'getdatabases':
	
				$_TPL['js']->databases = databaseAnalyzer::displayDatabases('Database:');
				$_TPL['js']->display();

			break;
		}	
		
	break;
	
}

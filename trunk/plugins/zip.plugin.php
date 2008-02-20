<?php
global $_TPL, $_URI;

switch($_URI[0]) 
{
	case 'generate':
		switch($_URI[1])
		{
			case 'interface':
				$analyzer = databaseAnalyzer::getInstance($_URI[2]);
				$oldoptions = $analyzer->generateoptions;

				$analyzer->generateoptions = array(
					'display' => true,
					'displayshort'=> true,
					'displayeditor'=> true
				);
				$analyzer->storeToSession();
				@unlink('./output/output.zip');
				$zip = new zipfile('./output/output.zip');
				$filelist = $zip->globr('./output/', '*.php','./output');
				$templatelist = $zip->globr('./library', '*.*', './library');
						
				foreach ($analyzer->virtuals as $table=>$virtualObject)
				{
					$generator = new classGenerator($virtualObject);
					$zip->add_file($generator->createClass($analyzer->virtuals), "includes/class.{$generator->name}.php");
					$zip->add_file($generator->createPlugin(), "plugins/{$generator->name}.plugin.php");
				}
				
				foreach ($templatelist as $file)
				{
					$input = file_get_contents('./library'.$file);
					$zip->add_file($input, $file);
				}
				
				$zip->add_file(file_get_contents('./settings/dbsettings.php'), 'settings/dbsettings.php');

				$zip->add_file(file_get_contents('./library/.htaccess'), '.htaccess');
				$zip->write();
				$analyzer->generateoptions = $oldoptions;
				$analyzer->storeToSession();
				header("location: {$_TPL['baseDir']}output/output.zip");
			break;
			case 'classes':
				$analyzer = databaseAnalyzer::getInstance($_URI[2]);
				//$analyzer->Analyze();
				@unlink('./output/output.zip');
				$zip = new zipfile('./output/output.zip');
			
				foreach ($analyzer->virtuals as $table=>$virtualObject)
				{
					$generator = new classGenerator($virtualObject);
					$zip->add_file($generator->createClass($analyzer->virtuals), "class.{$generator->name}.php");
				}
				$zip->write();
				header("location: {$_TPL['baseDir']}output/output.zip");
			break;
			case 'plugins':
				$analyzer = databaseAnalyzer::getInstance($_URI[2]);
				//$analyzer->Analyze();
				
				@unlink('./output/output.zip');
				$zip = new zipfile('./output/output.zip');
			
				foreach ($analyzer->virtuals as $table=>$virtualObject)
				{
					$generator = new classGenerator($virtualObject);
					$generator->createClass($analyzer->virtuals);
					$zip->add_file($generator->createPlugin(), "{$generator->name}.plugin.php");
				}
				$zip->write();
				header("location: {$_TPL['baseDir']}output/output.zip");

			break;



		}
	
	
	break;

}



?>
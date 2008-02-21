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
						
				foreach ($analyzer->virtuals as $table)
				{
					$zip->add_file($table->createClass(), strtolower("includes/class.{$table->name}.php"));
					$zip->add_file($table->createPlugin(), strtolower("plugins/{$table->name}.plugin.php"));
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
				chmod('./output/output.zip', 644);
				header("location: {$_TPL['baseDir']}output/output.zip");
			break;
			case 'classes':
				$analyzer = databaseAnalyzer::getInstance($_URI[2]);
				//$analyzer->Analyze();
				@unlink('./output/output.zip');
				$zip = new zipfile('./output/output.zip');
			
				foreach ($analyzer->virtuals as $table)
				{
					$zip->add_file($table->createClass(), strtolower("class.{$table->name}.php"));
				}
				$zip->write();
				chmod('./output/output.zip', 644);
				header("location: {$_TPL['baseDir']}output/output.zip");
			break;
			case 'plugins':
				$analyzer = databaseAnalyzer::getInstance($_URI[2]);
				//$analyzer->Analyze();
				
				@unlink('./output/output.zip');
				$zip = new zipfile('./output/output.zip');
			
				foreach ($analyzer->virtuals as $table)
				{
					$table->createClass();
					$zip->add_file($table->createPlugin(), strtolower("{$table->name}.plugin.php"));
				}
				$zip->write();
				chmod('./output/output.zip', 644);
				header("location: {$_TPL['baseDir']}output/output.zip");

			break;



		}
	
	
	break;

}



?>
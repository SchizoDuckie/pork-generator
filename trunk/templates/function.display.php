		function display()
		{
			$output = "<h1>@name@<table>";
			foreach($this->databaseInfo->Fields as $key=>$val)
			{
				$output .= "<tr><td>{$key}</td><td>{$val}</td></tr>";
			}
			return($output.'</table>');
		}
<?


Class PostFilter
{
	static function nl2mysql($values)
	{
		if(!empty($values))
		{
			foreach($values as $key=>$val)
			{
				$values[$key] = PostFilter::nl2mysqlconvert($val);
			}
		}
		return($values);
	}

	static function nl2mysqlconvert($val) 
	{
		if(ereg("[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}", $val))
		{
			return ereg_replace("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", "\\3-\\2-\\1", $val);	
		}
		if(ereg("[0-9]{2}:[0-9]{2}", $val))
		{
			return ereg_replace("([0-9]{2}):([0-9]{2})", "\\1:\\2:00", $val);
		}
		return $val;
	}

	static function mysql2nl($values) 
	{
		if(!empty($values))
		{
			foreach($values as $key=>$val)
			{
				$values[$key] = PostFilter::mysql2nlconvert($val);
			}
		}
		return($values);
	}

	static function mysql2nlconvert($val) 
	{
		if(ereg("[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}", $val))
		{
			return ereg_replace("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", "\\3-\\2-\\1", $val);
		}
		if(ereg("[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}", $val))
		{
			return ereg_replace("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2})", "\\3-\\2-\\1 \\4", $val);
		}
		if(ereg("[0-9]{2}:[0-9]{2}:[0-9]{2}", $val))
		{
			return ereg_replace("([0-9]{2}):([0-9]{2}):[0-9]{2}", "\\1:\\2", $val);
		}
		return $val;
	}

	static function filterURI($baseDir)
	{
		$uri = explode('/', str_replace(strtolower($baseDir), '', urldecode(strtolower('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))));
		foreach($uri as $key=>$val)
		{
			if(!empty($val)) $out[$key] = $val;
		}
		return($out);
	}

}



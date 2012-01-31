<?php

class Project
{
	public $mainPath = './';
	public $files = array();
	public $functions = array();
	public $classes = array();

	/**
	 * Let's do that thang!
	 */
	function __construct($mainPath = '')
	{
		$this->mainPath = ($mainPath) ?: $this->mainPath;
	}
	/**
	 * Creates flat array containing file structure
	 *
	 * @author Vasil Rangelov a.k.a. boen_robot
	 * Changed by:
	 * @author David King a.k.a. imkingdavid
	 * (Assumed public domain)
	 *
	 * @param array $types The file types to call the function for. Leave as NULL to match all types.
	 * @param bool $recursive Whether to list subfolders as well.
	 * @param string $dir The directory to traverse.
	 * @param string $baseDir String to append at the beginning of every filepath that the callback will receive.
	 *
	 * @return Curren instance of this object
	 */
	function getDirs($types = null, $recursive = true, $dir = '', $baseDir = '') {
		$dir = $dir ?: $this->mainPath;
		$baseDir = $baseDir ?: $this->mainPath;
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if ($file === '.' || $file === '..')
				{
					continue;
				}
				if (is_file($dir . $file))
				{
					if (is_array($types))
					{
						if (!in_array(strtolower(pathinfo($dir . $file, PATHINFO_EXTENSION)), $types, true))
						{
							continue;
						}
					}
					$this->files[] = $baseDir . $file;
				}
				else if ($recursive && is_dir($dir . $file))
				{
					$this->getDirs($types, $recursive, $dir . $file . '/', $dir . $file . '/');
				}
			}
			closedir($dh);
		}
		return $this;
	}

	/**
	 * Get all function and class names (it only gets the class name, not class methods)
	 */
	function getFns()
	{
		foreach ($this->files as $file)
		{
			$contents = file_get_contents($file);
			if (!empty($contents))
			{
				$cmatches = $fmatches = array();
				// Functions
				if ($classes = preg_match_all("/class ([a-zA-Z_]+([a-zA-Z0-9_]*)?)\s*{/", $contents, $cmatches))
				{
					foreach($cmatches[1] as $match)
					{
						$this->classes[] = $match;
					}
					// we don't want to get all the class methods
					// the only functions we care about are the ones in global scope
					continue;
				}

				if ($functions = preg_match_all("/function ([a-zA-Z_]+([a-zA-Z0-9_]*)?)\s*\(/", $contents, $fmatches))
				{
					foreach($fmatches[1] as $match)
					{
						$this->functions[] = $match;
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Get all class names
	 *
	 */
}

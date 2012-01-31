<?php
// This script is intended to go through the contents of two different projects
// and get an array containing each project's function and class names
// It then tells you which could conflict (if any).
include('./Project.php');

$paths = array(
	'./phpbb/',
	'./drupal/',
);

$projects = array();
foreach($paths as $path)
{
	$projects[$path] = new Project($path);
	$projects[$path]->getDirs(array('php', 'inc', 'module'))
					->getFns();
}

$functions = $classes = array();

foreach($projects as $path => $project)
{
	echo 'Project ' . $path . ' has ' . count($project->functions) . ' defined function(s) and ' . count($project->classes) . ' defined class(es).<br />';
	$functions += array_unique($project->functions);
	$classes += array_unique($project->classes);
}
$duplicate_functions = $duplicate_classes = array();
foreach(array_count_values($functions) as $fn => $fn_count)
{
	if($fn_count > 1)
	{
		$duplicate_functions[] = $fn;
	}
}
foreach(array_count_values($classes) as $class => $class_count)
{
	if($class_count > 1)
	{
		$duplicate_classes[] = $class;
	}
}
pre_print_r($duplicate_classes);
pre_print_r($duplicate_functions);

function pre_print_r(array $array = array())
{
	echo '<pre>' . print_r($array, true) . '</pre>';
}

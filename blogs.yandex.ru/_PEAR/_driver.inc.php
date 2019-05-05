<?php 

// ** Documentation
/*______________________________________________________________________________

	Pear Driver

	Last Update: May 2007
	Author: Tom at klenwell@gmail.com

  NOTES	

______________________________________________________________________________*/


// ** Initialize Driver
/*____________________________________________________________________________*/
	
	// internal
	$_PEAR = array();
	$_ds = DIRECTORY_SEPARATOR;
	$_ps = PATH_SEPARATOR;
	
	// paths
	$_PEAR['this_dir'] = dirname(__FILE__);
	$_PEAR['parent_dir'] = dirname($_PEAR['this_dir']);
	$_PEAR['root'] = $_PEAR['this_dir'] . $_ds;
	
	// set constants
	define('PEAR_ROOT', $_PEAR['root']);

/*____________________________________________________________________________*/


// ** Set Include Path
ini_set('include_path', get_include_path() . $_ps . PEAR_ROOT . $_ps . PEAR_ROOT . 'PEAR' . $_ds);	

	
?>

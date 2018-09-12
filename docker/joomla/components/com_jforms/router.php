<?php
/**
* (¯`·.¸¸.-> °º★ вүgιяσ.cσм ★º° <-.¸¸.·´¯)
* @version		0.4.4
* @package		jForms
* @subpackage	Contents
* @copyright	G. Tomaselli
* @author		Girolamo Tomaselli - http://bygiro.com - girotomaselli@gmail.com
* @MVC			basic MVC generated with Cook Self Service  V2.6.4 - www.j-cook.pro
* @license		GNU GPL v3 or later
*
*/

defined('_JEXEC') or die;


/**
 * Build the route for the com_jforms component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function JformsBuildRoute(&$query){

	$segments = array();
	if(isset($query['view']))
	{
		$view = $query['view'];
		$segments[] = $view;
		unset( $query['view'] );
	}

	if(isset($query['layout']))
	{
		$segments[] = $query['layout'];
		unset( $query['layout'] );
	}


	if(isset($query['id']) OR isset($query['cid']))	
	{
		if(in_array($view, array('submission','submissiondetails','editsubmission','finish')))
		{
			$id = (is_array($query['id'])?implode(',', $query['id']):$query['id']);
			$cid = (is_array($query['cid'])?implode(',', $query['cid']):$query['cid']);
			$segments[] = $id ?: $cid;
			unset( $query['id'] );
			unset( $query['cid'] );
		}
	};


	return $segments;
}


/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function JformsParseRoute($segments)
{
	$vars = array();


	$vars['view'] = $segments[0];

	$nextPos = 1;
	if (isset($segments[$nextPos]))
	{
		$vars['layout'] = $segments[$nextPos];
		$nextPos++;
	}

	//Item layout : get the cid value
	if(in_array($vars['view'], array('submission','submissiondetails','editsubmission','finish')) && isset($segments[$nextPos]))
	{
		$slug = $segments[$nextPos];
		$id = explode( ':', $slug );
		$vars['id'] = (int) $id[0];

		$nextPos++;
	}

	return $vars;
}


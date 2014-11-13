<?php
/**
*
* @package Search Results
* @copyright (c) 2014 John Peskens (http://ForumHulp.com)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_SEARCHRESULTS'				=> 'Search results log',
	'PRUNE_SEARCHRESULTS'			=> 'Search Results',
	'PRUNE_SEARCHRESULTS_EXPLAIN'	=> 'Max records in searchresults table, 0 will disable this function.',

	'LOG_SR_REMOVED'		=> '<strong>Prune search results</strong><br />» Search results (%1$s),  %2$s records',
	'LOG_SR_REMOVED_ALL'	=> '<strong>Purged search results</strong>',
));

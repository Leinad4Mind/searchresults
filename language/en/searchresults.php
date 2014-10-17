<?php
/**
*
* @package Search Results
* @copyright (c) 2014 ForumHulp.com
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
	'ACP_SEARCHRESULTS'	=> 'Search result\'s log',

	'ACP_SR_MANAGE'			=> 'Search results',
	'ACP_SR_MANAGE_EXPLAIN'	=> 'Display a list of searchwords your visitors searched for.<br />You can sort on searchword, hits and first and last visit date. A job will delete records where last visit is older then x days, adjustable in Board features.',

	'SEARCHWORD'	=> 'Searchword(s)',
	'REF_HITS'		=> 'Visits',
	'REF_FIRST'		=> 'First visit',
	'REF_LAST'		=> 'Latest visit',

	'LOG_SR_REMOVED'		=> '<strong>Prune search results</strong><br />» Search results (%1$s),  %2$s records',
	'LOG_SR_REMOVED_ALL'	=> '<strong>Purged search results</strong>',

	'DELETE_REFERRERS_DAYS'			=> 'Referrers',
	'DELETE_REFERRERS_DAYS_EXPLAIN'	=> 'Days after cron wil delete Search results. 0 will disable this cron.'
));

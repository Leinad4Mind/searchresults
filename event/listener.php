<?php
/**
*
* @package Search Results
* @copyright (c) 2014 John Peskens (http://ForumHulp.com)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\searchresults\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $config;
	protected $helper;
	protected $user;
	protected $db;
	protected $log;
	protected $phpbb_root_path;
	protected $php_ext;
	protected $searchresults_table;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper    $helper        Controller helper object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\log\log $log, $phpbb_root_path, $php_ext, $searchresults_table)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->user = $user;
		$this->db = $db;
		$this->log = $log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->searchresults_table	= $searchresults_table;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.search_results_modify_search_title'	=> 'search_result'
		);
	}

	public function search_result($event)
	{
		$sql = 'SELECT search_time, search_keywords, search_authors FROM tbl_search_results';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$sql = 'SELECT search_keywords, last_time FROM ' . $this->searchresults_table . ' WHERE search_keywords = \'' . $this->db->sql_escape($row['search_keywords']) . '\'';
			$resulttemp = $this->db->sql_query($sql);
			$found = ($rowtemp = $this->db->sql_fetchrow($resulttemp));
			$this->db->sql_freeresult($resulttemp);

			$fields = array('last_time' => $row['search_time']);
			if (!$found)
			{
				$fields += array(
					'search_keywords' => $row['search_keywords'],
					'hits' => 1,
					'first_time' => $row['search_time'],
				);
				$sql = 'INSERT INTO ' . $this->searchresults_table . ' ' . $this->db->sql_build_array('INSERT', $fields);
			} else if ($row['search_time'] != $rowtemp['last_time'])
			{
				$sql = 'UPDATE ' . $this->searchresults_table . ' SET hits = hits + 1, ' . $this->db->sql_build_array('UPDATE', $fields) . '
						WHERE search_keywords = \'' . $this->db->sql_escape($row['search_keywords']) . '\'';
			}
			$this->db->sql_query($sql);
		}
	}
}

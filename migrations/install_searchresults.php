<?php
/**
*
* @package Search Results
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\searchresults\migrations;

class install_searchresults extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['searchresults_version']) && version_compare($this->config['searchresults_version'], '3.1.0.RC5', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			// We have to create our own f1 webtip tables
			'add_tables'	=> array(
				// F1 driver table
				$this->table_prefix . 'searchresults'	=> array(
					'COLUMNS'		=> array(
						'search_key' => array('UINT', null, 'auto_increment'),
						'search_keywords' => array('TEXT', ''),
						'search_authors' => array('UINT', 0),
						'hits' => array('UINT', null),
						'first_time' => array('TIMESTAMP', 0),
						'last_time' => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'search_key',
				)
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'searchresults'
			)
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('referrers_version', '3.1.0.RC5')),
			array('module.add', array(
				'acp', 'ACP_FORUM_LOGS', array(
					'module_basename'	=> '\forumhulp\searchresults\acp\searchresults_module',
					'auth'				=> 'ext_forumhulp/searchresults && acl_a_viewlogs',
					'modes'				=> array('log'),
				),
			)),
		);
	}
}

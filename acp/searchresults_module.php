<?php
/**
*
* @package Search Results
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\searchresults\acp;

class searchresults_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $db, $config, $phpbb_root_path, $user, $template, $request, $phpbb_extension_manager, $phpbb_container;

		$user->add_lang_ext('forumhulp/searchresults', 'searchresults');

		$this->searchresults_table	= $phpbb_container->getParameter('tables.searchresults');
		$this->page_title		= $user->lang['ACP_SEARCHRESULTS'];
		$this->tpl_name			= 'acp_searchresults';

		$action = $request->variable('action', '');
		switch ($action)
		{
			case 'details':
				$user->add_lang(array('install', 'acp/extensions', 'migrator'));
				$ext_name = 'forumhulp/searchresults';
				$md_manager = new \phpbb\extension\metadata_manager($ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
				try
				{
					$this->metadata = $md_manager->get_metadata('all');
				}
				catch(\phpbb\extension\exception $e)
				{
					trigger_error($e, E_USER_WARNING);
				}

				$md_manager->output_template_data();

				try
				{
					$updates_available = $this->version_check($md_manager, $request->variable('versioncheck_force', false));

					$template->assign_vars(array(
						'S_UP_TO_DATE'		=> empty($updates_available),
						'S_VERSIONCHECK'	=> true,
						'UP_TO_DATE_MSG'	=> $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
					));

					foreach ($updates_available as $branch => $version_data)
					{
						$template->assign_block_vars('updates_available', $version_data);
					}
				}
				catch (\RuntimeException $e)
				{
					$template->assign_vars(array(
						'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
						'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
					));
				}

				$template->assign_vars(array(
					'U_BACK'	=> $this->u_action,
				));

				$this->tpl_name = 'acp_ext_details';
			break;

			default:
				$this->tpl_name	= 'acp_searchresults';
				$this->page_title = 'ACP_REF_MANAGE';
				$this->ref_manage($id, $mode);

			break;
		}
	}

	protected function ref_manage($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request, $phpbb_container;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		// define vars here
		$action		= $request->variable('action', '');
		$ref_id		= $request->variable('id', 0);
		$start		= $request->variable('start', 0);
		$deletemark = $request->variable('delmarked', false, false, \phpbb\request\request_interface::POST);
		$deleteall	= $request->variable('delall', false, false, \phpbb\request\request_interface::POST);
		$mark		= $request->variable('mark', array(0));

		// sort keys
		$sort_key	= $request->variable('sk', 'v');
		$sort_dir	= $request->variable('sd', 'd');

		// form name
		$form_name	= 'acp_searchresults';
		add_form_key($form_name);

		if ($deletemark || $deleteall)
		{
			if (confirm_box(true))
			{
				$sql_where = '';

				if ($deletemark && sizeof($mark))
				{
					$sql_in = array();
					foreach ($mark as $marked)
					{
						$sql_in[] = $marked;
					}
					$sql_where = ' WHERE ' . $db->sql_in_set('search_key', $sql_in);
					unset($sql_in, $marked);

					// get words for logs
					$sql = 'SELECT search_keywords FROM ' . $this->searchresults_table . $sql_where;
					$result = $db->sql_query($sql);

					$host_list = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$host_list[] = $row['search_keywords'];
					}
					$db->sql_freeresult($result);
				}

				if ($sql_where)
				{
					$sql = 'DELETE FROM ' . $this->searchresults_table . $sql_where;
					$db->sql_query($sql);

					add_log('admin', 'LOG_SR_REMOVED', implode(', ', array_unique($host_list)), (int) $this->db->sql_affectedrows());
				}
				else if ($deleteall)
				{
					// clear table
					$db->sql_query('TRUNCATE TABLE ' . $this->searchresults_table);
					add_log('admin', 'LOG_SR_REMOVED_ALL');
				}
			} else
			{
				confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
					'i'			=> $id,
					'start'		=> $start,
					'delmarked'	=> $deletemark,
					'delall'	=> $deleteall,
					'mark'		=> $mark,
					'sk'		=> $sort_key,
					'sd'		=> $sort_dir,
					'mode'		=> $mode,
					'id'		=> $ref_id,
					'action'	=> $action,
				)));
			}
		}

		// sorting
		$sort_by_sql = array('h' => 'search_keywords', 'v' => 'hits', 'f' => 'first_time', 'l' => 'last_time');

		// define sort sql for use in displaying referrers
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
		$dateformats = array_keys($user->lang['dateformats']);
		$sql = 'SELECT * FROM ' . $this->searchresults_table . ' ORDER BY ' . $sql_sort;
		$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('row', array(
				'ID'			=> (int) $row['search_key'],
				'SEARCHWORD'	=> $row['search_keywords'],
				'HITS'			=> (int) $row['hits'],
				'FIRST'			=> $user->format_date($row['first_time'], str_replace(' M ', '-n-', $dateformats[1])),
				'LAST'			=> $user->format_date($row['last_time'], str_replace(' M ', '-n-', $dateformats[1])),

				'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $row['search_key'],
			));
		}
		$db->sql_freeresult($result);

		// used for pagination
		$sql = 'SELECT COUNT(search_key) AS total_entries FROM ' . $this->searchresults_table .
				' ORDER BY ' . $sql_sort;
		$result = $db->sql_query($sql);
		$count = (int) $db->sql_fetchfield('total_entries');
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $count, $config['topics_per_page'], $start);

		// send to template
		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,

			'S_REFDEL'			=> $auth->acl_get('a_clearlogs'),
		));
	}

	/**
	* Check the version and return the available updates.
	*
	* @param \phpbb\extension\metadata_manager $md_manager The metadata manager for the version to check.
	* @param bool $force_update Ignores cached data. Defaults to false.
	* @param bool $force_cache Force the use of the cache. Override $force_update.
	* @return string
	* @throws RuntimeException
	*/
	protected function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false)
	{
		global $cache, $config, $user;
		$meta = $md_manager->get_metadata('all');

		if (!isset($meta['extra']['version-check']))
		{
			throw new \RuntimeException($this->user->lang('NO_VERSIONCHECK'), 1);
		}

		$version_check = $meta['extra']['version-check'];

		$version_helper = new \phpbb\version_helper($cache, $config, $user);
		$version_helper->set_current_version($meta['version']);
		$version_helper->set_file_location($version_check['host'], $version_check['directory'], $version_check['filename']);
		$version_helper->force_stability($config['extension_force_unstable'] ? 'unstable' : null);

		return $updates = $version_helper->get_suggested_updates($force_update, $force_cache);
	}
}

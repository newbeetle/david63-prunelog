<?php
/**
*
* @package Prune Log
* @copyright (c) 2014 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\prunelog\cron\task\core;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class prune_log extends \phpbb\cron\task\base
{
	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string PHP extension */
	protected $php_ext;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor.
	*
	* @param string $phpbb_root_path The root path
	* @param string $php_ext The PHP extension
	* @param phpbb_config $config The config
	* @param phpbb_db_driver $db The db connection
	* @param \phpbb\log\log $log
	* @param \phpbb\user $user
	*/
	public function __construct($phpbb_root_path, $php_ext, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\log\log $log, \phpbb\user $user)
	{
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext			= $php_ext;
		$this->config			= $config;
		$this->db				= $db;
		$this->log				= $log;
		$this->user				= $user;
	}

	/**
	* Runs this cron task.
	*
	* @return null
	*/
	public function run()
	{
		include($this->phpbb_root_path . 'includes/acp/acp_database.' . $this->php_ext);

		if ($this->config['prune_log_days'] > 0)
		{
			$last_log = time() - ($this->config['prune_log_days'] * $this->config['prune_log_gc']);

			$sql = 'DELETE FROM ' . LOG_TABLE . '
				WHERE log_time < ' . $last_log . '
					AND ' . $this->db->sql_in_set('log_operation', 'LOG_USER_WARNING_BODY', true) . '
					AND ' . $this->db->sql_in_set('log_operation', 'LOG_USER_GENERAL', true);
			$this->db->sql_query($sql);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PRUNE_LOG');

			$this->config->set('prune_log_last_gc', time(), true);
		}
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* @return bool
	*/
	public function is_runnable()
	{
		return (bool) $this->config['prune_log_days'] > 0;
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['prune_log_days'] > 0 && time() > ($this->config['prune_log_last_gc'] + $this->config['prune_log_gc']);
	}
}

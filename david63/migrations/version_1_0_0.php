<?php
/**
*
* @package Prune Log Extension
* @copyright (c) 2014 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\prunelog\migrations;

class version_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('prune_log_days', '30')),
			array('config.add', array('prune_log_gc', '86400')),
			array('config.add', array('prune_log_last_gc', '0')),
			array('config.add', array('version_prunelog', '1.0.0')),
		);
	}
}
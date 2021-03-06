<?php
/**
*
* @package Prune Log Extension
* @copyright (c) 2014 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\prunelog\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_board_config_edit_add'	=> 'acp_board_settings',
		);
	}

	/**
	* Set ACP board settings
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function acp_board_settings($event)
	{
		if ($event['mode'] == 'settings')
		{
			$new_display_var = array(
				'title'	=> $event['display_vars']['title'],
				'vars'	=> array(),
			);

			foreach ($event['display_vars']['vars'] as $key => $content)
			{
				$new_display_var['vars'][$key] = $content;
				if ($key == 'override_user_style')
				{
					$new_display_var['vars']['prune_log_days'] = array(
						'lang'		=> 'PRUNE_LOG_DAYS',
						'validate'	=> 'string',
						'type'		=> 'text:3:3',
						'explain' 	=> true,
					);
				}
			}

			$event->offsetSet('display_vars', $new_display_var);
		}
	}
}

<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 *
 * @package    Modular Gaming
 * @author     Oscar Hinton
 * @copyright  (c) 2010 Oscar Hinton
 * @license    http://copy112.com/mg/license
 */

class Controller_Npc extends Controller_Frontend {
	
	public $protected = TRUE;
	public $load_character = TRUE;
	public $require_character = TRUE;
	public $title = 'Npc';
	
	public function action_index($id)
	{
		
		if ( ! is_numeric($id))
		{
			Message::set( Message::ERROR, 'NPC does not exist' );
			$this->request->redirect('zone');		
		}	
		$npc = Jelly::select('npc')
			->where('id', '=', $id)
			->load();
		
		if ($npc->zone_id != $this->character->zone->id)
		{
			Message::set( Message::ERROR, 'NPC is not in your current zone.' );
			$this->request->redirect('zone');

		}		
		$this->template->content = View::factory('npc/index')
			->set('npc', $npc);
		
	}
	
	/**
	 * Get the message specified by the param.
	 * @param integer $id
	 * @param integer $message_id
	 */
	public function action_talk($id, $message_id)
	{
		
		$npc = Jelly::select('npc')
			->where('id', '=', $id)
			->load();
		
		$message = Jelly::select('npc_message')
			->where('id', '=', $message_id)
			->and_where('npc_id', '=', $id)
			->load();
		
		$this->template->content = View::factory('npc/talk')
			->set('npc', $npc)
			->set('message', $message);
		
	}
	
	/**
	 * Get the quest specified by the param.
	 * @param integer $id
	 * @param integer $message_id
	 */
	public function action_quest($id, $quest_id)
	{

		$npc = Jelly::select('npc')
			->where('id', '=', $id)
			->load();

		$quest = Jelly::select('npc_quest')
			->where('id', '=', $quest_id)
			->and_where('npc_id', '=', $id)
			->load();

		$this->template->content = View::factory('npc/quest')
			->set('npc', $npc)
			->set('quest', $quest);

	}

} // End Npc

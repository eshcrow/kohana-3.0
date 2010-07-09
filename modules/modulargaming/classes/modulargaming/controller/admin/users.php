<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 *
 * @package    Modular Gaming
 * @category   Controllers
 * @author     Oscar Hinton
 * @copyright  (c) 2010 Oscar Hinton
 * @license    http://modulargaming.com/license
 */

class Modulargaming_Controller_Admin_Users extends Controller_Backend {
	
	public $title = 'Users';
	private $users_per_page = 10;

	public function before()
	{
		parent::before();
		Asset::add('assets/js/admin/users.js', 'js');
	}
	
	
	public function action_index($page = 1)
	{
		
		// Make sure page is numeric, if not it might be a hacking attempt
		if ( ! is_numeric($page))
			die('Not numeric');
		
		// Count the ammount of users
		$t_users = Jelly::select('user')
			->count();
		
		// Get the offset based on page and the ammount of users we display.
		$offset = ($page-1) * $this->users_per_page;
		
		// Load the users
		$users = Jelly::select('user')
			->offset($offset)
			->limit($this->users_per_page)
			->execute();
		
		$pag_data = array
		(
			'current_page'   => array('source' => 'route', 'key' => 'id'),
			'total_items'    => $t_users,
			'items_per_page' => $this->users_per_page,
		);
		$pagination = Pagination::factory($pag_data)->render();
		
		$this->template->content = View::factory('admin/users/index')
			->set('t_users', $t_users)
			->set('users', $users)
			->set('pagination', $pagination);
		
	}
	
	public function action_new()
	{	
		
		$user = Jelly::factory('user');
		
		// Check if we have a post request
		$post = Validate::factory($_POST)
			->filter(TRUE, 'trim')
			->rule('username', 'not_empty')
			->rule('username', 'alpha_numeric')
			->rule('email', 'not_empty')
			->rule('email', 'email')
			->rule('password', 'min_length', array(6))
			->rule('password', 'max_length', array(20))
			->rule('password_confirm', 'matches', array('password'));
		
		$this->errors = array();
		
		if ( $post->check() )
		{
			
			$values = array(
				'username' => $post['username'],
				'password' => $post['password'],
				'email' => $post['email'],
			);
			
			// Assign the validated data to the sprig object
			$user->set($values);
			
			try
			{
				// Create the new user
				$user->save();
				
				// Redirect the user to the login page
				$this->request->redirect( 'admin/users' );
			}
			catch (Validate_Exception $e)
			{
				// Get the errors using the Validate::errors() method
				$this->errors = $e->array->errors('register');
			}
			
		}
		else
		{
			$this->errors = $post->errors('register');
		}
		
		if ( ! empty($this->errors))
			Message::set(Message::ERROR, $this->errors);
		
		$roles = array();
		
		$this->template->content = View::factory('admin/users/add')
			->set('post', $post->as_array());
		
	}
	
	public function action_edit( $id = '' )
	{
		// Check if the user got permission to edit users
		if ( !$this->a2->allowed( 'admin', 'users_edit' ) )
			$this->request->redirect( '' );
		
		if ( !is_numeric( $id ) ) {
			die('Error, not integer');
		}
		
		$user = Jelly::select('user')
			->where('id', '=', $id)
			->load();
		
		// Check if no post request was made, and load the user
		if ($_POST == array())
		{
			$post = $user;
		}
		else
		{
			
			// Check if we have a post request
			$post = Validate::factory( $_POST )
				->filter( TRUE, 'trim' )
				->rule('username', 'not_empty')
				->rule('username', 'alpha_numeric')
				->rule('email', 'not_empty')
				->rule('email', 'email')
				->rule('password', 'max_length', array( 20 ))
				->rule('password_confirm', 'matches', array('password'))
				->rule('role', 'not_empty');
			
			$this->errors = array();
			
			if ($post->check())
			{
				
				$values = array(
					'username' => $post['username'],
					'email' => $post['email'],
					'role' => $post['role'],
				);
				
				// Assign the validated data to the sprig object
				$user->set($values);
				
				// Hash the password
				if ( $post['password'] != '' )
				{
					$sprig->password = $this->a1->hash_password( $post['password'] );
				}
				
				try
				{
					// Create the new user
					$user->save();
					
					// Redirect the user to the login page
					$this->request->redirect( 'admin/users' );
				}
				catch (Validate_Exception $e)
				{
					// Get the errors using the Validate::errors() method
					$this->errors = $e->array->errors('register');
				}
				
			}
			else
			{
				$this->errors = $post->errors('register');
			}
			
		}
		
		if ( ! empty($this->errors))
			Message::set(Message::ERROR, $this->errors);
		
		$t = Kohana::config('a2');
		$t = $t['roles'];
		
		$roles = array();
		
		foreach ($t as $k => $v){
			$roles[$k] = $k;
		}
		
		$this->template->content = View::factory('admin/users/edit')
			->set('post', $post->as_array())
			->set('roles', $roles);
		
	}
	
	public function action_delete( $id = '' )
	{
		// Check if the user got permission to edit users
		if ( !$this->a2->allowed( 'admin', 'users_edit' ) )
			$this->request->redirect('');
		
		$user = Jelly::select('user')
			->where('id', '=', $id)
			->load();
		
		if ( ! $user->loaded())
		{
			Message::set(Message::ERROR, 'Unable to find the user!');
			$this->request->redirect('admin/users');
		}
		
		$user->delete();
		
		$this->request->redirect('admin/users');
		
	}

} // End Admin_Users

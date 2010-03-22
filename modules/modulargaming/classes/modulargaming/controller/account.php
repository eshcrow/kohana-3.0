<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller for managing the basic user actions (register, login, logout)
 *
 * @package    Modular Gaming
 * @subpackage Core
 * @author     Oscar Hinton
 * @copyright  (c) 2010 Oscar Hinton
 * @license    May not be used without full permission from the Author (Oscar Hinton).
 */

class Modulargaming_Controller_Account extends Controller_Frontend {
	
	public $title = 'Account';

	/**
	 * Settings page, for logged in users.
	 */
	public function action_index()
	{
		// Make sure the user is logged in.
		if ( !$this->user )
			$this->request->redirect( 'account/login' );
		
		
		$this->title = 'Settings';
		$this->template->content = View::factory('account/index');
		
	}
	
	/**
	 * Login page, verifies submited data.
	 */
	public function action_login()
	{
		// If the user is already logged in, send them to their settings.
		if ( $this->user )
			$this->request->redirect( 'account' );
		
		$this->title = 'Login';
		
		// Validate the form input
		$post = Validate::factory($_POST)
			->filter(TRUE,'trim')
			->rule('username', 'not_empty')
			->rule('username', 'min_length', array(3))
			->rule('username', 'max_length', array(20))
			->rule('password', 'not_empty');
		
		// Check if the validation passed and try to log them in.
		if ( $post->check() )
		{
			if ($this->a1->login($post['username'],$post['password'], isset($_POST['remember']) ? (bool) $_POST['remember'] : FALSE))
			{
				$this->request->redirect( '' );
			}
			else
			{
				$this->errors[] = 'Incorrect username or password';
			}
		}
		else
		{
			$this->errors = $post->errors('register');
		}
		
		if ( !empty($this->errors) )
			Message::set( Message::ERROR, $this->errors );
		
		$this->template->content = View::factory('account/login');
		
	}
	
	/**
	 * Register page, verifies submited data.
	 */
	public function action_register()
	{
		// If the user is already logged in, send them to their settings.
		if ( $this->user )
			$this->request->redirect( 'account' );
		
		$this->title = 'Register';
		$this->add_js('assets/js/register.js');
		
		$user = Jelly::factory('user');
		
		// Validate the form input
		$post = Validate::factory($_POST)
			->filter(TRUE,'trim')
			->rule ('username',         'not_empty')
			->rule ('username',         'alpha_numeric')
			->rule ('email',            'not_empty')
			->rule ('email',            'email')
			->rule ('email_confirm',    'matches', array('email'))
			->rule ('password',         'min_length', array ( 6 ) )
			->rule ('password',         'max_length', array( 20 ) )
			->rule ('password_confirm', 'matches', array('password'))
			->callback('captcha',       array($this, 'captcha_valid'))
			->rule ('tos',              'not_empty');
		
		if ($post->check())
		{
			
			$values = array(
				'username' => $post['username'],
				'email'    => $post['email'],
				'password' => $post['password'],
			);
			
			// Assign the validated data to the sprig object
			$user->set( $values );
			
			// Hash the password
			$user->password = $this->a1->hash_password( $post['password'] );
			
			// Set the default role for registered user.
			$user->role = 'user';
			
			try
			{
				// Create the new user
				$user->save();
				
				// Redirect the user to the login page
				$this->request->redirect( 'account/login' );
			}
			catch (Validate_Exception $e)
			{
				// Get the errors using the Validate::errors() method
				$this->errors = $e->array->errors('register');
			}
			
		}
		else
		{
			$this->errors = $post->errors('account/register');
		}
		
		if ( !empty($this->errors) )
			Message::set( Message::ERROR, $this->errors );
		
		$this->template->content = View::factory('account/register')
			->set( 'post',   $post->as_array() );
	}
	
	public function action_logout()
	{
		if ($this->user)
			$this->a1->logout();
		
		$this->request->redirect( '' );
	}
	
	public function captcha_valid(Validate $array, $field)
	{
		if ( ! Captcha::valid($array[$field])) $array->error($field, 'invalid');
	}
	
	public function action_confirm($key)
	{
		
	}
	
	public function action_tos()
	{
		$this->template->content = View::factory( 'account/tos' );
	}

} // End Account
<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 
 *
 * @package    Modular Gaming
 * @author     Oscar Hinton
 * @copyright  (c) 2010 Oscar Hinton
 * @license    http://www.modulargaming.com/license
 */

// Add our events
Event::add('before', 'Character_Event::before');
Event::add('dashboard-left', 'Character_Event::dashboard');

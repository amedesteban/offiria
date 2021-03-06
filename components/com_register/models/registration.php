<?php
/**
 * @version     1.0.0
 * @package     com_account
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_ROOT .DS.'administrator'.DS.'components'.DS.'com_register'.DS.'tables');
// We only need one table should loading entire dir is a good idea?
JTable::addIncludePath( JPATH_ROOT .DS.'components'.DS.'com_stream'.DS.'tables' );

/**
 * Model
 */
class RegisterModelRegistration extends JModel
{			
	public function registerUser($data)
	{
		$jxConfig = new JXConfig();
		$verifyEmail = $jxConfig->cleanEmailList(array($data['email']));
		if (!is_array($verifyEmail))
		{
			$this->setError($verifyEmail);
			return false;
		}
		elseif ($data['password'] == $data['conf_pass'])
		{
			$user = new JUser();
			$temp = new stdClass();
			$temp->name = $data['name'];
			$temp->username = $data['username'];
			$temp->password = $data['password'];
			$temp->block = 0;
			$temp->sendEmail = 0;
			$temp->email = $data['email'];

			// set the default new user group, Registered
			$temp->groups[] = 2;
			$bindData = (array)$temp;
			$user->bind($bindData);

			if(isset($data['group_limited'])) {
				$user->setParam('groups_member_limited', $data['group_limited']);
			}

			if ($user->save())
			{
				$activity = JTable::getInstance('Activity', 'StreamTable');
				$activity->addUser($user->id);
				return true;
			}
			else
			{
				$this->setError($user->getError());
				return false;
			}
		}
		else
		{
			$this->setError(JText::_('COM_REGISTER_ERRMSG_PASSWORD_MISMATCH'));
			return false;
		}
	}
}


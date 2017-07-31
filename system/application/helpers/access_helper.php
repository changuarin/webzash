<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Check if the currently logger in user has the necessary permissions
 * to permform the given action
 *
 * Valid permissions strings are given below :
 *
 * 'view entry'
 * 'create entry'
 * 'edit entry'
 * 'delete entry'
 * 'print entry'
 * 'email entry'
 * 'download entry'
 * 'create ledger'
 * 'edit ledger'
 * 'delete ledger'
 * 'create group'
 * 'edit group'
 * 'delete group'
 * 'create tag'
 * 'edit tag'
 * 'delete tag'
 * 'view reports'
 * 'view log'
 * 'clear log'
 * 'change account settings'
 * 'cf account'
 * 'backup account'
 * 'administer'
   'ft request' 2015-03-31
   'ft approval'
 */

if ( ! function_exists('check_access'))
{
	function check_access($action_name)
	{
		$CI =& get_instance();
		$user_role = $CI->session->userdata('user_role');
		$permissions['manager'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'collection report - OR',
			'collection report - PR',
			'disbursement report',
			'view reports',
			'view log',
			'clear log',
			'change account settings',
			'cf account',
			'backup account',
			'ft request',
			'ft approval',
		);
		
		$permissions['commission'] = array(
			'master group',
			'ref no',
			'commn',
			'view reports'
		);
		
		$permissions['reportviewonly'] = array(
			'collection group',
			'monthly collection',
			'view reports',
			'disbursement report',
			'collection report - OR',
			'collection report - PR',
			'monthly collection summary'
		);
		
		$permissions['accountant'] = array(
			'collection group',
			'collection entry',
			
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'collection report - OR',
			'collection report - PR',
			'collection report - others',
			'disbursement report',
			'view reports',
			'view log',
			'clear log',
			'ft request',
			'ft approval',
		);
		
		$permissions['dataentry'] = array(
			'ref no',
			'sales group',
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'collection group',
			'collection entry',
			'assign OR PR',
			'monthly collection',
			'collection report - OR',
			'collection report - PR',
			'collection report - Others',
			'collection adjustment',
			'disbursement report',
			'view reports',
			'ft request'
		);

		$permissions['cashier'] = array(
			'sales group',
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'ft request',
			'disbursement report',
			'view reports',
		);
		
		$permissions['guest'] = array(
			'view entry',
			'print entry',
			'email entry',
			'download entry',
		);

		// custodian
		$permissions['custodian'] = array(
			'sales group',
			'collection group',
			'collection entry',
			'auto debit',
			'assign OR PR',
			'monthly collection',
			'monthly collection summary',
			'collection report - OR',
			'collection report - PR',
			'collection report - Others',
			'collection report - Auto-debit',
			'collection adjustment',
			'view reports',
			'disbursement report',
			'post to CRB',
			'view entry',
			'print entry',
			'email entry',
			'download entry',
		);

		// admin custodian
		$permissions['admincustodian'] = array(
			'collection group',
			'generate billing',
			'collection entry',
			'assign OR PR',
			'collection report - OR',
			'collection report - PR',
			'collection report - Others',
			'collection report - Auto-debit',
			'collection adjustment',
			'disbursement report',
			'view reports',
			'post to CRB',
			'view entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'ft request',
		);

		if ( ! isset($user_role))
			return FALSE;

		/* If user is administrator then always allow access */
		if ($user_role == "administrator")
			return TRUE;

		if ( ! isset($permissions[$user_role]))
			return FALSE;

		if (in_array($action_name, $permissions[$user_role]))
			return TRUE;
		else
			return FALSE;
	}
}

/* End of file access_helper.php */
/* Location: ./system/application/helpers/access_helper.php */

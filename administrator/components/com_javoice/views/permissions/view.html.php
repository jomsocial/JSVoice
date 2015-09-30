<?php

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

/**
 * Enter description here...
 *
 */
class JAVoiceViewpermissions extends JAVBView {
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tpl
	 */
	function display($tpl = null) {
		
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		switch ($this->getLayout ()) {
			case 'form' :
				{
					$this->edit ();
				}
				break;
			
			default :
				{
					$this->displayItems ();
				}
				break;
		
		}
		
		parent::display ( $tpl );
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function displayItems() {
		
		$db = JFactory::getDBO ();
		$model = $this->getModel ();
		
		$lists = $model->_getVars ();
		$where_more = '';
		$order = '';
		if (isset ( $lists ['order'] ) && $lists ['order'] != '') {
			$order = $lists ['order'] . ' ' . @$lists ['order_Dir'];
		}
		
		if (isset ( $lists ['search'] ) && $lists ['search']) {
			
			if (is_numeric ( $lists ['search'] )) {
				$where_more .= " AND u.id ='" . ( int ) $lists ['search'] . "' ";
			}
			$where_more .= " AND u.username LIKE " . $db->Quote ( '%' . $lists ['search'] . '%' );
		}
		
		jimport ( 'joomla.html.pagination' );
		$total = $model->getTotal ( $where_more );
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		$items = $model->getItems ( $where_more, $lists ['limit'], $lists ['limitstart'], trim ( $order ) );
		
		$this->assign ( 'items', $items );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $item
	 */
	function edit() {
		$mainframe = JFactory::getApplication();
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		if (! $cid) {
			return FALSE;
		}
		$id = $cid [0];
		$user = JFactory::getUser ( $id );
		$isadmin = 0;
		if($user->usertype=='Manager' || $user->usertype=='Administrator' || $user->usertype=='Super Administrator' )
			$isadmin=1;
		$params = class_exists('JRegistry') ? new JRegistry($user->params) : new JParameter($user->params);
		$this->assignRef ( 'user', $user );
		$this->assignRef ( 'isadmin', $isadmin );
		$this->assignRef ( 'params', $params );
		return TRUE;
	}
}	
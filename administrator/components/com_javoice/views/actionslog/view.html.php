<?php
/**
 * ------------------------------------------------------------------------
 * JA Voice Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

/**
 * Enter description here...
 *
 */
class JAVoiceViewactionslog extends JAVBView {
	
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
		$model = $this->getModel ();
		
		$lists = $model->_getVars ();
		$where_more = $model->getWhereClause($lists);
		$order = '';
		if (isset ( $lists ['filter_order'] ) && $lists ['filter_order'] != '') {
			$order = $lists ['filter_order'] . ' ' . @$lists ['filter_order_Dir'];
		}

		jimport ( 'joomla.html.pagination' );
		$total = $model->getTotal ( $where_more );
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		$items = $model->getItems ( $where_more, $lists ['limitstart'], $lists ['limit'], trim ( $order ) );
		$displayTypes='';
		
		$types =  $model->getTypes ( );
		if($types){
			$types = array_merge ( array (JHTML::_ ( 'select.option', '0', JText::_('SELECT_TYPE' ), 'type', 'type' ) ), $types );
			$displayTypes = JHTML::_ ( 'select.genericlist', $types, 'types', 'class="inputbox" size="1" onchange="form.submit()"', 'type', 'type', $lists ['types'] );
		}
		$this->assign ( 'items', $items );
		$this->assign ( 'displayTypes', $displayTypes );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );		
		parent::display ( $tpl );
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
}	
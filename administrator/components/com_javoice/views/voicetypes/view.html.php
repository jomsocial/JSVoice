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
 * @package		Joomla
 * @subpackage	Contacts
 */
class javoiceViewvoicetypes extends JAVBView {
	function display($tpl = null) {
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		$layout = JRequest::getVar ( 'layout', 'default' );
		switch ($layout) {
			case 'form' :
				$this->edit ();
				break;
			default :
				{
					$layout = 'default';
					$this->displayItems ();
					break;
				}
		}
		$this->addToolbar();
		$this->setLayout ( $layout );
		parent::display ( $tpl );
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	function addToolbar(){
		$text = 'JA User Voice Types';
		JToolBarHelper::title ( JText::_ ( $text ) );
		$task = JRequest::getWord ( 'task', '' );
		switch ($task) {
			case 'add' :
			case 'save' :
			case 'apply' :
			case 'edit' :
				{
					JToolBarHelper::apply ();
					JToolBarHelper::save ();
					JToolBarHelper::cancel ();
				}
				break;
			default :
				{
					JToolBarHelper::publishList();
					JToolBarHelper::unpublishList();
					//JToolBarHelper::editListX();
					JToolBarHelper::addNew ();
					JToolBarHelper::deleteList ( JText::_('WARNING_TYPES_BEFOR_DELETED' ) );
				}
		}
	}
	function displayItems() {
		$model = $this->getModel ( 'voicetypes' );
		$lists = $model->_getVars ();
		
		$where = $model->getWhereClause ( $lists );
		//echo $where;
		//order by
		

		$orderby = $lists ['filter_order'] . ' ' . $lists ['filter_order_Dir'];
		$total = $model->getTotal ( $where );
		//limit
		if ($lists ['limit'] > $total) {
			$lists ['limitstart'] = 0;
		}
		if ($lists ['limit'] == 0)
			$lists ['limit'] = $total;
		
		jimport ( 'joomla.html.pagination' );
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		$items = $model->getItems ( $where, $orderby, $lists ['limitstart'], $lists ['limit'] );
		$this->assign ( 'items', $items );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );
	}
	function edit($item = null) {
		$model = $this->getModel ( 'voicetypes' );
		$number = JRequest::getVar ( 'number', 0 );
		if (! $item) {
			$item = $this->get ( 'Item' );
			$postback = JRequest::getVar ( 'postback' );
			$lists_vote_option = $this->initVoteOption ();
			if (! $postback) {
				$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
				$item->bind ( $post );
			} else {
				$lists_vote_option ['count_vote_option'] = count ( $post ['votes_value'] );
				$lists_vote_option ['votes_value'] = $post ['votes_value'];
				$lists_vote_option ['votes_text'] = $post ['votes_text'];
				$lists_vote_option ['votes_description'] = $post ['votes_description'];
			}
		}
		$this->parseOption ( $item->vote_option, $lists_vote_option );
		$data = $item->vote_option;
		$params = class_exists('JRegistry') ? new JRegistry($data) : new JParameter($data);
		
		$ordering = $model->getOrdering ( $item );
		
		$this->assignRef ( 'params', $params );
		$this->assignRef ( 'item', $item );
		$this->assignRef ( 'ordering', $ordering );
		$this->assignRef ( 'count_vote_option', $lists_vote_option ['count_vote_option'] );
		$this->assignRef ( 'votes_value', $lists_vote_option ['votes_value'] );
		$this->assignRef ( 'votes_text', $lists_vote_option ['votes_text'] );
		$this->assignRef ( 'votes_description', $lists_vote_option ['votes_description'] );
		$this->assignRef ( 'number', $number );
	}
	function parseOption($vote_option, &$lists_vote_option) {
		if ($vote_option) {
			$params = class_exists('JRegistry') ? new JRegistry($vote_option) : new JParameter($vote_option);
			$votes_value = $params->get ( 'votes_value' );
			if ($votes_value) {
				$votes_value = explode ( "###", $votes_value );
			}
			$votes_text = $params->get ( 'votes_text' );
			if ($votes_text) {
				$votes_text = explode ( "###", $votes_text );
			}
			$votes_description = $params->get ( 'votes_description' );
			if ($votes_description) {
				$votes_description = explode ( "###", $votes_description );
			}
				
			if ($votes_value && $votes_text && $votes_text) {
				$count_vote_option = count ( $votes_value );
				if ($count_vote_option > 0) {
					$lists_vote_option ['count_vote_option'] = $count_vote_option;
					//$lists_vote_option ['votes_value'] = $this->bindData ( $votes_value, $count_vote_option );
					//$lists_vote_option ['votes_text'] = $this->bindData ( $votes_text, $count_vote_option );
					$lists_vote_option ['votes_value'] = $votes_value;
					$lists_vote_option ['votes_text'] = $votes_text;
					$lists_vote_option ['votes_description'] = $this->bindData ( $votes_description, $count_vote_option );
				}
			}
		
		}
	}
	function bindData($array2, $n) {
		$array1 = array ();
		for($i = 0; $i < $n; $i ++) {
			if ($array2 [$i])
				$array1 [$i] = $array2 [$i];
			else
				$array1 [$i] = '';
		}
		return $array1;
	}
	function initVoteOption() {
		$lists_vote_option ['votes_value'] [0] = '';
		$lists_vote_option ['votes_text'] [0] = '';
		$lists_vote_option ['votes_text'] [0] = '';
		$lists_vote_option ['count_vote_option'] = 1;
		return $lists_vote_option;
	}	
}	

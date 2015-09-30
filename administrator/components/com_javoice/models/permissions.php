<?php
defined ( '_JEXEC' ) or die ();
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
jimport ( 'joomla.application.component.model' );

class JAVoiceModelpermissions extends JAVBModel {

	function __construct() {
		parent::__construct ();
	}

	function getTotal($where_more = '', $joins = '') {
		$db = JFactory::getDBO ();
		
		$query = "SELECT count(a.id) FROM #__users as a" . "\n  $joins" . "\n WHERE 1=1 $where_more";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function getItems($where="",$limit = 100, $limitStart=0) {
		$db = JFactory::getDBO ();						
		$order = ' u.id';						
		$fields = 'u.*';																
								
		$sql = 'SELECT a.*,COUNT(map.group_id) AS group_count,GROUP_CONCAT(g2.title SEPARATOR '.$db->Quote("\n").') AS group_names'
				. ' FROM `#__users` AS a'
				. ' LEFT JOIN #__user_usergroup_map AS map'
				. ' ON map.user_id = a.id'
				. ' LEFT JOIN #__usergroups AS g2'
				. ' ON g2.id = map.group_id'
				. ' LEFT JOIN #__user_usergroup_map AS map2'
				. ' ON map2.user_id = a.id'
				. ' WHERE 1=1'.$where
				. ' GROUP BY a.id'
				. ' ORDER BY a.name asc'
				. ' LIMIT '.$limitStart.', '.$limit;
										
		$db->setQuery ( $sql ); //echo $db->getQuery ( $sql ), '<br>';
		return $db->loadObjectList ();
	}
	
	function parse(&$items){
		$count=count($items);
		if($count>0){
			for($i=0;$i<$count;$i++){
				$item = & $items[$i];
				$item->params=class_exists('JRegistry') ? new JRegistry($item->params) : new JParameter($item->params);
			}
		}
	}
	
	function getItem($cid = array(0)) {
		
		$edit = JRequest::getVar ( 'edit', true );
		if (! $cid || @! $cid [0]) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		
		}
		$this->_getTable ();
		JArrayHelper::toInteger ( $cid, array (0 ) );
		if ($edit) {
			$this->_table->load ( $cid [0] );
		}
		
		return $this->_table;
	}
	
	function _getVars() {	
		$mainframe = JFactory::getApplication();
		$option = 'permissions';
		
		$list = array ();
		$list ['filter_order'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order', 'filter_order', 'u.username', 'cmd' );
		
		$list ['filter_order_Dir'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$list ['limit'] = $mainframe->getUserStateFromRequest ( $option . 'list_limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		
		$list ['limitstart'] = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );
				
		
		$list ['group'] = $mainframe->getUserStateFromRequest ( $option . '.group', 'group', 'permissions', 'string' );
		
		$list ['search'] = $mainframe->getUserStateFromRequest ( $option . '.search', 'search', '', 'string' );
		
		return $list;
	}
}

?>
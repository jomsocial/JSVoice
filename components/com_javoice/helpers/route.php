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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class JavoiceHelperRoute
{
	/**
	 * @param	int	The route of the content item
	 */
	function getTypeId($type_alias, $field='id'){
		$db = JFactory::getDBO();
		
		$sql = "select $field from #__jav_voice_types where (alias='".$db->escape( str_replace(":","-",$type_alias), true )."' or title='".$db->escape(  str_replace(":","-",$type_alias), true )."') and published=1";
		$db->setQuery($sql);//echo $db->getQuery();exit;
		if($field=='id')	return $db->loadResult();
		else return $db->loadObject();
	}
	
	function getForumsId($forums_alias, $type_id){
		$db = JFactory::getDBO();
		
		$sql = "select f.id from #__jav_forums as f inner join #__jav_forums_has_voice_types as vt on (f.id=vt.forums_id and vt.voice_types_id in ($type_id) ) where alias='".$db->escape( $forums_alias, true )."' and f.published=1";
		$db->setQuery($sql);//echo $db->getQuery();exit;
		return $db->loadResult();
	}

	function getStatusId($status_alias, $type_id){
		$db = JFactory::getDBO();
		
		$sql = "select id from #__jav_voice_type_status where alias='".$db->escape( $status_alias, true )."' and voice_types_id='$type_id' and parent_id!=0 and published=1";
		$db->setQuery($sql);//echo $db->getQuery();exit;
		return $db->loadResult();
	}
	
	function getUserId($username){
		$db = JFactory::getDBO();
		
		$sql = "select id from #__users where username='".$db->escape( $username, true )."' and block=0";
		$db->setQuery($sql);//echo $db->getQuery();exit;
		return $db->loadResult();
	}
	
	function getAlias($id, $tablename='voice_types', $field='alias'){
		$db = JFactory::getDBO();
		
		$sql = "select $field from #__jav_$tablename where id='".(int)$id."' and published=1";
		$db->setQuery($sql);//echo $db->getQuery();exit;
		return $db->loadResult();
	}	
	
	function getItemAlias($id, $alias=true){
		$db = JFactory::getDBO();
		
		$sql = "select title from #__jav_items where id='".(int)$id."' and published=1";
		$db->setQuery($sql);
		$title = $db->loadResult();
		if(!$alias) return $title;
		return JFilterOutput::stringURLSafe($title);
	}
	
	function getList()
	{
		$mainframe = JFactory::getApplication();
		$link = 'index.php?option=com_javoice';
		$items = array();
		$pathway = $mainframe->getPathway();
		$items   = $pathway->getPathWay();
				
		if($type_id=JRequest::getInt('type')){
			$item = new stdClass();
			$item->name = $this->getAlias($type_id, 'voice_types', 'title');
			$item->link = $link."&type=$type_id";
			$link = $item->link;
			$items[] = $item;
		}
		if($forums_id=JRequest::getInt('forums')){
			$item = new stdClass();
			$item->name = $this->getAlias($forums_id, 'forums', 'title');
			$item->link = $link."&forums=$forums_id";
			$link = $item->link;
			$items[] = $item;
		}
		if($status_id=JRequest::getInt('status')){
			$item = new stdClass();
			$item->name = $this->getAlias($status_id, 'voice_type_status', 'title');
			$item->link = $link. "&status=$status_id";
			$link = $item->link;
			$items[] = $item;
		}
		if(JRequest::getString('order') && JRequest::getString('order')=='create_date desc'){
			$item = new stdClass();
			$item->name = JText::_('NEW_POST');
			$item->link = $link."&order=create_date desc";
			$link = $item->link;
			$items[] = $item;
		}
		if($cid=JRequest::getInt('cid')){
			$item = new stdClass();
			$item->name = $this->getItemAlias($cid, false);
			$item->link = $link."&cid=$cid";
			$link = $item->link;
			$items[] = $item;
		}
		
		if($uid=JRequest::getInt('uid')){
			$item = new stdClass();
			$item->name = JFactory::getUser($uid)->username;
			$item->link = $link."&view=users&uid=$uid";
			$link = $item->link;
			$items[] = $item;
		}
		
		$count = count($items);
		for ($i = 0; $i < $count; $i ++)
		{
			$items[$i]->name = stripslashes(htmlspecialchars($items[$i]->name));
			$items[$i]->link = JRoute::_($items[$i]->link);
			
		}
		
		$item = new stdClass();
		$item->name = JText::_('HOME');
		$item->link = JURI::base();
		array_unshift($items, $item);
		
		return $items;
	}

	/**
 	 * Set the breadcrumbs separator for the breadcrumbs display.
 	 *
 	 * @param	string	$custom	Custom xhtml complient string to separate the
 	 * items of the breadcrumbs
 	 * @return	string	Separator string
 	 * @since	1.5
 	 */
	public static function siteimg($file, $folder = '/images/system/', $altFile = null, $altFolder = '/images/system/', $alt = null, $attribs = null,
		$asTag = true)
	{
		// Deprecation warning.
		JLog::add('JImage::site is deprecated.', JLog::WARNING, 'deprecated');

		static $paths;
		$app = JFactory::getApplication();

		if (!$paths)
		{
			$paths = array();
		}

		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		$cur_template = $app->getTemplate();

		// Strip HTML.
		$alt = html_entity_decode($alt, ENT_COMPAT, 'UTF-8');

		if ($altFile)
		{
			$src = $altFolder . $altFile;
		}
		elseif ($altFile == -1)
		{
			return '';
		}
		else
		{
			$path = JPATH_SITE . '/templates/' . $cur_template . '/images/' . $file;
			if (!isset($paths[$path]))
			{
				if (file_exists(JPATH_SITE . '/templates/' . $cur_template . '/images/' . $file))
				{
					$paths[$path] = 'templates/' . $cur_template . '/images/' . $file;
				}
				else
				{
					// Outputs only path to image.
					$paths[$path] = $folder . $file;
				}
			}
			$src = $paths[$path];
		}

		if (substr($src, 0, 1) == "/")
		{
			$src = substr_replace($src, '', 0, 1);
		}

		// Prepend the base path.
		$src = JURI::base(true) . '/' . $src;

		// Outputs actual HTML <img> tag.
		if ($asTag)
		{
			return '<img src="' . $src . '" alt="' . $alt . '" ' . $attribs . ' />';
		}

		return $src;
	}
	function setSeparator($custom = null)
	{
		$mainframe = JFactory::getApplication();
		$lang = JFactory::getLanguage();

		/**
	 	* If a custom separator has not been provided we try to load a template
	 	* specific one first, and if that is not present we load the default separator
	 	*/
		if ($custom == null) {
			if($lang->isRTL()){
				$_separator = $this->siteimg('arrow_rtl.png');
			}
			else{
				$_separator = $this->siteimg('arrow.png');
			}
		} else {
			$_separator = $custom;
		}
		return $_separator;
	}
}
?>

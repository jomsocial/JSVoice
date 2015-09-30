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
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class JAVoiceViewfeeds extends JAVBView
{


    function display($tpl = null)
    {
        $layout = JRequest::getVar('layout', 'list');
        $permission = Jrequest::getVar('permission', 0);
        $config = new JConfig();
        $pagetitle = ' RSS Feed ' . ' ' . $config->sitename;
        $document = JFactory::getDocument();
        $document->setTitle($pagetitle);
        switch (strtolower($layout)) {
            case 'guide':
                $this->getItemList();
                break;
            case 'form':
                $this->displayForm();
                break;
            case 'rss':
                $this->rss();
                break;
            default:
                $this->listItems();
                break;
        }
        $this->assignRef('permission', $permission);
        parent::display($tpl);
    }


    function rss()
    {
        $mainframe = JFactory::getApplication();
        require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'feedcreator.class.php');
        $alias = JRequest::getVar('alias', NULL);
        $model = $this->getModel();
        $model_items = JAVBModel::getInstance('items', 'javoiceModel');
        $feed = JTable::getInstance('feeds', 'Table');
        if (!$alias) {
            $post = JRequest::get('request', JREQUEST_ALLOWHTML);
            $post['feed_name'] = isset($post['feed_name']) ? $post['feed_name'] : JText::_("NO_NAME");
            $feed->bind($post);
        } else {
            $feeds = $model->getItems(" AND f.feed_alias='$alias'");
            if (!$feeds) {
                $mainframe->redirect(JRoute::_("index.php?option=com_javoice&view=feeds&layout=guide"), JText::_("NOT_FOUND_RSS"));
                return FALSE;
            }
            $feed->bind($feeds[0]);
        }
        //print_r($feed);exit;
        $where = $model->getWhereClause($feed);
        $document = JFactory::getDocument();
        $document->setTitle($feed->feed_name);
        $limit = 20;
        if ($feed->msg_count > 0)
            $limit = $feed->msg_count;
        $joins = " INNER JOIN #__jav_voice_type_status as s ON s.id=i.voice_type_status_id ";
        $items = $model_items->getItems($where, '', 0, $limit, '', $joins);
        $content = $model->getContentRss($feed, $items);
        $this->assignRef('feed', $feed);
        $this->assignRef('content', $content);
        return TRUE;
    }


    function listItems()
    {
        $lists = array();
        $model = $this->getModel();
        $lists = $model->getvar();
        $total = $model->getTotal("");
        $order = "";
        $filter = array("feed_last_update", "id", "feed_alias", "feed_name");
        if (in_array($lists['filter_order'], $filter))
            $order = " f." . $lists['filter_order'] . " DESC ";
        
        $pagination = $model->getPagination($lists, $total);
        
        $items = $model->getItems(" AND f.published=1", $order, $lists['limitstart'], $lists['limit']);
        
        $this->assignRef('pagination', $pagination);
        $this->assignRef('items', $items);
    }


    function getItemList()
    {
        $user = JFactory::getUser();
        $lists = array();
        $model = $this->getModel();
        $model_forums = JAVBModel::getInstance('forums', 'javoiceModel');
        $model_voice_types = JAVBModel::getInstance('voicetypes', 'javoiceModel');
        $lists = $model->getvar();
        
        $total = $model->getTotal(" AND f.user_id = $user->id");
        $order = "";
        $filter = array("feed_last_update", "id", "feed_alias", "feed_name");
        if (in_array($lists['filter_order'], $filter))
            $order = " f." . $lists['filter_order'] . " DESC ";
        
        $pagination = $model->getPagination($lists, $total);
        
        $items = $model->getItems(" AND f.user_id = $user->id", $order, $lists['limitstart'], $lists['limit']);
        
        //		Filter feeds - begin -							
        $where = " f.published =1";
        foreach ($user->getAuthorisedViewLevels() as $gkey => $gVal) {
            $gid[] = $gVal;
            if ($where == " f.published =1") {
                $where .= " AND (f.gids_view LIKE '%$gkey%'";
            } else {
                $where .= " OR f.gids_view LIKE '%$gkey%'";
            }
        }
        if ($where != " f.published =1")
            $where .= ")";
        
        $forums = $model_forums->getItems($where);
        $forumStr = "";
        if ($forums) {
            $str = array();
            foreach ($forums as $forum) {
                $str[] = $forum->title;
            }
            $forumStr = implode(",", $str);
        }
        $joins = "	INNER JOIN #__jav_forums_has_voice_types as fv ON fv.voice_types_id = t.id
				 	INNER JOIN #__jav_forums as f ON f.id = fv.forums_id";
        
        $where .= " AND t.published=1 AND f.gids_view IN (".implode(",", $gid).") AND f.published =1";
        
        $voice_types = $model_voice_types->getDyamicItems($where, '', 0, 0, 'DISTINCT t.*', $joins, 'object');
        if ($voice_types) {
            $str = array();
            foreach ($voice_types as $voice_type) {
                $str[] = $voice_type->title;
            }
            $voice_typesStr = implode(",", $str);
        }
        $this->assignRef('pagination', $pagination);
        $this->assignRef('forumStr', $forumStr);
        $this->assignRef('voice_typesStr', $voice_typesStr);
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
    }


    function displayForm()
    {
        $user = JFactory::getUser();
        $model = $this->getModel('feeds');
        $model_forums = JAVBModel::getInstance('forums', 'javoiceModel');
        $model_voice_types = JAVBModel::getInstance('voicetypes', 'javoiceModel');
        $feed = JRequest::getVar('feed', NULL);
        if (!$feed) {
            $feed = $this->get('Item');
        }
        $lists['rssTypeList'] = $model->getRssType($feed->feed_type);
        $model->getLists($lists, $feed);
        //		Filter feeds - begin			
        $where = " AND f.published =1";
        foreach ($user->getAuthorisedViewLevels() as $gkey => $gVal) {
            // $gid[] = $gVal;
            if ($where == " AND f.published =1") {
                $where .= " AND (f.gids_view LIKE '%$gkey%'";
            } else {
                $where .= " OR f.gids_view LIKE '%$gkey%'";
            }
        }
        if ($where != " AND f.published =1")
            $where .= ")";
        
        $forums = $model_forums->getItems($where);
        $lists['forumStr'] = "";
        $lists['filter_forums_id'] = "";
        if ($forums) {
            $str = array();
            foreach ($forums as $forum) {
                $str[] = $forum->title;
            }
            $lists['forumStr'] = implode(",", $str);
            $selected = "";
            if ($feed->filter_forums_id)
                $selected = explode(",", $feed->filter_forums_id);
            $forumsHTML = array_merge(array(JHTML::_('select.option', '-1', JText::_('ALL'), 'id', 'title')), $forums);
            $lists['filter_forums_id'] = JHTML::_('select.genericlist', $forumsHTML, 'filter_forums_id[]', 'class="inputbox" size="10" multiple="multiple"', 'id', 'title', $selected);
        }
        $joins = "	INNER JOIN #__jav_forums_has_voice_types as fv ON fv.voice_types_id = t.id
				 	INNER JOIN #__jav_forums as f ON f.id = fv.forums_id";
        
        $where = " AND t.published=1 AND f.published=1";
        foreach ($user->getAuthorisedViewLevels() as $gkey => $gVal) {
            // $gid[] = $gVal;
            if ($where == " AND t.published=1 AND f.published=1") {
                $where .= " AND (f.gids_view LIKE '%$gkey%'";
            } else {
                $where .= " OR f.gids_view LIKE '%$gkey%'";
            }
        }
        if ($where != " AND t.published=1 AND f.published=1")
            $where .= ")";
        
        $voice_types = $model_voice_types->getDyamicItems($where, '', 0, 0, 'DISTINCT t.*', $joins, 'object');
        $lists['filter_voice_types_id'] = "";
        $lists['voice_typesStr'] = "";
        if ($voice_types) {
            $str = array();
            foreach ($voice_types as $voice_type) {
                $str[] = $voice_type->title;
            }
            $lists['voice_typesStr'] = implode(",", $str);
            $selected = "";
            if ($feed->filter_voicetypes_id)
                $selected = explode(",", $feed->filter_voicetypes_id);
            $voice_typesHTML = array_merge(array(JHTML::_('select.option', '-1', JText::_('ALL'), 'id', 'title')), $voice_types);
            
            $lists['filter_voice_types_id'] = JHTML::_('select.genericlist', $voice_typesHTML, 'filter_voicetypes_id[]', 'class="inputbox" size="10" multiple="multiple"', 'id', 'title', $selected);
        }
        $selected = isset($feed->filter_status) ? $feed->filter_status : 2;
        
        $lists['statuss'] = $model->getItemsStatus($selected);
        $this->assignRef('lists', $lists);
        $this->assignRef('feed', $feed);
    
    }
}
?>
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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
/*
 * 
 */
class JAVoiceViewItems extends JAVBView
{


    /**
     * Display the view
     */
    function display($tmpl = null)
    {
        $mainframe = JFactory::getApplication();
        switch ($this->getLayout()) {
            case 'item':
                $this->show_detail();
                break;
            case 'items':
                $this->setLayout('default');
                $this->change_data();
                break;
            case 'search':
                $this->setLayout('default');
                $this->search_data();
                break;
            case 'suggesttopost':
                $this->setLayout('default');
                $this->suggest_to_post_data();
                break;
            case 'vote':
                $this->setLayout('default');
                $this->change_data('vote');
                break;
            case 'form':
                $this->show_form_full();
                break;
            case 'add':
                $this->setLayout('default');
                $this->show_form();
                break;
            case 'edit':
                $this->setLayout('default');
                $this->show_edit();
                break;
            case 'after_save':
                $this->setLayout('default');
                $this->change_data('save');
                break;
            case 'after_edit':
                $this->setLayout('default');
                $this->change_data();
                break;
            case 'paging':
                $this->setLayout('default');
                $this->search_data(false);
                break;
            case 'widget':
                $this->widget_show_list();
                break;
            case 'widget_change':
                $this->widget_change_data();
                break;
            case 'widget_search':
                $this->widget_search_data();
                break;
            case 'youtube':
                $this->youtube();
                break;
            case 'newitem':
            	$this->show_new_item();
                break;
            default:
                $this->show_list();
        
        }
        parent::display($tmpl);
    }


    /*
	 * 
	 */
    function show_list()
    {
        global $javconfig;
        $type_id = null;
        $Itemid = JRequest::getCmd('Itemid');
        $option = JRequest::getCmd('option');
        $mainframe = JFactory::getApplication();
        // Get the page/component configuration
        $params = $mainframe->getParams();
        
        // parameters
        $gl_types = $params->def('types', '');
        $gl_forums = $params->def('forums', '');
        $gl_type_active = $params->def('type_active', '');
        
        $model = $this->getModel();
        
        $where_more = ' and vt.published=1';
        if ($gl_types) {
            $gl_types = explode(',', $gl_types);
            JArrayHelper::toInteger($gl_types, array(0));
            
            if (intval($gl_type_active) && in_array($gl_type_active, $gl_types)) {
                $type_id = (int) $gl_type_active;
            }
            
            $gl_types = implode(',', $gl_types);
            $where_more = " and vt.id in($gl_types) ";
        }
        
        $types = $model->getVoiceTypes($where_more);
        if ($types) {
            foreach ($types as $k => $type) {
                if ($type->total < 1) {
                    unset($types[$k]);
                }
            }
        }
        if (!$type_id) {
            foreach ($types as $type) {
                $type_id = $type->id;
                break;
            }
        }
        $this->assignRef('types', $types);
        if (!$types)
            return;
        
        $type_id = JRequest::getInt('type', $type_id);
        JRequest::setVar('type', $type_id);
        $this->assignRef('type_id', $type_id);
        
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        
        $user = JFactory::getUser();
        
        if (isset($user->usertype) && in_array($user->usertype, array('Manager', 'Administrator', 'Super Administrator'))) {
            $this->getStatus($types);
        }
        
        /*
		 * Check if the configuration shown form login, the rendering module login
		 */
        $this->assignRef('user', $user);
        
        $enale_form_login = isset($javconfig['plugin']) ? $javconfig['plugin']->get('enable_login_form', 1) : 1;
        $this->assignRef('enale_form_login', $enale_form_login);
        $enable_login_form_type = isset($javconfig['plugin']) ? $javconfig['plugin']->get('enable_login_form_type', 1) : 1;
        $this->assignRef('enable_login_form_type', $enable_login_form_type);
        
        $base_url = "";
        if ($enale_form_login) {
            $base_url = JRoute::_("index.php?option=com_javoice&view=items&type={$type_id}&Itemid={$Itemid}");
            $base_url = base64_encode($base_url);
        }
        $this->assignRef('base_url', $base_url);
        
        /*
		 * Get total votes left of current user
		 */
        $total_votes_left = $model->getVotes_left();
        $this->assignRef('total_votes_left', $total_votes_left);
        $show_votes_left = $this->show_votes_left($type, $total_votes_left);
        $this->assignRef('show_votes_left', $show_votes_left);
        
        $this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video', 0));
        $this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys', 0));
        $this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode', 0));
        $this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline', 0));
        $this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image', 0));
        $this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file', 0));
        $this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file', 0));
        $this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha', 0));
        $this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user', 0));
        $this->assign("textAreaID", "newVoiceContentReply");
        
        //show
        if ($javconfig["plugin"]->get("enable_button_create_new", 0)) {
            $this->assign("callAddNewButton", 1);
            $this->assign("formcreatenew", $this->loadTemplate('search_result'));
        }
        /*
		 * Set the title of the page
		 */
        $pagetitle = '';
        if ($types) {
            $type_titles = array();
            foreach ($types as $type) {
                $type_titles[] = $type->title;
            }
            $pagetitle = implode(' - ', $type_titles);
        
        }
        $config = new JConfig();
        $pagetitle .= ' ' . JText::_('AT') . ' ' . $config->sitename;
        $document = JFactory::getDocument();
        $document->setTitle($pagetitle);
    }


    /*
	 * Show voice item detail by ID. By Default is menu config
	 */
    function show_detail()
    {
        global $javconfig;
        $model = $this->getModel();
        JRequest::setVar('view_detail', 1);
        $type_id = JRequest::getInt('type');
        $JAVoiceControllerItems = new JAVoiceControllerItems();
        $items = $JAVoiceControllerItems->getItems();
        
        $this->assignRef('items', $items);
        if ($items) {
            $type_id = @$items[0]->voice_types_id;
        }
        $this->assignRef('type_id', $type_id);
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        $total_votes_left = $model->getVotes_left();
        $this->assignRef('total_votes_left', $total_votes_left);
        $show_vote_left = $this->show_votes_left($type, $total_votes_left);
        $this->assignRef('show_votes_left', $show_vote_left);
        
        /*
		 * Check if the configuration shown form login, the rendering module login
		 */
        $user = JFactory::getUser();
        $this->assignRef('user', $user);
        
        $enale_form_login = isset($javconfig['plugin']) ? $javconfig['plugin']->get('enable_login_form', 1) : 1;
        $this->assignRef('enale_form_login', $enale_form_login);
        $enable_login_form_type = isset($javconfig['plugin']) ? $javconfig['plugin']->get('enable_login_form_type', 1) : 1;
        $this->assignRef('enable_login_form_type', $enable_login_form_type);
        
        $base_url = "";
        if ($enale_form_login) {
            $base_url = JRoute::_("index.php?option=com_javoice&view=items&type=$type_id");
            $base_url = base64_encode($base_url);
        }
        $this->assignRef('base_url', $base_url);
        
        $config = new JConfig();
        $pagetitle = $config->sitename;
        if ($items) {
            $pagetitle = $items[0]->title . ' - ' . $type->title . ' ' . JText::_('FROM') . ' ' . @JFactory::getUser($items[0]->user_id)->username . ' ' . JText::_('AT') . ' JA Voice';
        
        }
        $document = JFactory::getDocument();
        $document->setTitle($pagetitle);
        
        $this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video', 0));
        $this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys', 0));
        $this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode', 0));
        $this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline', 0));
        $this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image', 0));
        $this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file', 0));
        $this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file', 0));
        $this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha', 0));
        $this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user', 0));
        $this->assign("textAreaID", "newVoiceContentReply");
    }


    function widget_show_list()
    {
        $option = JRequest::getCmd('option');
        $mainframe = JFactory::getApplication();
        require_once JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'helpers' . DS . 'route.php';
        $helper_route = new JavoiceHelperRoute();
        
        $model = $this->getModel();
        
        $voicetypes_popular = JRequest::getString('voicetypes');
        $types = array();
        
        if ($voicetypes_popular) {
            $voicetypes = explode(',', $voicetypes_popular);
            foreach ($voicetypes as $value) {
                $type = null;
                if (is_int($value)) {
                    $type = $model->getVoiceType($value);
                } elseif ($value != '') {
                    $type = $helper_route->getTypeId(trim($value), '*');
                }
                if ($type)
                    $types[] = $type;
            }
        } else {
            $types = $model->getVoiceTypes();
        }
        $this->assignRef('types', $types);
        
        if (!$types) {
            $types_suggest = $model->getVoiceTypes();
            if ($types_suggest) {
                foreach ($types_suggest as $type) {
                    $types_title[] = $type->alias;
                }
                $types_title = implode(', ', $types_title);
                $this->assignRef('types_title', $types_title);
                return;
            }
        }
        
        $forums = array();
        $forums_text = JRequest::getString('forums');
        if ($forums_text) {
            $forums_arr = explode(',', $forums_text);
            foreach ($forums_arr as $value) {
                $forum = null;
                if (is_int(trim($value))) {
                    $forums[] = trim($value);
                } elseif ($value != '' && isset($types[0]->id)) {
                    $forum = $helper_route->getForumsId(trim($value), $types[0]->id);
                    if ($forum)
                        $forums[] = $forum;
                }
            }
            
            if ($forums) {
                $forums_id = implode(',', $forums);
                JRequest::setVar('forums_id', $forums_id);
            } else {
                JRequest::setVar('forums_id', "no_forum");
            }
        }
        
        //print_r($forums);exit;
        

        $type_id = JRequest::getInt('type', @$types[0]->id);
        $number_voices = JRequest::getVar('number_voices', 20);
        JRequest::setVar('type', $type_id);
        $this->assignRef('type_id', $type_id);
        $this->assignRef('number_voices', $number_voices);
        $this->assignRef('forums_text', $forums_text);
        
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        $link = $_SERVER['REQUEST_URI'];
        $this->assignRef('link', $link);
    
    }


    function widget_change_data()
    {
        $type_id = JRequest::getInt('type');
        $this->assignRef('type_id', $type_id);
        $this->setLayout('default');
        require_once JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'helpers' . DS . 'route.php';
        $helper_route = new JavoiceHelperRoute();
        
        $object = array();
        $k = 0;
        
        /*$object [$k] = new stdClass ( );
		$object [$k]->id = '#jav-mainbox-' . $type_id . ' .jav-col1';
		$object [$k]->attr = 'html';
		$object [$k]->content = $this->show_form ( true, true );
		$k ++;
		*/
        
        $forums = array();
        $forums_text = JRequest::getString('forums');
        if ($forums_text) {
            $forums_arr = explode(',', $forums_text);
            foreach ($forums_arr as $value) {
                $forum = null;
                if (is_int(trim($value))) {
                    $forums[] = trim($value);
                } elseif ($value != '' && isset($type_id)) {
                    $forum = $helper_route->getForumsId(trim($value), $type_id);
                    if ($forum)
                        $forums[] = $forum;
                }
            }
            
            if ($forums) {
                $forums_id = implode(',', $forums);
                JRequest::setVar('forums_id', $forums_id);
            } else {
                JRequest::setVar('forums_id', "no_forum");
            }
        }
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-mainbox-' . $type_id . ' .jav-list-items';
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->get_top_popular();
        $k++;
        
        $helper = new JAVoiceHelpers();
        echo $helper->parse_JSON_new($object);
        exit();
    }


    function widget_search_data()
    {
        $type_id = JRequest::getInt('type');
        $this->assignRef('type_id', $type_id);
        
        $object = array();
        $k = 0;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-mainbox-' . $type_id . ' .jav-list-items';
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->get_top_popular();
        $k++;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-mainbox-' . $type_id . ' .jav-search-result';
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->loadTemplate('search_result');
        $k++;
        
        $helper = new JAVoiceHelpers();
        echo $helper->parse_JSON_new($object);
        exit();
    }


    function change_data($task = '')
    {
        global $javconfig;
        $model = $this->getModel();
        $type_id = JRequest::getInt('type');
        $this->assignRef('type_id', $type_id);
        $type = $model->getVoiceType($type_id);
        
        $object = array();
        $k = 0;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-pathway-' . $type_id;
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->getPatway();
        $k++;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-points-remaining-' . $type_id;
        ;
        $object[$k]->attr = 'html';
        $votes_left = $model->getVotes_left();
        $object[$k]->content = $this->show_votes_left($type, $votes_left);
        $k++;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#votes-left-' . $type_id;
        $object[$k]->attr = 'value';
        $object[$k]->content = $votes_left;
        $k++;
        
        if ($javconfig["plugin"]->get("enable_your_items", 1)) {
            $object[$k] = new stdClass();
            $object[$k]->id = '#jav-col-right-' . $type_id . ' .jav-list-your-ideas';
            $object[$k]->attr = 'html';
            $object[$k]->content = $this->getYourItems();
            $k++;
        }
        switch ($task) {
            case 'save':
            case 'vote':
                {
                    $cid = JRequest::getInt('cid');
                    $item = $model->getItem();
                    
                    if ($item->total_vote_down > 0) {
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-total-votes-of-user-down-' . $cid;
                        $object[$k]->attr = 'html';
                        $object[$k]->content = '-' . $item->total_vote_down;
                        $k++;
                    }
                    
                    if ($item->total_vote_up > 0) {
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-total-votes-of-user-' . $cid;
                        $object[$k]->attr = 'html';
                        $object[$k]->content = $item->total_vote_up;
                        $k++;
                    }
                    
                    $object[$k] = new stdClass();
                    $object[$k]->id = '#jav-item-votes-' . $cid;
                    $object[$k]->attr = 'class';
                    $object[$k]->content = 'votes value-' . JRequest::getInt('votes');
                    $k++;
                    
                    $object[$k] = new stdClass();
                    $object[$k]->id = '#jav-item-votes-' . $cid;
                    $object[$k]->attr = 'html';
                    $object[$k]->content = JRequest::getInt('votes');
                    $k++;
                    
                    $object[$k] = new stdClass();
                    $object[$k]->id = '#jav-box-item-' . $cid;
                    $object[$k]->attr = 'class';
                    $object[$k]->content = 'jav-box-item selected';
                    $k++;
                    
                    if ($task == 'save') {
                        $object[$k] = new stdClass();
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#key-' . $type_id;
                        $object[$k]->attr = 'value';
                        $object[$k]->content = '';
                        $k++;
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-list-items-' . $type_id;
                        $object[$k]->attr = 'html';
                        $object[$k]->content = $this->getItems();
                        $k++;
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-mainbox-' . $type_id . ' .jav-search-result';
                        $object[$k]->attr = 'css';
                        $object[$k]->content = "display,none";
                        $k++;
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-list-options-' . $type_id;
                        $object[$k]->attr = 'css';
                        $object[$k]->content = "display,inline-block";
                        $k++;
                        
                        $user = JFactory::getUser();
                        if ($user->id)
                            $msg = JText::_("VOTED_WELL_TEXT") . ' <a href="' . JRoute::_('index.php?option=com_javoice&amp;view=users&uid=' . $user->id) . '" title="' . JText::_('CLICK_HERE_TO_GO_TO_EMAIL_PREFERENCE') . '">' . JText::_("EMAIL_YOU") . '</a> ' . JText::_('WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGE');
                        else
                            $msg = JText::_('VOTED') . ' <a href="' . JRoute::_('index.php?option=com_users&amp;view=login') . '" title="' . JText::_('CLICK_HERE_TO_LOGIN') . '">' . JText::_("LOGIN") . '</a> ' . JText::_('OR') . ' <a href="' . JRoute::_('index.php?option=com_users&amp;view=registration') . '" title="' . JText::_('CLICK_HERE_TO_SIGN_UP') . '">' . JText::_("SIGN_UP") . '</a> ' . JText::_('SO_THAT_WE_CAN_LET_YOU_KNOW_WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGE');
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-msg-succesfull';
                        $object[$k]->attr = 'html';
                        $object[$k]->content = $msg;
                        $k++;
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-msg-succesfull';
                        $object[$k]->attr = 'css';
                        $object[$k]->content = 'display,block';
                        $k++;
                        
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-what-now';
                        $object[$k]->attr = 'css';
                        $object[$k]->content = "display,block";
                        $k++;
                    
                    } else {
                        if (isset($_SESSION['first_votes'])) {
                            $_SESSION['first_votes'] = null;
                            $object[$k] = new stdClass();
                            $object[$k]->id = '#jav-firsttime-voting';
                            $object[$k]->attr = 'css';
                            $object[$k]->content = "display,block";
                            $k++;
                        }
                    
                    }
                }
                break;
            
            default:
                {
                    $object[$k] = new stdClass();
                    $object[$k]->id = '#jav-list-options-' . $type_id;
                    $object[$k]->attr = 'html';
                    $object[$k]->content = $this->getOptions();
                    $k++;
                    
                    $object[$k] = new stdClass();
                    $object[$k]->id = '#jav-list-items-' . $type_id;
                    $object[$k]->attr = 'html';
                    $object[$k]->content = $this->getItems();
                    $k++;
                    if ($javconfig["plugin"]->get("enable_button_create_new", 1)) {
                        $object[$k] = new stdClass();
                        $object[$k]->id = '#jav-mainbox-' . $type_id . ' .jav-search-result';
                        $object[$k]->attr = 'html';
                        $this->assign("callAddNewButton", 1);
                        $object[$k]->content = $this->loadTemplate('search_result');
                        $k++;
                    }
                
                }
        
        }
		
		//update forums only in case new items do not need to be approved 
        if(!$javconfig["systems"]->get('item_needs_approved',0)){
			$object[$k] = new stdClass();
			$object[$k]->id = '#jav-list-forums-' . $type_id;
			$object[$k]->attr = 'html';
			$object[$k]->content = $this->getForums();
			$k++;
        }
        
        /* Paging */
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-pagination-' . $type_id;
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->getPaging($type_id);
        $k++;
        
        $helper = new JAVoiceHelpers();
        echo $helper->parse_JSON_new($object);
        exit();
    }


    function search_data($search = true)
    {
        global $javconfig;
        $type_id = JRequest::getInt('type');
        $this->assignRef('type_id', $type_id);
        
        $objects = array();
        
        $object = new stdClass();
        $object->id = '#jav-pathway-' . $type_id;
        $object->attr = 'html';
        $object->content = $this->getPatway();
        $objects[] = $object;
        
        if ($javconfig["systems"]->get("paging_type", "normal") == "autoscroll") {
            $total_page = 14;
            $current_page = (int) JRequest::getVar("javpage", 0) + 1;
            $object = new stdClass();
            $object->id = '#jav-list-items-' . $type_id . ' ol';
            $object->attr = 'append';
            if (JRequest::getVar("pagingtype", "normal") == "autoscroll") {
                $object->attr = 'append';
            } else {
                $object->attr = 'html';
            }
            $object->content = $this->getItems(false, $total_page);
            $objects[] = $object;
            
            //set page and total item									
            $object = new stdClass();
            $object->id = '#jav_nexpage_' . $type_id;
            $object->attr = 'value';
            $object->content = $current_page . "_" . $total_page;
            $objects[] = $object;
        } else {
            $object = new stdClass();
            $object->id = '#jav-list-items-' . $type_id;
            $object->attr = 'html';
            $object->content = $this->getItems();
            $objects[] = $object;
            
            /* Paging */
            $object = new stdClass();
            $object->id = '#jav-pagination-' . $type_id;
            $object->attr = 'html';
            $object->content = $this->getPaging($type_id);
            $objects[] = $object;
        }
        
        if ($search) {
            $object = new stdClass();
            $object->id = '#jav-mainbox-' . $type_id . ' .jav-search-result';
            $object->attr = 'html';
            $object->content = $this->loadTemplate('search_result');
            $objects[] = $object;
        }
        
        $object = new stdClass();
        $object->id = '#jav-list-forums-' . $type_id;
        $object->attr = 'html';
        $object->content = $this->getForums();
        $objects[] = $object;
        
        $object = new stdClass();
        $object->id = '#jav-list-options-' . $type_id;
        $object->attr = 'html';
        $object->content = $this->getOptions();
        $objects[] = $object;
        
        //set page and total item									
        $object = new stdClass();
        $object->id = 'assignValue';
        $object->attr = 'jav_process_ajax';
        $object->content = 0;
        $objects[] = $object;
        
        $helper = new JAVoiceHelpers();
        $content = $helper->parse_JSON_new($objects);
        die($content);
        //echo $helper->parse_JSON_new ( $objects );
        exit();
    }


    function suggest_to_post_data()
    {
        $object = array();
        $k = 0;
        
        $object[$k] = new stdClass();
        $object[$k]->id = '#jav-suggestion';
        $object[$k]->attr = 'html';
        $object[$k]->content = $this->getItems(true);
        $k++;
        
        $helper = new JAVoiceHelpers();
        echo $helper->parse_JSON_new($object);
        exit();
    }


    function getItems($show_suggest = false, &$total_page = 0)
    {
        global $javconfig;
        $model = $this->getModel('items');
        
        $type_id = JRequest::getInt('type');
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        $JAVoiceControllerItems = new JAVoiceControllerItems();
        $items = $JAVoiceControllerItems->getItems();
        $this->assignRef('items', $items);
        $this->assignRef('show_suggest', $show_suggest);
        
        $numberOfItem = $JAVoiceControllerItems->currentTotal();
        $limit = JRequest::getInt('limit', $javconfig['systems']->get('display_num', 20));
        $total_page = ceil($numberOfItem / $limit);
        $this->assignRef('total_page', $total_page);
        $this->assignRef('type_id', $type_id);
        
        $html = $this->loadTemplate('items');
        
        return $html;
    }


    function getPaging($type_id)
    {
        $model = $this->getModel();
        
        $lists = $model->_getVars();
        $pagination = $model->getPagination($lists['limitstart'], $lists['limit'], 'jav-pagination-' . $type_id);
        $this->assignRef('lists', $lists);
        $this->assignRef('pagination', $pagination);
        
        return $this->loadTemplate('paging');
    }


    function getOptions()
    {
        $model = $this->getModel('items');
        $type_id = JRequest::getInt('type');
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        
        $list_options = JAVBModel::getInstance('voicetypesstatus', 'javoiceModel')->getItems(' and s.show_on_tab=1 and s.parent_id!=0 and s.voice_types_id=' . $type_id);
        $this->assignRef('list_options', $list_options);
        
        return $this->loadTemplate('options');
    
    }


    function getForums()
    {
        $mainframe = JFactory::getApplication();
        // Get the page/component configuration
        $params = $mainframe->getParams();
        
        // parameters
        $gl_forums = $params->def('forums', '');
        $where_more = '';
        if ($gl_forums) {
            $gl_forums = explode(',', $gl_forums);
            JArrayHelper::toInteger($gl_forums, array(0));
            
            $gl_forums = implode(',', $gl_forums);
            $where_more = " and f.id in ($gl_forums)";
        }
        
        $type_id = JRequest::getInt('type');
        $model_forums = JAVBModel::getInstance('forums', 'javoiceModel');
        
        $where_more .= ' and f.published=1 and ft.voice_types_id=' . $type_id;
        $fields_join = 'ft.total_items';
        $join = 'INNER JOIN #__jav_forums_has_voice_types as ft ON f.id=ft.forums_id';
        
        $forums = $model_forums->getItems($where_more, 100, 0, 'f.ordering', $fields_join, $join);
        
        $this->assignRef('list_forums', $forums);
        $this->assignRef('type_id', $type_id);
        
        $html = $this->loadTemplate('forums');
        
        return $html;
    }


    function getYourItems()
    {
        $type_id = JRequest::getInt('type');
        $this->assignRef('type_id', $type_id);
        
        $model = $this->getModel();
        $user = JFactory::getUser();
        $items = $model->getYourItems($user->id);
        if ($items) {
            $this->assignRef('your_items', $items);
            $html = $this->loadTemplate('your_items');
            return $html;
        }
        return '';
    }


    function getStatus($types)
    {
        $model = $this->getModel();
        
        $list_status = array();
        $model_status = JAVBModel::getInstance('voicetypesstatus', 'javoiceModel');
        
        foreach ($types as $k => $type) {
            //$status = $model->getStatus ( " and s.voice_types_id='{$type->id}' and s.published=1" );
            //$voice_type_id, $voice_type_status_id, $attrs, $published = ''
            $attrs = " style='width:100px;' name='status_{$type->id}' id='status_{$type->id}'";
            $list_status[$type->id] = $model_status->displaySelectOptgroup($type->id, '', $attrs, 1); //( $status, " id='status_{$type->id}' name='status_{$type->id}' class=\"inputbox\" ", '', JText::_('SELECT_STATUS' ) );
        }
        $this->assignRef('list_status', $list_status);
    }


    function show_edit()
    {
        global $javconfig;
        $user = JFactory::getUser();
        $model = $this->getModel();
        
        if (!JAVoiceHelpers::checkPermissionAdmin()) {
            $isAllowRegisterEdit = 0;
            //print_r($javconfig["systems"]->get("is_edit_delete_voice",1));die();
            if ($javconfig["systems"]->get("is_edit_delete_voice", 0)) {
                $item = $model->getItem();
                $userE = JFactory::getUser();
                $userEId = $userE->get('id');
                if ($userEId == $item->user_id) {
                    $timeE = $javconfig["systems"]->get("time_for_edit_voice", 900);
                    if ($timeE != -1 || time() < ($item->create_date + $timeE)) {
                        $isAllowRegisterEdit = 1;
                    }
                }
            }
            if (!$isAllowRegisterEdit) {
                echo JText::_('YOU_DONT_PERMISSION');
                exit();
            }
        }
        
        $type_id = JRequest::getInt('type');
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        
        $modelforums = JAVBModel::getInstance('forums', 'javoiceModel');
        $fids = '0';
        $tem = $modelforums->getForumByPermissionVote();
        if (!$tem) {
            ?>
<!--  -->
<div class="jav-msg-error jav-notpermission">
				<?php
            echo JText::_('SORRY_YOU_DONT_PERMISSION_TO_EDIT_THIS');
            ?> 
				<?php
            echo $type->title?>
			</div>
<?php
            exit();
        }
        $fids = implode(',', $tem);
        
        $model = $this->getModel();
        $item = $model->getItem();
        $this->assignRef('item', $item);
        
        $where_more = " and f.id in ($fids) and vt.voice_types_id='$type_id'";
        $join = " INNER JOIN #__jav_forums_has_voice_types as vt ON vt.forums_id=f.id";
        $forums = $modelforums->getItems($where_more, 50, 0, 'f.ordering', '', $join);
        $displayForums = JHTML::_('select.genericlist', $forums, 'forums_id', "class=\"inputbox\" size=\"1\" ", 'id', 'title', $item->forums_id);
        $this->assignRef('displayForums', $displayForums);
        
        $this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video', 0));
        $this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys', 0));
        $this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode', 0));
        $this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline', 0));
        $this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image', 0));
        $this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file', 0));
        $this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file', 0));
        $this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha', 0));
        $this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user', 0));
        
        echo $html = $this->loadTemplate('form_edit');
        exit();
    }


    function show_votes_left($type, $votes_left)
    {
        if ($type->total_votes == -1) {
            return JText::_('UNLIMITED');
        }
        return $votes_left;
    }


    function getListLimit($limitstart, $limit, $order = '')
    {
         global $javconfig;
    	
        $display_num = $javconfig['systems']->get('display_num', 10);
        
    	$array = array(5, 10, 15, 20, 50, 100);
    	if(!in_array($display_num, $array)){
    		array_push($array, $display_num);
    	}
    	sort($array);
        $list_html = array();
        foreach ($array as $value) {
            $list_html[] = JHTML::_('select.option', $value, $value);
        }
        //limitstart, limit, order
        $onchange = "$limitstart, $limit, '$order'";
        $key = JRequest::getVar('key');
		$forums = '';
		if (JRequest::getInt('forums')) $forums = '&amp;forums='.JRequest::getInt('forums');
 		$Itemid = JRequest::getInt('Itemid');
 		$link = '';
        if(JRequest::getString('order')=='total_vote_up desc' || (!JRequest::getInt('status') && !JRequest::getString('order'))){
        	$link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;order=total_vote_up desc&amp;type='.JRequest::getInt('type').'&amp;Itemid='.$Itemid.$forums;
        }
        if(JRequest::getString('order')=='create_date desc'){
        	$link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;order=create_date desc&amp;type='.JRequest::getInt('type').'&amp;Itemid='.$Itemid.$forums;
        }
        if(JRequest::getInt('status')){
        	$link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;status='.JRequest::getInt('status').'&amp;type='.JRequest::getInt('type').'&amp;Itemid='.$Itemid.$forums;
        }
        $list_html = JHTML::_('select.genericlist', $list_html, 'limit', ' onchange="jav_doPaging(' . $limitstart . ', this.value, \'' . $order . '\', \'' . $key . '\',\''.$link.'\')"', 'value', 'text', $limit);
        
        return $list_html;
    }


    function getPatway()
    {
        
    	global $javconfig;
        $enable_pathway = $javconfig['plugin']->get('enable_pathway', 1);
        if (!$enable_pathway)
            return '';
        
        require_once JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'helpers' . DS . 'route.php';
        $helper = new JavoiceHelperRoute();
        $list = $helper->getList();
        $this->assignRef('list', $list);
        
        $separator = $helper->setSeparator();
        $this->assignRef('separator', $separator);
        
        $html = $this->loadTemplate('pathway');
        return $html;
    }


    function show_form($full = false, $return = false)
    {
        global $javconfig;
        $model = $this->getModel();
        
        $type_id = JRequest::getInt('type');
        $forum_id = JRequest::getInt('forum_id', 0);
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        
        /*get ItemID */
        $Itemid = JRequest::getInt("Itemid", 0);
        $this->assignRef('Itemid', $Itemid);
        
        $modelforums = JAVBModel::getInstance('forums', 'javoiceModel');
        $fids = '0';
        $forums = $modelforums->getItemsbyType($type_id);
        if (!$forums) {
            ?>

<div class="jav-msg-error jav-notpermission">
				<?php
            echo JText::_("SORRY_YOU_CANT_POST_PLEASE_CONTACT_ADMINISTRATOR_FOR_FURTHER_ASSISTANCE");
            ?> 
			</div>
<?php
            if (!$full)
                exit();
        }
        $tem = $modelforums->getForumByPermissionVote();
        if (!$tem) {
            ?>

<!--  -->

<div class="jav-msg-error jav-notpermission">
				<?php
            echo JText::_('SORRY_YOU_DONT_PERMISSION_TO_POST_A_NEW');
            ?> <?php
            echo $type->title?>
			</div>
<?php
            if (!$full)
                exit();
        
        }
        $fids = implode(',', $tem);
        
        $votes_left = $model->getVotes_left();
        $this->assignRef('votes_left', $votes_left);
        if ($javconfig["systems"]->get("is_use_vote", 1) && $votes_left == 0) {
            ?>

<div class="jav-msg-error jav-notpermission">
				<?php
            echo JText::_("SORRY_YOU_CANT_POST_BECAUSE_YOURE_OUT_OF_VOTES");
            ?> 
			</div>
<?php
            if (!$full)
                exit();
        }
        $key = JRequest::getString('key', ''); //echo $key;
        $key = rawurldecode($key);
        
        $this->assignRef('key', $key);
        
        if ($type->total_votes == -1)
            $votes_left = 1000;
        
        $where_more = " and f.id in ($fids) and vt.voice_types_id='$type_id'";
        $join = " INNER JOIN #__jav_forums_has_voice_types as vt ON vt.forums_id=f.id";
        $forums = $modelforums->getItems($where_more, 50, 0, 'f.ordering', '', $join);
        $displayForums = "";
        if ($forums) {
            $displayForums = JHTML::_('select.genericlist', $forums, 'forums_id', "class=\"inputbox\" size=\"1\" ", 'id', 'title', $forum_id);
        }
        
        $this->assignRef('displayForums', $displayForums);
        if ($javconfig['systems']->get('is_use_vote', 1)) {
            $array_votes = array();
            
            $params_type = class_exists('JRegistry')? new JRegistry($type->vote_option) : new JParameter($type->vote_option);
            $votes_text = $params_type->get('votes_text') ? explode("###", $params_type->get('votes_text')) : array();
            $votes_value = $params_type->get('votes_value') ? explode("###", $params_type->get('votes_value')) : array();
            $put = array();
            $list_votes = '';
            
            if ($votes_value && $votes_text) {
                foreach ($votes_value as $k => $value) {
                    if (abs($value) <= $votes_left) {
                        $put[] = JHTML::_('select.option', $value, $votes_text[$k]);
                    }
                }
            }
            if ($put)
                $list_votes = JHTML::_('select.radiolist', $put, 'votes', "class=\"inputbox rad\" size=\"1\" ", 'value', 'text', $put[0]->value);
            
            $this->assignRef('displayVotes', $list_votes);
        }
        $this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video', 0));
        $this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys', 0));
        $this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode', 0));
        $this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline', 0));
        $this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image', 0));
        $this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file', 0));
        $this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file', 0));
        $this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha', 0));
        $this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user', 0));
        
        if (!$full) {
            echo $html = $this->loadTemplate('form');
            exit();
        } elseif ($return) {
            return $this->loadTemplate('form');
        }
    }

	function show_new_item(){
		$Itemid = JRequest::getInt('Itemid');
        $this->assignRef('Itemid', $Itemid);
        
        $model = $this->getModel();
        $this->assignRef('model', $model);
        
        $type_id = JRequest::getInt('type');
        
        $types = $model->getVoiceTypes();
        if (!$types)
            return;
        foreach ($types as $k => $type) {
            if ($type->total < 1) {
                unset($types[$k]);
            } elseif (!$type_id) {
                $type_id = $type->id;
            }
        }
        
        JRequest::setVar('type', $type_id);
        $this->assignRef('type_id', $type_id);
        $this->assignRef('types', $types);
        
        $types_option = JHTML::_('select.genericlist', $types, 'type', ' onchange="jav_change_vars(this.value);"', 'id', 'title', $type_id);
        $this->assignRef('types_option', $types_option);
        
        $this->show_form(true);
	}
    function show_form_full()
    {
        $Itemid = JRequest::getInt('Itemid');
        $this->assignRef('Itemid', $Itemid);
        
        $model = $this->getModel();
        $this->assignRef('model', $model);
        
        $type_id = JRequest::getInt('type');
        
        $types = $model->getVoiceTypes();
        if (!$types)
            return;
        foreach ($types as $k => $type) {
            if ($type->total < 1) {
                unset($types[$k]);
            } elseif (!$type_id) {
                $type_id = $type->id;
            }
        }
        
        JRequest::setVar('type', $type_id);
        $this->assignRef('type_id', $type_id);
        $this->assignRef('types', $types);
        
        $types_option = JHTML::_('select.genericlist', $types, 'type', ' onchange="jav_change_vars(this.value);"', 'id', 'title', $type_id);
        $this->assignRef('types_option', $types_option);
        
        $this->show_form(true);
    
    }


    function get_top_popular()
    {
        $model = $this->getModel('items');
        
        $type_id = JRequest::getInt('type');
        $type = $model->getVoiceType($type_id);
        $this->assignRef('type', $type);
        
        JRequest::setVar('limit', JRequest::getInt('number_voices', 5));
        $items = JAVoiceControllerItems::getItems();
        $this->assignRef('items', $items);
        
        $this->setLayout('widget');
        $html = $this->loadTemplate('items');
        
        return $html;
    }


    function getAddThis()
    {
        global $javconfig;
        $str = "";
        if ($javconfig['plugin']->get('enable_addthis')) {
            $str = trim($javconfig['plugin']->get('custom_addthis'));
            if (strlen($str) == 0) {
                $str = "
					<!-- AddThis Button BEGIN -->
					<a href=\"http://www.addthis.com/bookmark.php?v=250&amp;pub=xa-4a764a015f702d7f\" onmouseover=\"return addthis_open(this, '', '[URL]', '[TITLE]')\" onmouseout=\"addthis_close()\" onclick=\"return addthis_sendto()\">
						<img src=\"http://s7.addthis.com/static/btn/lg-share-en.gif\" width=\"125\" height=\"16\" alt=\"" . JText::_('BOOKMARK_AND_SHARE') . "\" style=\"border:0\"/>
					</a>
					<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a764a015f702d7f\"></script>
					<!-- AddThis Button END -->	";
            }
        }
        return $str;
    }

    function getAddToAny()
    {
        global $javconfig;
        $str = "";
        if ($javconfig['plugin']->get('enable_addtoany', 1)) {
            $str = trim($javconfig['plugin']->get('custom_addtoany'));
            if (strlen($str) == 0) {
                $str = "
						<a class=\"a2a_dd\" href=\"http://www.addtoany.com/share_save\"><img src=\"http://static.addtoany.com/buttons/share_save_171_16.png\" width=\"171\" height=\"16\" border=\"0\" alt=\"Share/Bookmark\"/></a><script type=\"text/javascript\">a2a_linkname=document.title;a2a_linkurl=location.href;</script><script type=\"text/javascript\" src=\"http://static.addtoany.com/menu/page.js\"></script>";
            }
        }
        return $str;
    }

    function getTagName($tagID)
    {
        $modelTags = JAVBModel::getInstance('tags', 'javoiceModel');
        return $modelTags->getTagName($tagID);
    }
}
?>
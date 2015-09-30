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

jimport('joomla.application.component.controller');

class JAVoiceController extends JAVFController
{


    /**
     * Method to show a weblinks view
     *
     * @access	public
     * @since	1.5
     */
    
    function display($cachable = false, $urlparams = false)
    {    	
        parent::display($cachable = false, $urlparams = false);
        
    }
}
?>
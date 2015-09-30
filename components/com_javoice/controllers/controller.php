<?php
/**
 * @version     $Id: controller.php 1620 2012-09-21 12:11:58Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ;

jimport('joomla.application.component.controller');

if (version_compare(JVERSION, '3.0', 'ge'))
{
    class JAVFController extends JControllerLegacy
    {
        public function display($cachable = false, $urlparams = array())
        {
            parent::display($cachable, $urlparams);
            if (!substr(JRequest::getVar('layout'), 0, 6) == 'widget') {
	            echo "<div style='clear:both; margin-top: 20px;'><center>";
	            echo JAPermissions::show_powered_by();
	            echo '</center></div>';
       	 	}   
        }

    }

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
    class JAVFController extends JController
    {
        public function display($cachable = false, $urlparams = false)
        {
            parent::display($cachable, $urlparams);
        }

    }

}
else
{
    class JAVFController extends JController
    {
        public function display($cachable = false)
        {
            parent::display($cachable);
        }

    }

}

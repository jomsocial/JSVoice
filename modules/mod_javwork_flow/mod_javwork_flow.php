<?php
/**
 *$JA#COPYRIGHT$
 */

?>

<?php
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
$app = JFactory::getApplication();
  	    
JHTML::stylesheet('modules/mod_javwork_flow/asset/themes/'.$params->get('javthemes', 'default').'/ja.workflow.css');
if(file_exists(JPATH_BASE.'/templates/'.$app->getTemplate().'/css/ja.workflow.css')) {
	JHTML::stylesheet('templates/'.$app->getTemplate().'/css/ja.workflow.css');
}
require(JModuleHelper::getLayoutPath('mod_javwork_flow'));
?>
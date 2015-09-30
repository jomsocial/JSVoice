<?php
/**
 *$JA#COPYRIGHT$
 */

?>

<?php
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
require_once JPATH_ADMINISTRATOR . '/components/com_javoice/models/model.php';
if(!defined('JAVOICE_GLOBAL_CSS')){
	// global $mainframe;
	$mainframe = JFactory::getApplication();
	JHTML::stylesheet('components/com_javoice/asset/css/ja.voice.css');
	if(file_exists(JPATH_BASE.DS.'templates/'.$mainframe->getTemplate().'/css/ja.voice.css')){
		JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/ja.voice.css');
	}
	  	    
	JHTML::stylesheet('ja.popup.css', 'components/com_javoice/asset/css/');
	if(file_exists(JPATH_BASE.DS.'templates/'.$mainframe->getTemplate().'/css/ja.popup.css')){
		JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/ja.popup.css');
	}
	define('JAVOICE_GLOBAL_CSS', true);
}

$model = new javoiceModelvoicetypes ( );

$forums_id = $params->get('forums', '');
$type_ids = array();
if($forums_id && is_array($forums_id)){	
	foreach ($forums_id as $fid){
		if($fid==1){
			$type_ids = array();
			break;
		}
		$fid = explode('_', $fid);
		if(!in_array($fid[0], $type_ids))	$type_ids[] = $fid[0];
	}
}
$where = ' and t.published=1 ';
if($type_ids){
	$type_ids = implode(',', $type_ids);
	$where .= " and t.id in ($type_ids)";
}
$types = $model->getItems($where);
/*$type_id = $params->get ( 'voicetypes', 0 );
$type = $model->getItems($params->get ( 'voicetypes', 0 ));*/
$Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));
$user = JFactory::getUser();

$list = modJAVLatestItemsHelper::getList($params);

modJAVLatestItemsHelper::parseItems($list, $types);

require(JModuleHelper::getLayoutPath('mod_javlist_voices',$params->get('layout', 'default')));
?>
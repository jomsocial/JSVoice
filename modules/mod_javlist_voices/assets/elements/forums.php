<?php 
/**
 *$JA#COPYRIGHT$
 */ 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.html');
//import the necessary class definition for formfield
jimport('joomla.form.formfield');
require_once JPATH_ADMINISTRATOR . '/components/com_javoice/models/model.php';
class JFormFieldForums extends JFormFieldList
{
	protected $type = 'forums';
	
	protected function getInput()
	{
		require_once (JPATH_SITE . '/components/com_javoice/models/items.php');	
		require_once (JPATH_SITE . '/components/com_javoice/models/forums.php');	
		
		$jaVoiceModelItems = new JAVoiceModelItems ( );
		$model_forums = new JAVoiceModelforums();
		$class = (string) $this->element['class'];
		$multiple = $this->element['multiple']?'multiple="multiple"':'';
		$types = $jaVoiceModelItems->getVoiceTypes ( " AND vt.published = 1", "vt.ordering" );	
		$forums=array();
		$forums[0] = new stdClass();
		$forums[0]->id = '1';
		$forums[0]->title = JText::_("Select All");
		foreach ($types as $type) {
			array_push($forums, JHTML::_('select.optgroup', $type->title, 'id', 'title'));
			
			$where_more = ' and f.published=1 and ft.voice_types_id=' . $type->id;
			$join = 'INNER JOIN #__jav_forums_has_voice_types as ft ON f.id=ft.forums_id';
			
			$results = $model_forums->getItems ( $where_more, 100, 0, 'f.ordering', '', $join );
					
			//array_push($forums, $optgroup);
			foreach ($results as $result) {
				$result->id = $type->id.  '_' .$result->id;
				array_push($forums, $result);
			}
			array_push($forums, JHTML::_('select.optgroup', $type->title, 'id', 'title'));
		}
		//jexit();
		$out = JHTML::_('select.genericlist',  $forums, $this->name, 'class="inputbox '.$class.'" '.$multiple, 'id', 'title', $this->value );
		
		return $out;
	}
}
?>

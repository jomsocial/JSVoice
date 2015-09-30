<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
/**
* This controller is used for configuration feature of the component
*/
class JAVoiceControllerfeeds extends JAVFController{
	function __construct()
	{
		parent::__construct();	
		
	}

	function display($cachable = false, $urlparams = false)
	{
		global $javconfig;
		$layout = Jrequest::getVar('layout');		
			$per=FALSE;
			$permission = $javconfig['systems']->get('user_group',"1,6,7,2,3,4,5,10,12,8");
			if($permission){
				$permissions = explode(",",$permission);
				$user = JFactory::getUser();
				
				//Get all groups that the user is mapped to recursively.
		        $groups = $user->getAuthorisedGroups();
				if($user->id){
					if($permissions[0]=="1"){
						$per = TRUE;	
					}else{
					    foreach($groups as $gkey=>$gVal){
							if (in_array ( $gVal, $permissions )) {
								$per = TRUE;
								break;
							}					
						}
					}
				}					
			}	
		if(!$per &&($layout=='guide'||$layout=='form'))	Jrequest::setVar('layout','list');
		Jrequest::setVar('permission',$per);
		parent::display($cachable = false, $urlparams = false);
		return TRUE;	
	}

	function save()
	{
		$model = $this->getModel('feeds');
		$id=$model->storeFeed();
		if ($id) {
			
			$msg = JText::_('SUCCESSFULLY_SAVE_TO_FEED');
			$this->setRedirect(JRoute::_("index.php?option=com_javoice&view=feeds&layout=guide&filter_order=feed_last_update"),$msg);
		} else {
			JRequest::setVar('layout','form');			
			parent::display();
		}
	}
	function cancel()
	{
		$Itemid = JRequest::getVar('Itemid');
        $this->setRedirect(JRoute::_("index.php?option=com_javoice&view=feeds&layout=guide"));
	}
}
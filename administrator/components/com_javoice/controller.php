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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.controller' );

class JAVoiceController extends JAVBController {
	function display($cachable = false, $urlparams = false) {	
		
        if($controller = JRequest::getCmd('view', 'voice')) {	
        	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
        	if (file_exists($path)) {
        		require_once $path;
        	} else {
        		$controller = '';
        	}
        }
        $view = $controller;
        
        
        if(!JRequest::getVar('tmpl')&& $view!='emailtemplates'){
        	?>
        	
        	<script type="text/javascript">
        		var siteurl = '<?php echo JURI::base()."index.php?tmpl=component&option=com_javoice&view=".$view;?>';
        		var message = document.getElementById('system-message');
        		if(!message){
            		
        			var myMessage  = new Element('div', {id: 'system-message'});
        			<?php if (version_compare(JVERSION, '3.0', 'ge')) {?>
        			myMessage.inject(document.getElementById('content'), 'before');	
        			<?php }else{?>
        			myMessage.inject(document.getElementById('element-box'), 'before');	
        			<?php }?>			
        		}					
        	</script>
        	
        	<?php
        	
        }
        	
		parent::display();	 			
								
	}
}
?>
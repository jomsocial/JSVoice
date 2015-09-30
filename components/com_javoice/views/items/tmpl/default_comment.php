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
defined( '_JEXEC' ) or die( 'Restricted access' );

global $javconfig;
$items = $this->items;
$sytem_comment = isset($javconfig['integrate'])?$javconfig['integrate']->get('run_system', 'intensedebate'):'intensedebate';
$helper = new JAVoiceHelpers ( );

if ($sytem_comment == 'jcomments') {
	$jcomment = JAVoiceHelpers::checkComponent ( 'com_jcomments' );
	$this->jcomments = $jcomment;
}
elseif ($sytem_comment == 'jacomment') {
	$jacomment = JAVoiceHelpers::checkComponent ( 'com_jacomment' );
	$this->jacomment = $jacomment;
}
?><!-- 
 -->
<script type="text/javascript">
	//<![CDATA[
	var pre_langs = new Array();
	var next_langs = new Array();
	//]]>
</script>
<?php

if( $items ){
	$item = $items[0];
	
	$type_id = $item->voice_types_id;
	$mode = $javconfig['integrate']->get("system_mode_$sytem_comment", 1);
	$languages = $javconfig['integrate']->get( 'language_'. $sytem_comment. '_'. $type_id );
	$script_langs = array();
	if ( trim($languages)!='' ){
		$languages = class_exists('JRegistry')? new JRegistry($languages) : new JParameter($languages);
		$languages = $languages->_registry['_default']['data'];
		if($languages){
			foreach ($languages as $k=>$lang){
				$script_langs[1][] = "'". trim($k) ."'"; 
				$script_langs[2][] = "'". JText::_(trim($lang)) ."'";
			}
		}
	}
	
	$link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id);?>
	
	<script type="text/javascript">
	//<![CDATA[		
		<?php if($script_langs && isset($script_langs[1]) &&  isset($script_langs[2])){?>
		pre_langs = new Array(<?php echo implode(',', $script_langs[1])?>);
		next_langs = new Array(<?php echo implode(',', $script_langs[2])?>);		
		<?php } ?>
	//]]>
	</script>
	
	<?php	if ($sytem_comment == 'intensedebate') {?>
	
		<script type="text/javascript">
		//<![CDATA[
			var idcomments_acct = "<?php echo $javconfig['integrate']->get('account_'.$sytem_comment)?>";
			var idcomments_post_id = "<?php echo $item->id?>";
			var idcomments_post_url = "<?php echo JURI::base().substr($link,  strlen(JURI::base(true)) + 1);//JURI::base().$link?>";	
		//]]>
		</script>
		<span id="IDCommentsPostTitle" style="display:none"></span>
		<script type="text/javascript" src="http://www.intensedebate.com/js/genericCommentWrapperV2.js"></script>
		
		
	<?php } else if ($sytem_comment == 'disqus') {?>
	
		<div id="disqus_thread"></div>
		<script type="text/javascript">
			//<![CDATA[
			var disqus_developer = "<?php echo $mode;?>";
			var disqus_url= "<?php echo $link?>";
			var disqus_identifier = "<?php echo $item->id?>";				
			//]]>
		</script>   
		<script type="text/javascript" src="http://disqus.com/forums/<?php echo $javconfig['integrate']->get('account_'.$sytem_comment)?>/embed.js"></script><noscript><a href="http://{subdomain}.disqus.com/?url=ref">View the discussion thread.</a></noscript><a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
		
		
	<?php } elseif($sytem_comment == 'jacomment' && $this->jacomment){?>
			{jacomment contentid=<?php echo $item->id?> option=com_javoice contenttitle=<?php echo $item->title ?>}
			
						
	<?php }elseif($sytem_comment == 'jcomments' && $this->jcomments){		
			$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
			if (file_exists($comments)) {
			  require_once($comments);
			  echo JComments::showComments($item->id, 'com_javoice', $item->title);
			}
				
		}?>
<?php }?>


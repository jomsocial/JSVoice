<?php 
/**
 *$JA#COPYRIGHT$
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="mod_javwork_flow">
	<ul class="clearfix">
		<li class="item1">
				<img src="<?php echo JURI::base();?>modules/mod_javwork_flow/asset/themes/<?php echo $params->get('javthemes', 'default')?>/icon1.png" alt="post search" />
				<p>1. Post/Search</p>
		</li>
		<li class="item2">
				<img src="<?php echo JURI::base();?>modules/mod_javwork_flow/asset/themes/<?php echo $params->get('javthemes', 'default')?>/icon2.png" alt="vote" />
				<p>2. Vote</p>
		</li>
		<li class="item3">
				<img src="<?php echo JURI::base();?>modules/mod_javwork_flow/asset/themes/<?php echo $params->get('javthemes', 'default')?>/icon3.png" alt="comments" />
				<p>3. Comments</p>
		</li>
		<li class="item4">
				<img src="<?php echo JURI::base();?>modules/mod_javwork_flow/asset/themes/<?php echo $params->get('javthemes', 'default')?>/icon4.png" alt="watch" />
				<p>4. Watch</p>
		</li>
	</ul>
</div>
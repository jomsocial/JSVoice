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
// no direct access
defined('_JEXEC') or die('Restricted access');
$voiceTypes=$this->voiceTypes;
$lifetime=$this->lifetime;
$lastest=$this->lastest;
$lastest_spam=$this->lastest_spam;
$lastest_change=$this->lastest_change;
$admin_response = $this->admin_response;
$SmartTrim = new SmartTrim();
echo $this->menu();?>
<br />
	<center>
	<table width="100%">
		<tr>
			<td colspan="6">
				<fieldset>
					<legend><?php echo JText::_("OVERALL_STATISTICS")?></legend>
					<table class='adminlist'>
						<thead>
							<tr>
								<th >
									<?php echo JText::_("VOICE_TYPE")?>
								</th>
								<th>
									<?php echo JText::_("ALL_TIME")?>
								</th>
								<th>
									<?php echo JText::_("SINCE_LAST_LOGGED_IN"). (($this->latvisited == "-62170008124") ? "" : " (".date('d/M/Y',$this->latvisited).")");?>
								</th>																
							</tr>
						</thead>
						<tbody>
							<?php 
								$count=count($voiceTypes);
								for ($i=0;$i<$count;$i++){
									?>
										<tr>
											<td class="key">
												<?php echo $voiceTypes[$i]->title;?>
											</td>
											<td>
												<?php echo $lifetime[$i];?>
											</td>	
											<td>												
												<?php echo $lastest[$i];
													if($lastest[$i]>0){
														?>
															[<a href="index.php?option=com_javoice&view=items&voicetypes=<?php echo $voiceTypes[$i]->id?>&createdate=<?php echo $this->latvisited;?>"><?php echo JText::_("VIEW")?></a>]
														<?php 
													}
												?>
											</td>
										</tr>
									<?php 
								}
							?>
						</tbody>
					</table>
				</fieldset>			
			</td>			
		</tr>
		<tr>
			<td colspan="2" width="33%" valign="top">
				<fieldset style="min-height: 250px;">
					<legend><?php echo JText::_("RECENT_SPAM_UPDATE_CRONT")?></legend>
					<?php 
					$count=count($lastest_spam);
					if( $count>0 ) {						
					?>						
					<div style="min-height: 200px;">
						<ol>
					 		<?php
								for ($i=0;$i<$count; $i++) {
									$item	= $lastest_spam[$i];
						
									JFilterOutput::objectHtmlSafe($item);
									?>
										<li style="line-height: 20px;"><a href="index.php?option=com_javoice&view=items&search=<?php echo $item->ref_id;?>&voicetypes=<?php echo $item->voice_type_id;?>"> 
											<?php 
												if (function_exists ( 'mb_substr' )) {
													$doc = JDocument::getInstance ();
													echo SmartTrim::mb_trim($item->item_title, 0, 50, $doc->_charset);
												}
												else{
													echo SmartTrim::trim($item->item_title, 0, 50);
												}
											?>
										</a>
									<?php 
								}
							?>						
						</ol>
					</div>
					<div style="text-align:right;"><a href="index.php?option=com_javoice&view=actionslog&runby=2&limit=10&limistart=0&filter_order=l.time&filter_order_Dir=DESC"><?php echo JText::_("VIEW_MORE")?></a> </div>
					<?php }else{
						?>
							<div style="min-height:250px;">
								<?php echo JText::_('HAVE_NO_RESULT')?>
							</div>
						<?php
					}
					?>						
				</fieldset>
			</td>
			<td colspan="2" width="33%" valign="top">
				<fieldset style="min-height: 250px;">
					<legend><?php echo JText::_("RECENT_STATUS_UPDATES_BY_ADMIN")?></legend>
					<?php 
					$count=count($lastest_change);					
					if( $count>0 ) {						
					?>						
					<div style="min-height:200px;">
						<ol>
					 		<?php
								foreach ($lastest_change as $item_change) {
						
									JFilterOutput::objectHtmlSafe($item_change);
									?>
										<li style="line-height: 20px;">
											<a href="index.php?option=com_javoice&view=items&search=<?php echo $item_change->ref_id;?>&voicetypes=<?php echo $item_change->voice_type_id;?>"> 
												<?php echo $item_change->item_title;?>&nbsp;
											</a>
											</li>
									<?php 
								}
							?>						
						</ol>
					</div>
					<div style="text-align:right;"><a href="index.php?option=com_javoice&view=actionslog&runby=1&limit=10&limistart=0&filter_order=l.time&filter_order_Dir=DESC"><?php echo JText::_("VIEW_MORE_")?></a> </div>
					<?php }else{
						?>
							<div style="min-height:250px;">
								<?php echo JText::_('HAVE_NO_RESULT')?>
							</div>
						<?php
					}
					?>					
				</fieldset>			
			</td>
			<td colspan="2" width="33%" valign="top">
				<fieldset style="min-height:250px;">
					<legend><?php echo JText::_("RECENT_ADMIN_RESPONSES")?></legend>
					<?php 
					$count=count($admin_response);
					if( $count>0 ) {						
					?>					
					<div style="min-height: 200px;">
						<ol>
					 		<?php
							for ($i=0;$i<$count; $i++) {
								$item	= $admin_response[$i];
					
								JFilterOutput::objectHtmlSafe($item);
								?>
									<li style="line-height: 20px;"><a href="index.php?option=com_javoice&view=items&voicetypes=<?php echo $item->voice_types_id?>&search=<?php echo $item->item_id ?>"> 
										<?php 
											if (function_exists ( 'mb_substr' )) {
												$doc = JDocument::getInstance ();
												echo SmartTrim::mb_trim($item->item_title, 0, 50, $doc->_charset);
											}
											else{
												echo SmartTrim::mb($item->item_title, 0, 50);
											}
										?>
									</a>
								<?php 
							}
							?>						
						</ol>
					</div>					
					<?php }else{
						?>
							<div style="min-height: 250px;">
								<?php echo JText::_('HAVE_NO_RESULT')?>
							</div>
						<?php
					}
					?>
				</fieldset>					
			</td>
		</tr>
	</table>
</center>
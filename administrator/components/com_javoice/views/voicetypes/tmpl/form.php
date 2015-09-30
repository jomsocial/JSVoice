<?php // no direct access
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
$item=$this->item;
$params=$this->params;
$display=FALSE;
JHTML::_('behavior.tooltip');
?> 

 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="voicetypes" /> 

 	<input type="hidden" name="task" value="saveIFrame" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"> 

	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $item->id?>"> 
	
	<input type="hidden" name="number" value="<?php echo $this->number;?>">
	
	<input type="hidden" name="tmpl" value="component" /> 
	
	<div><h2><?php echo JText::_("GENERAL_SETTINGS")?></h2></div>

	
	<ul>
		<li class="ja-haftleft">
			<label class="desc" for="title"><?php echo JText::_("TITLE" );?> <font color="red">*</font></label>
			<div><input onblur="checkdataString(this,'error')" type="text" name="title" id='title' size='57' value="<?php echo $item->title?>" class="text required"></div>
		</li>
		<li class="ja-haftright">
			<label class="desc" for="search_title"><?php echo JText::_("PUBLISHED" );?></label>
			<div>
			<?php
				$published = ($item->published==1) ? $item->published : 0;
				$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
				echo $lists['published'];
			?>
			</div>
		</li>
		<li class="ja-haftleft">
			<label class="desc" for="alias"><?php echo JText::_("ALIAS" );?></label>
			<div><input type="text" name="alias" id='alias' size='57' value="<?php echo $item->alias?>" class="text"></div>
		</li>		
		<li class="ja-haftright">
			<div class="ja-haftleft">
				<label class="desc" for="title">
					<span class="editlinktip hasTip" title="<?php echo JText::_('COMMENT_OVERRIDE' );?>::<?php echo JText::_('COMMENT_OVERRIDE_TOOLTIP' ); ?>">
						<?php echo JText::_('COMMENT_OVERRIDE' );?>
					</span>				
				</label>
				<div><input type="text" name="language_response" class="text" id='language_response' size='20' value="<?php echo $item->language_response?>"></div>
			</div>
			
			<div class="ja-haftright">
				<label for="has_answer" class="desc editlinktip hasTip" title="<?php echo JText::_('VOICE_HAS_BEST_ANSWER_TOOLTIP' );?>"><?php echo JText::_("VOICE_HAS_BEST_ANSWER" );?> <font color="red">*</font></label>
				<div>
				<?php
					$has_answer = ($item->has_answer==1) ? $item->has_answer : 0;
					$lists['has_answer'] = JHTML::_('select.booleanlist',  'has_answer', 'class="inputbox"', $has_answer );
					echo $lists['has_answer'];
				?>
				</div>
			</div>
			
		</li>
		
		
		<li style="display: block; clear: both;">
			<label for="description" class=" desc editlinktip hasTip" title="<?php echo JText::_('INSTRUCTION_MESSAGE_TOOLTIP' );?>"><?php echo JText::_("INSTRUCTION_MESSAGE" );?></label>
			<div><textarea rows="1" name='description' cols="70" class="textarea"><?php echo $item->description;?></textarea></div>
		</li>
		<li class="ja-haftleft">
			<label for="search_title" class=" desc editlinktip hasTip" title="<?php echo JText::_('SEARCH_HINTS_TOOLTIP' );?>"><?php echo JText::_("SEARCH_HINTS" );?></label>
			<div><input type="text" name="search_title" id='search_title' size='57' value="<?php echo $item->search_title?>" class="text"></div>
		</li>
		<li class="ja-haftright">
			<label for="search_button"  class=" desc editlinktip hasTip" title="<?php echo JText::_('SEARCH_BUTTON_TOOLTIP' );?>"><?php echo JText::_("SEARCH_BUTTON" );?> <font color="red">*</font></label>
			<div><input type="text" name="search_button" id='search_button' size='30' value="<?php echo $item->search_button?>" class="text"></div>
		</li>
		<li>
			<label for="search_description"  class=" desc editlinktip hasTip" title="<?php echo JText::_('INSTRUCTION_TOOLTIP' );?>"><?php echo JText::_("INSTRUCTION" );?></label>
			<div><textarea rows="1" name='search_description' cols="70" class="textarea"><?php echo $item->search_description;?></textarea></div>
		</li>
		
	</ul>	

	<h2><?php echo JText::_("VOTE_SETTINGS")?></h2>
	
	<ul>
		<li class="ja-haftleft">
			<label class="desc" for="total_votes"><span class="editlinktip hasTip" title="<?php echo JText::_('VOTES_QUOTA');?>::<?php echo JText::_('VOTES_QUOTA_TOOLTIP') ;?>"><?php echo JText::_("VOTES_QUOTA" );?> <font color="red">*</font></span></label>
			<div><input type="text" name='total_votes' id='total_votes' value="<?php echo $item->total_votes;?>" class="text"></div>
		</li>		
		<li class="ja-haftright">
			
		</li>
		<li style="display: block; width: 100%; clear: both;">
			<fieldset class="adminform">
			<legend>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VOTES_OPTION' );?>::<?php echo JText::_('VOTES_OPTION_TOOLTIP' ); ?>">
					<?php echo JText::_("VOTES_OPTION" );?> <font color="red">*</font>
				</span>		
				
			</legend>
			<div id='jav-div-vote-option'> 	
				<span class='jav-span-value editlinktip hasTip' title="<?php echo JText::_('VALUE' );?>::<?php echo JText::_('VALUE_TOOLTIP' ); ?>">
					<?php echo JText::_("VALUE")?>
				</span>
				<span class='jav-span-title editlinktip hasTip' title="<?php echo JText::_('TEXT' );?>::<?php echo JText::_('TEXT_TOOLTIP' ); ?>">
					<?php echo JText::_("TEXT")?>
				</span>
				<span class='jav-span-title-description editlinktip hasTip' title="<?php echo JText::_('DESCRIPTION' );?>::<?php echo JText::_('DESCRIPTION_TOOLTIP' ); ?>">
					<?php echo JText::_("DESCRIPTION")?>
				</span>
										
				<ul class="ss-magiclist-ul" id='jav-ul-vote-option'>
					<?php 
					if($this->count_vote_option>0){
						if($this->count_vote_option == 1)$display="style='display:none'";
						for($j=0;$j<$this->count_vote_option;$j++){?>
						<li class='jav-li-vote-option'>
							<span>
								<input onblur="checkdataInt(this,'error',2)" name='votes_value[]' value="<?php echo $this->votes_value[$j]?>"  class="required jav-input-value text duplicate" type="text" style="opacity: 1;"/>
							</span>
							<span>
							<input onblur="checkdataString(this,'error',2)" name='votes_text[]' value="<?php echo $this->votes_text[$j]?>"  class="required jav-input-title text" type="text" style="opacity: 1;"/>
							</span>
							<span>
							<input name='votes_description[]' value="<?php echo $this->votes_description[$j]?>"class=" jav-input-description text" type="text" style="opacity: 1;"/>
							</span>	 							
							<span class="jav-x-close" <?php echo $display;?>> 
								<a onclick="removeoption(this);return false;" href="#"> 
									<img border="0" alt="Remove" src="components/com_javoice/asset/images/publish_x.png"/>
								</a>
							</span>
							<br class="clr" />
						</li>
						<?php }
					}?>
					<li style="opacity: 0.3;">
						<span>
							<input onblur="checkdataInt(this,'error',2)" onfocus="addvoteoption(this);" class="jav-input-value text duplicate" size="70" type="text" />
						</span>
						<span>
							<input onblur="checkdataString(this,'error',2)" onfocus="addvoteoption(this);"  class="jav-input-title text" type="text" />
						</span>
						<span>
						<input onfocus="addvoteoption(this);"  class=" jav-input-description text" type="text" />
						</span>	
						<span class="jav-x-close" style='display:none;'> 
							<a onclick="removeoption(this);return false;" href="#" title="<?php echo JText::_('CLICK_HERE_TO_REMOVE_THE_RECORD')?>"> 
								<img border="0" alt="Remove" src="components/com_javoice/asset/images/publish_x.png"/>
							</a>
						</span>	 								 							
						<br class="clr" />
					</li> 	
										
				</ul>
			</div>
			</fieldset>
		</li>	
		
	</ul>
	
	<div style="clear:both;"></div>	

 </form>
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
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

$EMAIL_TEMPLATES_CONFIG = '

== TAGSET configs ==
CONFIG_ADMIN_EMAIL - Administrator email
CONFIG_SITE_CONTACT_EMAIL - Site \'s email
CONFIG_SITE_TITLE - Site Title
CONFIG_ROOT_URL - Root URL of the script
EMAIL_PREFERENCE_LINK - Email preference
== TAGSET user ==
USERS_USERNAME - User\'s username
USERS_EMAIL - User\'s email
== TAGSET item_content ==
ITEM_TITLE - Title
ITEM_TITLE_WITH_LINK - Title with link
ITEM_FORUM - forum
ITEM_VOICE_TYPE - Voice Type
ITEM_DESCRIPTON - Description
ITEM_CREATE_DATE - Create Date
ITEM_CREATE_BY - Create By
ITEM_NUM_OF_VOTES - Number Of Votes
ITEM_NUM_OF_VOTES_DOWN - Number Of Votes Down
ITEM_TOTAL_VOTE_DOWN - Total Vote Down
ITEM_NUM_OF_VOTES_UP - Number Of Votes Up
ITEM_TOTAL_VOTE_UP - Total Vote Up
ITEM_NEW_STATUS_WITH_COLOR - New Status 
ITEM_NEW_STATUS - New Status 
ITEM_OLD_STATUS - Old Status 
== TAGSET item_details ==
ITEM_DETAILS - item details
==EMAIL TAGS user,item_content,configs==
Javnotify_to_admin_new_item:javnotify_to_admin_new_item.txt - Notify to admin when new item has just posted
==EMAIL TAGS user,item_content,configs==
Javnotify_to_user_item_change_status:javnotify_to_user_item_change_status.txt - Notify to users when item change status
==EMAIL TAGS user,configs,item_details==
Javnotify_to_user_new_voice_weekly:Javnotify_to_user_new_voice_weekly.txt - Notify to users for history item
==EMAIL TAGS user,configs,item_details==
Javnotify_to_user_new_voice_daily:Javnotify_to_user_new_voice_daily.txt - Notify to users for history item
==EMAIL TAGS configs==
mailheader:mailheader.txt - Header
==EMAIL TAGS configs==
mailfooter:mailfooter.txt - Footer
';


$PARSED_EMAIL_TEMPLATES_CONFIG = array(
    'tagset' => array(),
    'emails' => array(),
);
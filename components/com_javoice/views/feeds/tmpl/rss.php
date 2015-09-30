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
if(!defined("TIME_ZONE")){
	define("TIME_ZONE", "");
}
global $javconfig;
$mainframe = JFactory::getApplication();
$helper = new JAVoiceHelpers ( );
$content = $this->content;
//make a feed id based filename


$rss = new UniversalFeedCreator ( );

//Use cache if docache is set to 1
if (intval ( $content->feed_cache ) == 1) {
	$rss->useCached ( $content->feed_type, $content->filename, $content->feed_cache ); // use cached version if age<1 hour. May not return!
}

$rss->title = $content->feed_title;
$rss->description = $content->feed_description;
$rss->link = $content->feed_link;
$rss->syndicationURL = $_SERVER ['SERVER_NAME'] . $_SERVER ["PHP_SELF"];
$rss->descriptionHtmlSyndicated = true;

$image = new FeedImage ( );
$image->title = $content->image_title;
$image->url = $content->image_url;
$image->link = $content->image_href;
$image->description = $content->image_description;
$image->descriptionHtmlSyndicated = true;

if (isset($content->imgUrl) && $content->imgUrl != "") {
	$rss->image = $image;
}

$rows = $content->rsscontents;
if (is_array ( $rows ))
	foreach ( $rows as $row ) {
		$item = new FeedItem ( );
		$item->title = $row->title;
		
		$item->link = $row->link;
		
		$item->guid = $row->guid;
		
		$AddReadMoreLink = false;
		$words = $row->content;
		//add BBCODE
		if ($javconfig ['plugin']->get ( 'enable_bbcode', 1 )) {
			$words = $helper->showItem ( $words );
		}
		
		if (str_word_count ( trim ( $words ) ) > $content->feed_numWords) {
			
			$AddReadMoreLink = true;
			
			$words = word_limiter ( $words, $content->feed_numWords );
		
		}
		
		$words = addAbsoluteURL ( $words );
		
		$item->description = $words;
		$item->descriptionHtmlSyndicated = true;
		$item->date = $row->date;
		$item->source = $row->source;
		$author = trim ( $row->user_id );
		
		//		if (empty($author)) $author = $row->author;
		

		$temp_user = JFactory::getUser ( $row->user_id );
		$item->author = ($content->feed_renderAuthorFormat == 'NAME') ? $temp_user->user_name : (($content->feed_renderAuthorFormat == 'EMAIL') ? $temp_user->email : $author . ' <' . $temp_user->email . '>');
		$rss->addItem ( $item );
	}

$rss->saveFeed ( $this->type, $content->filename, true );

function noHTML($words) {
	$words = preg_replace ( "'<script[^>]*>.*?</script>'si", "", $words );
	$words = preg_replace ( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $words );
	$words = preg_replace ( '/<!--.+?-->/', '', $words );
	$words = preg_replace ( '/{.+?}/', '', $words );
	$words = strip_tags ( $words );
	$words = preg_replace ( '/&nbsp;/', ' ', $words );
	$words = preg_replace ( '/&amp;/', '&', $words );
	$words = preg_replace ( '/&quot;/', '"', $words );
	
	return $words;
}

function addAbsoluteURL($html) {
	$root_url = JURI::root ();
	$html = preg_replace ( '@href="(?!http://)(?!https://)([^"]+)"@i', "href=\"{$root_url}\${1}\"", $html );
	$html = preg_replace ( '@src="(?!http://)(?!https://)([^"]+)"@i', "src=\"{$root_url}\${1}\"", $html );
	
	return $html;
}

/*
** Delete all the images from the url
*/
function delImagesFromHTML($html) {
	$html = preg_replace ( '/<img\\s.*>/i', '', $html );
	
	return $html;
}

/* >> MAD 2007/10/09
 * Added function word_limiter
 */
function word_limiter($string, $limit = 100) {
	$words = array ();
	$string = preg_replace ( "/ +/", " ", $string );
	$array = explode ( " ", $string );
	//$limit = (count($array) <= $numwords) ? count($array) : $numwords;
	for($k = 0; $k < $limit; $k ++) {
		if ($limit > 0 && $limit == $k)
			break;
		if (preg_match ( "[0-9A-Za-zÀ-ÖØ-öø-ÿ]", $array [$k] ))
			$words [$k] = $array [$k];
	}
	$txt = implode ( " ", $words );
	return $txt;
}
?>
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
defined('_VALID_MOS') or defined('_JEXEC') or die('Restricted access');

if (! defined('_JA_BASE_MENU_CLASS')) {
	define('_JA_BASE_MENU_CLASS', 1);
	
	/**
	 * JAVMenu class
	 * 
	 * @package		Joomla.Administrator
	 * @subpackage	JAComment
	 */
	class JAVMenu
	{
		var $_menu = null;
		var $_activeMenu = null;
		
		/**
		 * Load menu
		 * 
		 * @return void
		 */
		static function _menu()
		{
			$menu = new JAVMenu();
			$menu->_loadMenu();
			$menu->genMenuId($menu->_menu);
			$menu->genMenuItems($menu->_menu);
		}
		
		/**
		 * Load menu from XML file
		 * 
		 * @return void
		 */
		function _loadMenu()
		{
			if (version_compare(JVERSION, '3.0', '<')) {
				jimport('joomla.utilities.simplexml');
			}
			
			$xmlfile = dirname(__FILE__) .  '/menu.xml';
			
			$xml = new JSimpleXML();
			$xml->loadFile($xmlfile);
			if (! $xml->document) {
				echo "Cannot load menu xml: $xmlfile";
				return;
			}
			$this->_menu = $xml->document;
			//include the dynamic menu
			include dirname(__FILE__) .  '/dynamic_menu.php';
		}
		
		/**
		 * Get menu id
		 * 
		 * @param object &$item Menu item
		 * 
		 * @return void
		 */
		function genMenuId(&$item)
		{
			$temp = array();
			foreach ($item->children() as $child) {
				$child->parent = $item;
				$child->menuId = '';
				$child->menuId = md5($this->getlink($child) . $this->gettitle($child));
				if (isset($child->menuId) && isset($_SESSION['menuId']) && ($child->menuId == $_SESSION['menuId'])) {
					$this->_activeMenu[] = $child->menuId;
					$this->updateActiveMenu($child);
				}
				$this->genMenuId($child);
				$temp[] = $child;
			}
			$item->_children = $temp;
		}
		
		/**
		 * Update active menu for current item and its parent items
		 * 
		 * @param object $item Menu item
		 * 
		 * @return void
		 */
		function updateActiveMenu($item)
		{
			if (isset($item->parent->menuId) && $item->parent->menuId != "") {
				$this->_activeMenu[] = $item->parent->menuId;
				$this->updateActiveMenu($item->parent);
			}
		}
		
		/**
		 * Add an item into parent item
		 * 
		 * @param string $parentname Parent item name
		 * @param array  $attrs 	 Item attributes
		 * 
		 * @return void
		 */
		function addItem($parentname, $attrs)
		{
			if ($parentname) {
				$parent = $this->findElementByAttribute($this->_menu, 'name', $parentname);
			} else {
				$parent = $this->_menu;
			}
			
			if ($parent) {
				$parent->addChild('item', $attrs);
			}
		}
		
		/**
		 * Find an element by attribute
		 * 
		 * @param object $item  Menu item
		 * @param string $attr  Attribute name
		 * @param string $value Attribute value
		 * 
		 * @return mixed Return item if it is found, otherwise return NULL
		 */
		function findElementByAttribute($item, $attr, $value)
		{
			if (strtolower($item->attributes($attr)) == strtolower($value)) {
				return $item;
			}
			foreach ($item->children() as $child) {
				if (($found = $this->findElementByAttribute($child, $attr, $value))) {
					return $found;
				}
			}
			return null;
		}
		
		/**
		 * Generate menu items
		 * 
		 * @param object $menu Menu object
		 * 
		 * @return void
		 */
		function genMenuItems($menu)
		{
			if (! $menu || ! $menu->children()) {
				return;
			}
			
			$this->beginMenuItems($menu);
			$i = 0;
			foreach ($menu->children() as $item) {
				if ($item->name() != 'item') {
					continue;
				}
				if ($i++ == 0) {
					$item->addAttribute('first', true);
				}
				
				$this->beginMenuItem($item);
				$this->genMenuItem($item);
				
				// show menu with menu expanded - submenus visible
				$this->genMenuItems($item);
				
				$this->endMenuItem($item);
			}
			$this->endMenuItems($menu);
		}
		
		/**
		 * Generate HTML code for menu item
		 * 
		 * @param object $item Menu item
		 * 
		 * @return void
		 */
		function genMenuItem($item)
		{
			?>
			<a href="<?php echo $this->getlink($item); ?>" <?php echo $this->getclass($item); ?> title="">
				<span><?php echo JText::_($this->gettitle($item)); ?></span>
			</a>
			<?php
		}
		
		/**
		 * HTML code for beginning of menu items list
		 * 
		 * @return void
		 */
		function beginMenuItems()
		{
			echo "<ul>";
		}
		
		/**
		 * HTML code for end of menu items list
		 * 
		 * @return void
		 */
		function endMenuItems()
		{
			echo "</ul>";
		}
		
		/**
		 * HTML code for beginning of menu item
		 * 
		 * @param object $item Menu item
		 * 
		 * @return void
		 */
		function beginMenuItem($item = null)
		{
			echo "<li " . $this->getclass($item) . ">";
		}
		
		/**
		 * HTML code for end of menu item
		 * 
		 * @param object $mitem Menu item
		 * 
		 * @return void
		 */
		function endMenuItem($mitem = null)
		{
			echo "</li>";
		}
		
		/**
		 * Get css class of menu item
		 * 
		 * @param object $item Menu item
		 * 
		 * @return string Css class of menu item
		 */
		function getclass($item)
		{
			$cls = $item->attributes('class');
			
			if ($item->attributes('first')) {
				$cls .= ' first';
			}
			if (count($item->children())) {
				$cls .= ' havechild';
			}
			
			if (is_array($this->_activeMenu)) {
				if (isset($this->_activeMenu[1])) {
					if ($item->menuId == $this->_activeMenu[1]) {
						$cls .= ' active opened';
					}
				} else {
					if (isset($this->_activeMenu[0])) {
						if ($item->menuId == $this->_activeMenu[0]) {
							$cls .= ' active opened';
						}
					}
				}
			}
			
			if (JRequest::getVar("group", "")) {
				$findWord = "group=" . JRequest::getVar("group", "");
				if (strpos($item->attributes('link'), $findWord) !== false) {
					$cls .= ' active ';
				}
			} else if (JRequest::getVar("layout", "")) {
				$findWord = "layout=" . JRequest::getVar("layout", "");
				if (strpos($item->attributes('link'), $findWord) !== false) {
					$cls .= ' active ';
				}
			} else {
				if (isset($this->_activeMenu[0])) {
					if ($item->menuId == $this->_activeMenu[0]) {
						$cls = str_replace("active", "", $cls);
						$cls .= ' active opened';
					}
				}
			}
			
			$cls = trim($cls) ? 'class="' . trim($cls) . '"' : '';
			return $cls;
		}
		
		/**
		 * Get link of menu item
		 * 
		 * @param object $item Menu item
		 * 
		 * @return string Link of menu item
		 */
		function getlink($item)
		{
			$link = $item->attributes('link');
			if (! isset($item->menuId)) {
				$item->menuId = 0;
			}
			if ($link != "") {
				$link .= "&amp;menuId=" . $item->menuId;
			} else {
				$link = "menuId=" . $item->menuId;
			}
			return "index.php?$link";
		}
		
		/**
		 * Get title of menu item
		 * 
		 * @param object $item Menu item
		 * 
		 * @return string Link of menu item
		 */
		function gettitle($item)
		{
			return $item->attributes('title');
		}
	}
}
?>

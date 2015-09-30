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

// Try extending time, as unziping/ftping took already quite some... :
@set_time_limit( 0 );
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class Com_javoiceInstallerScript
{
	function postflight($type, $parent) {
		if(version_compare( JVERSION, '3.0.0', '>' )){
			require_once( JPATH_SITE .'/components/com_javoice/helpers/jahelper.php');
			JAVoiceHelpers::Install_Db();
			$messages = array();
			
			// Import required modules
			jimport('joomla.installer.installer');
			jimport('joomla.installer.helper');
			jimport('joomla.filesystem.file');
			// Get packages
			$p_dir = JPath::clean(JPATH_SITE.'/components/com_javoice/packages');
			// Did you give us a valid directory?
			if (!is_dir($p_dir)){
				$messages[] = JText::_('Package directory(Related modules, plugins) is missing');
			}
			else {
				$subpackages = JFolder::files($p_dir);
				$result = true;
				$installer = new JInstaller();
				if ($subpackages) {			
					$app = JFactory::getApplication();
					$templateDir = 'templates/'.$app->getTemplate();
					
					foreach ($subpackages as $zpackage) {
						if (JFile::getExt($p_dir.DS.$zpackage) != "zip") {
							continue;
						}
						$subpackage = JInstallerHelper::unpack($p_dir.DS.$zpackage);
						if ($subpackage) {
							$type = JInstallerHelper::detectType($subpackage['dir']);
							if (! $type) {
								$messages[] = '<img src="'.$templateDir.'/images/admin/publish_x.png" alt="" width="16" height="16" />&nbsp;<span style="color:#FF0000;">'.JText::_($zpackage." Not valid package") . '</span>';
								$result = false;
							}
							if (! $installer->install($subpackage['dir'])) {
								// There was an error installing the package
								$messages[] = '<img src="'.$templateDir.'/images/admin/publish_x.png" alt="" width="16" height="16" />&nbsp;<span style="color:#FF0000;">'.JText::sprintf('Install %s: %s', $type." ".JFile::getName($zpackage), JText::_('Error')).'</span>';
							}
							else {
								$messages[] = '<img src="'.$templateDir.'/images/admin/tick.png" alt="" width="16" height="16" />&nbsp;<span style="color:#00FF00;">'.JText::sprintf('Install %s: %s', $type." ".JFile::getName($zpackage), JText::_('Success')).'</span>';
							}
							
							if (! is_file($subpackage['packagefile'])) {
								$subpackage['packagefile'] = $p_dir.DS.$subpackage['packagefile'];
							}
							if (is_dir($subpackage['extractdir'])) {
								JFolder::delete($subpackage['extractdir']);
							}
							if (is_file($subpackage['packagefile'])) {
								JFile::delete($subpackage['packagefile']);
							}
						}
					}			
				}
				JFolder::delete($p_dir);
			}
		}
	}
	public function uninstall($parent){
		if(version_compare( JVERSION, '3.0.0', '>' )){
			jimport('joomla.installer.installer');
			jimport('joomla.installer.helper');
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			$db = JFactory::getDBO();
			$messages = array();

			$arrPackages = array("mod_javlist_voices","mod_javwork_flow");

			$eids = array();

			foreach ($arrPackages as $package){
				$type = substr($package, 0, 3);
				switch ($type){
					case "mod":
						$db->setQuery("SELECT extension_id, `name` FROM #__extensions WHERE `type` = 'module' AND `element` = '".$package."'");
						$el = $db->loadColumn();
						if (count($el))
						{
							foreach ($el as $id)
							{
								$installer = new JInstaller;
								$result = $installer->uninstall('module', $id);
								$messages[] = JText::_('Uninstalling module "'.$package.'" was successful.');
							}
							
						}
						break;
					case "plg":
						$info = explode("_", $package);
						if (count($info) >= 3) {
							$info[2] = str_replace($info[0]."_".$info[1]."_", "", $package);
							$db->setQuery("SELECT extension_id, `name` FROM #__extensions WHERE `type` = 'plugin' AND `element` = '".$info[2]."' AND `folder` = '".$info[1]."' ");
							$extensions = $db->loadColumn();
							if (count($extensions))
							{
								foreach ($extensions as $id)
								{
									$installer = new JInstaller;
									$result = $installer->uninstall('plugin', $id);
									$messages[] = JText::_('Uninstalling plugin "'.$package.'" was successful.');
								}
								
							}
						}
						
						break;
				}
			}
			?>
			 <div style="text-align:left;">
				<table width="100%" border="0" style="line-height:200%; font-weight:bold;">
					<tr>
					  <td align="center">
							Uninstalling JA Voice
							<?php
							if (count($messages) > 1) {
								echo ' and all related modules, plugins were';
							}
							else {
								echo ' was';
							}
							echo ' successful.<br />';
							
							echo implode("<br />", $messages);
							?>
							<br />
					  </td>
					</tr>
				</table>
			 </div>
			<?php 
		}
	}
}
?>

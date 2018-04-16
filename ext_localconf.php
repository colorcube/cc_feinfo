<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugin.php', '_pi1', 'list_type', 0);
// addPItoST43 can't handle non pi1 class names
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', 'plugin.tx_ccfeinfo_pi1.userFunc = Colorcube\CcFeinfo\Plugin->main');
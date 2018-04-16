<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'Debug/Info output',
    $_EXTKEY . '_pi1',
    'EXT:' . $_EXTKEY . '/ext_icon.png'
),'list_type');
<?php

namespace Colorcube\CcFeinfo;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 *
 *
 * @author    Rene Fritz <r.fritz@colorcube.de>
 */


class FeInfo
{

    var $pObj;

    var $types = array(
        'gpvar' => 'GET/POST Vars',
        'user' => 'User',
        'constants' => 'Constants',
        'globjects' => 'Global Objects',
        'locals' => 'Local Variables',
        'globals' => 'Global Variables',
        'tsfe' => 'TSFE (excerpt)',
        'rootline' => 'Rootline',
        'env' => 'Environment',
        'debuginfo' => 'Debug info',
        'phpinfo' => 'PHPInfo',
    );


    function init(&$pObj)
    {

        $this->pObj = &$pObj;

        if (!is_object($GLOBALS['TSFE'])) {
            unset($this->types['tsfe']);
            unset($this->types['rootline']);
            unset($this->types['debuginfo']);
        }

        if ($this->pObj->prefixId) {
            $this->prefixId = $this->pObj->prefixId . '_' . $this->prefixId;
        }
    }


    function information($type, $level = 2)
    {
        global $HTTP_GET_VARS, $HTTP_POST_VARS;

        $content = '';

        switch (TYPO3_MODE . ':' . $type) {

            case 'FE:gpvar':
            case 'BE:gpvar':
                $out = $this->debugvar($_GET, $name = 'GET Variables', $level);
                $content .= $this->section('GET Variables:', $out);
                $out = $this->debugvar($_POST, $name = 'POST Variables', $level);
                $content .= $this->section('POST Variables:', $out);
                break;

            case 'BE:constants':
            case 'FE:constants':
                $const = get_defined_constants(true);
                $const = $const['user'];
                $out = $this->debugvar($const, $name = 'Constants', $level);
                $content .= $this->section('Constants:', $out);
                break;


            case 'FE:user':
            case 'BE:user':
                global $BE_USER;

                $user = array();
                if (is_object($GLOBALS['TSFE'])) {
                    $user['fe_user->user'] = $GLOBALS['TSFE']->fe_user->user;
                    $user['fe_user->groupData'] = $GLOBALS['TSFE']->fe_user->groupData;
                }
                if (is_object($BE_USER)) {
                    $user['BE_USER->user'] = $BE_USER->user;
                    $user['BE_USER->groupData'] = $BE_USER->groupData;
                }
                $out = $this->debugvar($user, $name = 'User', $level);
                $content .= $this->section('User:', $out);
                break;

            case 'FE:tsfe':

                $tsfe = array();
                $tsfe['id'] = $GLOBALS['TSFE']->id;
                $tsfe['type'] = $GLOBALS['TSFE']->type;
                $tsfe['no_cache'] = $GLOBALS['TSFE']->no_cache;
                $tsfe['page'] = $GLOBALS['TSFE']->page;
                $tsfe['fe_user->user'] = $GLOBALS['TSFE']->fe_user->user;
                $tsfe['fe_user->groupData'] = $GLOBALS['TSFE']->fe_user->groupData;
                $tsfe['fe_user:gr_list'] = $GLOBALS['TSFE']->gr_list;

                $out = $this->debugvar($tsfe, $name = 'TSFE (excerpt)', $level);
                $content .= $this->section('TSFE (excerpt):', $out);
                break;

            case 'FE:rootline':
                $out = $this->debugvar($GLOBALS['TSFE']->rootLine, $name = 'Rootline', $level);
                $content .= $this->section('Rootline:', $out);
                break;


            case 'FE:locals':
            case 'BE:locals':
                global $SOBE;

                if (is_object($this->pObj)) {
                    $objArray = array();
                    foreach ($this->pObj as $objName => $obj) {
                        if (!is_object($obj)) {
                            $objArray['$this->' . $objName] = $obj;
                        }
                    }
                    $out = $this->debugvar($objArray, $name = 'Local Variables', $level);
                    $content .= $this->section('Local Variables', $out);
                }
                break;

            case 'FE:globals':
            case 'BE:globals':
                $objArray = array();
                foreach ($GLOBALS as $objName => $obj) {
                    if (!is_object($obj)) {
                        $objArray['$' . $objName] = $obj;
                    }
                }
                $out = $this->debugvar($objArray, $name = 'Global Variables', $level);
                $content .= $this->section('Global Variables:', $out);
                break;


            case 'FE:env':
            case 'BE:env':

                $getEnvArray = array();
                $gE_keys = explode(',', 'QUERY_STRING,HTTP_ACCEPT,HTTP_ACCEPT_ENCODING,HTTP_ACCEPT_LANGUAGE,HTTP_CONNECTION,HTTP_COOKIE,HTTP_HOST,HTTP_USER_AGENT,REMOTE_ADDR,REMOTE_HOST,REMOTE_PORT,SERVER_ADDR,SERVER_ADMIN,SERVER_NAME,SERVER_PORT,SERVER_SIGNATURE,SERVER_SOFTWARE,GATEWAY_INTERFACE,SERVER_PROTOCOL,REQUEST_METHOD,SCRIPT_NAME,PATH_TRANSLATED,HTTP_REFERER,PATH_INFO');
                foreach ($gE_keys as $k) {
                    $getEnvArray[$k] = getenv($k);
                }

                $out = $this->debugvar(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('_ARRAY'), $name = '\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv()', $level);
                $content .= $this->section('GeneralUtility::getIndpEnv():', $out);

                $out = $this->debugvar($getEnvArray, $name = 'getenv()', $level);
                $content .= $this->section('getenv():', $out);

                $out = $this->debugvar($_ENV, $name = '$_ENV', $level);
                $content .= $this->section('$_ENV:', $out);

                $out = $this->debugvar($_SERVER, $name = '$_SERVER', $level);
                $content .= $this->section('$_SERVER:', $out);

                $out = $this->debugvar($_COOKIE, $name = '$_COOKIE', $level);
                $content .= $this->section('$_COOKIE:', $out);

                break;


            case 'FE:globjects':
            case 'BE:globjects':
                /*
                    $objArray = array();
                    while(list($objName,$obj)=each($this))	{
                        if(is_object($obj)){
                            $objArray['$this->'.$objName] = $obj;
                        }
                    }
                    $out = $this->debugvar($objArray, $name = 'Local Objects', $level);
                    $content .= $this->section('Local Objects', $out);
                    $content .= $this->divider();
                */

                $objArray = array();
                foreach ($GLOBALS as $objName => $obj) {
                    if (is_object($obj)) {
                        $objArray['$' . $objName] = $obj;
                    }
                }
                $out = $this->debugvar($objArray, $name = 'Global Objects', $level);
                $content .= $this->section('Global Objects:', $out);
                break;


            case 'FE:debuginfo':

                $sVar = (array)\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('_ARRAY');
                $sVar['CONST: PHP_OS'] = PHP_OS;
                $sVar['CONST: TYPO3_OS'] = TYPO3_OS;
                $sVar['CONST: PATH_thisScript'] = PATH_thisScript;
                $sVar['CONST: php_sapi_name()'] = php_sapi_name();
                $sVar['OTHER: TYPO3_VERSION'] = $GLOBALS['TYPO_VERSION'];
                $sVar['OTHER: PHP_VERSION'] = phpversion();
                $sVar['imagecreatefromgif()'] = function_exists('imagecreatefromgif');
                $sVar['imagecreatefrompng()'] = function_exists('imagecreatefrompng');
                $sVar['imagecreatefromjpeg()'] = function_exists('imagecreatefromjpeg');
                $sVar['imagegif()'] = function_exists('imagegif');
                $sVar['imagepng()'] = function_exists('imagepng');
                $sVar['imagejpeg()'] = function_exists('imagejpeg');
                $sVar['imagettftext()'] = function_exists('imagettftext');
                $sVar['OTHER: IMAGE_TYPES'] = imagetypes();
                $sVar['OTHER: memory_limit'] = get_cfg_var('memory_limit');

                $gE_keys = explode(',', 'SERVER_PORT,SERVER_SOFTWARE,GATEWAY_INTERFACE,SCRIPT_NAME,PATH_TRANSLATED');
                foreach ($gE_keys as $k) {
                    $sVar['SERVER: ' . $k] = $GLOBALS['HTTP_SERVER_VARS'][$k];
                }

                $gE_keys = explode(',', 'image_processing,gdlib,gdlib_png,gdlib_2,im,im_path,im_path_lzw,im_version_5,im_negate_mask,im_imvMaskState,im_combine_filename');
                foreach ($gE_keys as $k) {
                    $sVar['T3CV_GFX: ' . $k] = $GLOBALS['TYPO3_CONF_VARS']['GFX'][$k];
                }

                $debugInfo = array();
                $debugInfo[] = '### DEBUG SYSTEM INFORMATION - START ###';
                foreach ($sVar as $kkk => $vvv) {
                    $debugInfo[] = str_pad(substr($kkk, 0, 20), 20) . ': ' . $vvv;
                }
                $debugInfo[] = '### DEBUG SYSTEM INFORMATION - END ###';

                $content .= $this->section('Debug Info:', implode('<br />', $debugInfo));
                break;


            case 'FE:phpinfo':
            case 'BE:phpinfo':
                ob_start();
                phpinfo();
                $infoOut = explode('<body>', ob_get_contents());
                ob_end_clean();
                $infoOut = explode('</body>', $infoOut[1]);
                $out = $infoOut[0];
                $content .= $this->section('PHPInfo:', $out);
                break;

        }
        if ($content) {
            $content = '<a name="tx_ccinfo_' . $type . '"></a>' . $content;
        }
        return $content;
    }


    function section($header, $content)
    {
        global $SOBE;

        if (is_object($SOBE)) {
            $content = $SOBE->doc->section($header, $content, 0, 1);
        } else {
            $content = '<h2>' . htmlspecialchars($header) . '</h2>' . $content . '<br />';
        }
        return $content;
    }


    function divider()
    {
        global $SOBE;

        if (is_object($SOBE)) {
            $content = $SOBE->doc->divider();
        } else {
            $content = '<br /><hr /><br />';
        }
        return $content;
    }


    /**
     * DebugVar for PHP / TYPO3 Development.
     *
     * @author    Luite van Zelst <luite@aegee.org>
     * @link    http://www.xinix.dnsalias.net/fileadmin/t3dev/debugvar.php.txt
     *
     * @access    public
     * @version    1.0
     *
     * @param    mixed $var The variable you want to debug. It may be one of these: object, array, boolean, int, float, string
     * @param    string $name Name of the variable you are debugging. Usefull to distinguish different debugvar() calls.
     * @param    int $level The number of recursive levels to debug. With nested arrays/objects it's the safest thing
     * @internal                 Don't use the recursive param yourself - you'll end up with incomplete tables!
     * @return    string            Returns ready debug output in html-format. Uses nested tables, unfortunately.
     */
    function debugvar($var, $name = '', $level = 3, $recursive = false)
    {
        $style[0] = 'font-size:9.5px;font-family:verdana,sans-serif;border-collapse:collapse;background:#E7EEEE;';
        $style[1] = 'font-size:9.5px;font-family:verdana,sans-serif;border-width:1px;border-style:dotted; border-color:#A0AEB0;border-right-style:dotted;';
        $style[2] = 'font-size:9.5px;font-family:verdana,sans-serif;border-width:1px;border-style:dotted; border-color:#A0AEB0;border-right-style:dotted;border-left-style:dotted;';
        $style[3] = 'font-size:9.5px;font-family:verdana,sans-serif;border-width:1px;border-style:dotted; border-color:#A0AEB0;border-left-style:dotted;';
        if (null === $var) {
            $type = 'Mixed';
            $var = 'NULL';
            $style[3] .= 'color:red;font-style:italic;';
        } else if (@is_array($var)) {
            $type = 'Array';
            $len = '&nbsp;(' . sizeof($var) . ')';
            if ($level > -1) {
                $line = '';
                $multiple = true;
                foreach ($var as $key => $val) {
                    $line .= $this->debugvar($val, $key, $level - 1, true);
                }
                $var = sprintf("<table style=\"%s\">\n%s\n</table >\n",
                    $style[0],
                    $line
                );
            } else {
                $var = 'Skipped. Increase "depth" if you want to see this.';
                $style[3] .= 'color:red;font-style:italic;';
            }
            $style[1] .= 'color:#449;font-weight:bold;';
            $style[2] .= 'color:#449;font-weight:bold;';
            $style[3] .= 'padding:0px;';
        } else if (@is_object($var)) {
            $type = 'object: ' . @get_class($var);// . '&nbsp;(extends&nbsp;' . @get_parent_class($var) . ')&nbsp;';
            $style[1] .= 'color:purple;';
            $style[3] .= 'color:purple;';
            if ($level > -1) {
                $line = '';
                $multiple = true;
                $vars = (array)@get_object_vars($var);
                foreach ($vars as $key => $val) {
                    $line .= $this->debugvar($val, $key, $level - 1, true);
                }
                $methods = (array)@get_class_methods($var);
                foreach ($methods as $key => $val) {
                    $line .= sprintf("<tr ><td style=\"%s\">Method</td ><td colspan=\"2\" style=\"%s\">%s</td ></tr >",
                        $style[1],
                        $style[3],
                        $val . '&nbsp;(&nbsp;)'
                    );
                }
                $var = sprintf("<table style=\"%s\">\n%s\n</table >\n",
                    $style[0],
                    $line
                );
                $len = '&nbsp;(' . sizeof($vars) . '&nbsp;+&nbsp;' . sizeof($methods) . ')';
            } else {
                $var = 'Skipped. Increase "depth" if you want to see this.';
                $style[3] .= 'color:red;font-style:italic;';
            }
            $style[3] .= 'padding:0px;';
        } else if (@is_bool($var)) {
            $type = 'Boolean';
            $style[1] .= 'color:#906;';
            $style[2] .= 'color:#906;';
            if (!$var) $style[3] .= 'color:red;';
            if ($var == 0) $var = 'FALSE';
        } else if (@is_float($var)) {
            $type = 'Float';
            $style[1] .= 'color:#066;';
            $style[2] .= 'color:#066;';
        } else if (@is_int($var)) {
            $type = 'Integer';
            $style[1] .= 'color:green;';
            $style[2] .= 'color:green;';
        } else if (@is_string($var)) {
            $type = 'String';
            $style[1] .= 'color:#222;';
            $style[2] .= 'color:#222;';
            $var = nl2br(@htmlspecialchars($var));
            $len = '&nbsp;(' . strlen($var) . ')';
            if ($var == '') $var = '&nbsp;';
        } else {
            $type = 'Unknown!';
            $style[1] .= 'color:red;';
            $style[2] .= 'color:red;';
            $var = @htmlspecialchars($var);
        }
        if (!$recursive) {
            if ($name == '') {
                $name = '(no name given)';
                $style[2] .= 'font-style:italic;';
            }
            $style[2] .= 'color:red;';

            if ($multiple) {
                $html = "<table cellpadding=\"1\" style=\"%s\">\n<tr >\n<td width=\"0\" style=\"%s\">%s</td ></tr ><tr >\n<td style=\"%s\">%s</td>\n</tr >\n<tr >\n <td colspan=\"2\" style=\"%s\">%s</td>\n</tr >\n</table >\n";
            } else {
                $html = "<table cellpadding=\"1\" style=\"%s\">\n<tr >\n<td style=\"%s\">%s</td>\n<td style=\"%s\">%s</td ><td style=\"%s\">%s</td >\n</tr >\n</table>\n";
            }
            return sprintf($html, $style[0],
                $style[1], $type . $len,
                $style[2], $name,
                $style[3], $var
            );
        } else {
//			return 	sprintf("<tr >\n<td style=\"%s\">\n%s\n</td >\n<td style=\"%s\">%s</td >\n<td style=\"%s\">\n%s\n</td ></tr >",
//						$style[1],
//						$type . $len,
//						$style[2],
//						$name,
//						$style[3],
//						$var
//					);
            #return 	sprintf("<tr >\n<td style=\"%s\">\n<b>%s</b>\n<br /><span style=\"color:grey;\">%s</span></td >\n<td style=\"%s\">\n%s\n</td ></tr >",
            return sprintf("<tr >\n<td style=\"%s\">\n<b>%s</b>&nbsp;<span style=\"color:grey;\">%s</span></td >\n<td style=\"%s\">\n%s\n</td ></tr >",
                $style[1],
                $name,
                $type . $len,
                $style[3],
                $var
            );
        }
    }


    /***************************
     *
     * Plugin functions
     *
     **************************/


    var $prefixCSS = 'tx-ccfeinfo';
    var $prefixId = 'txccfeinfo';
    var $selected = array();

    protected $selectedCount = 0;


    /**
     * Renders thw whole info output including form
     *
     * @return string HTML
     */
    function pi_getInfoOutput()
    {
        $content = '';

        $this->pi_mergeOptionsFromSession();

        $content = $this->pi_getOptionsForm($this->types);
        $content .= '<br />';
        $content .= $this->pi_getInfo();
        $content = $this->pi_wrapInBaseClass($content);

        $GLOBALS['TSFE']->setCSS($this->prefixCSS, '
			.' . $this->prefixCSS . '  { color:black; background-color:#eee; border:2px #9c1f00 solid; padding:5px; font-size:10.5px; font-weight:normal; text-decoration:none; }
			');

        return $content;
    }


    /**
     * Renders thw whole info output including form wrapped in a toggle-able div
     *
     * @return string HTML
     */
    function pi_getToggledInfoOutput()
    {
        $content = $this->pi_getInfoOutput();
        $prefix = str_replace('_', '-', $this->prefixId);
        $content = $this->pi_buttonToggleDisplay($prefix, '<span style="color:#666">FE Debug/Info output:</span> ' . $this->prefixId, $content, $this->selectedCount);

        return $content;
    }


    /**
     * read piVars from user session if not already set (by form)
     */
    function pi_mergeOptionsFromSession()
    {
        $submitted = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST($this->prefixId);
        if (is_array($submitted)) {
            $this->selected = $submitted;
        } else {
            $this->selected = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixId);
        }
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixId, $this->selected);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }


    /**
     * Returns HTML code of an form with some checkbox options
     *
     * @param    array $options Options array
     * @return string HTML
     */
    function pi_getOptionsForm($options)
    {

        $trows = '';

        foreach ($options as $option => $optTitle) {
            $trow = '';

            $this->selectedCount += $this->selected[$option] ? 1 : 0;

            $checked = $this->selected[$option] ? ' checked="checked"' : '';
            $trow .= '
			<td nowrap="nowrap" valign="top" class="' . $this->prefixCSS . '-option">
				<input type="hidden" name="' . $this->prefixId . '[' . $option . ']" value="0" />
				<input type="checkbox" ' . $checked . ' name="' . $this->prefixId . '[' . $option . ']" id="optid' . $option . '" value="1" /> <label for="optid' . $option . '">' . $optTitle . '</label>
			</td>
			';

            $optionDepth = $option . '_depth';
            $this->selected[$optionDepth] = isset($this->selected[$optionDepth]) ? $this->selected[$option . '_depth'] : 2;
            $depth = max(0, min(9, intval($this->selected[$option . '_depth'])));
            $level = '<input type="text" size="2" maxlength="1" name="' . $this->prefixId . '[' . $option . '_depth]" value="' . $depth . '" style="font-size:0.9em;background-color:#eee;" title="Define rendering depth of arrays and objects (0-9)." />';
            $trow .= '
			<td nowrap="nowrap" valign="top" style="padding-left:1em;padding-right:3em;">' . $level . '</td>';

            $trows .= '
			<tr>' . $trow . '</tr>
			';
        }


        $content = '

			<!--
				' . $this->prefixId . ' options form.
			-->
			<form action="' . htmlspecialchars($this->pObj->pi_getPageLink($GLOBALS['TSFE']->id, '_top')) . '" target="_top" method="post" style="margin: 0 0 0 0;">
			<table cellpadding="0" cellspacing="0" border="0">
			' . $trows . '
			</table>
			<input type="hidden" name="no_cache" value="1" />
			<input type="submit" name="' . $this->prefixId . '-submit" value="Set" style="margin:0.5em; width:8em;" /><br />
			</form>
		';

        return $content;
    }


    /**
     * Returns HTML code of info output
     *
     * @return string HTML
     */
    function pi_getInfo()
    {
        $content = '';

        $count = 0;
        foreach ($this->types as $option => $optTitle) {
            if ($this->selected[$option]) {
                if ($count++) {
                    $content .= $this->divider();
                }
                $depth = max(0, min(9, intval($this->selected[$option . '_depth'])));
                $content .= $this->information($option, $depth);
            }
        }

        return $content;
    }


    /**
     *
     */
    function pi_wrapInBaseClass($str)
    {
        return '



	<div class="' . $this->prefixCSS . '">
	<div>
		' . $str . '
	</div>
	</div>

	';
    }


    /**
     * Renders a box which can be toggled to be expanded or shrinked to display or hide the content inside.
     *
     * @param    string $id Unique id for the box. Needs to be CSS valid.
     * @param    string $title Title/label
     * @param    string $guiElement The content inside the box
     * @param    boolean $displayOpen When set the box is initially open
     * @return    string HTML content
     */
    function pi_buttonToggleDisplay($id, $title, $guiElement, $displayOpen = false)
    {
        $GLOBALS['TSFE']->additionalCSS[$this->prefixId] = '
			.' . $id . '  { display: table-cell; margin-top:25px; margin-bottom:25px; }
			.' . $this->prefixCSS . '  { display: table-cell; }
			.' . $this->prefixCSS . ' hr  { margin:0; border-top: 1px solid #000; }
			td.' . $this->prefixCSS . '-option { display: table-cell; vertical-align:middle; height:1.3em; display: flex; align-items: center}
			td.' . $this->prefixCSS . '-option label { margin:0 0 0 0.5em; }
			td.' . $this->prefixCSS . '-option input { margin:0; }
            .buttonToggleDisplay { display: table; }
            .buttonToggleDisplayRow { table-row; }
            .buttonToggleDisplayCell, .buttonToggleDisplayCellH { padding: 0px 2px 0px 0px; display: table-cell; }
            .buttonToggleDisplayCellH:hover { background-color: #a4adb3; }
            .buttonToggleDisplayCellH a { text-decoration: none; display: block; }';

        $GLOBALS['TSFE']->additionalJavaScript['tx_ccfeinfo_toggleDisplay'] = '
			function toggleDisplay(toggleId, e) {
				if (!e) {
					e = window.event;
				}
				if (!document.getElementById) {
					return false;
				}
				var body = document.getElementById(toggleId);
				if (!body) {
					return false;
				}
				if (body.style.display == "none") {
					body.style.display = "block";
				} else {
					body.style.display = "none";
				}
				if (e) {
					// Stop the event from propagating, which
					// would cause the regular HREF link to
					// be followed, ruining our hard work.
					e.cancelBubble = true;
					if (e.stopPropagation) {
						e.stopPropagation();
					}
				}
			}
			';

        $content = '';
        $content .= '<div class="buttonToggleDisplay" style="display:block; margin: 1em 0 1em 0;">
						<div class="buttonToggleDisplayRow"><div class="buttonToggleDisplayCellH" style="background-color: #b8c2c9; border: 1px solid #888;"><a href="#" onclick="toggleDisplay(\'' . $id . '\', event);return false;" style="white-space:nowrap;">
						' . $title . '</a></div></div>';

        $content .= '<div class="buttonToggleDisplayRow"><div class="buttonToggleDisplayCell" id="' . $id . '" style="display:' . ($displayOpen ? 'block' : 'none') . ';"><div class="guiElementBox">' . $guiElement . '</div></div></div>';
        $content .= '</div>';
        return $content;
    }
}

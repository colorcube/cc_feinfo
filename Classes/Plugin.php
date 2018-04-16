<?php
namespace Colorcube\CcFeinfo;


/***************************************************************
*  Copyright notice
*
*  (c) 2004-2018 Rene Fritz (r.fritz@colorcube.de)
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

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Plugin 'Debug/Info output' for the 'cc_feinfo' extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */



class Plugin {

    /**
     * The backReference to the mother cObj object set at call time
     *
     * @var ContentObjectRenderer
     */
    public $cObj;

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;

		$info = new FeInfo();
		$info->init($this);
		return $info->pi_getToggledInfoOutput();
	}

    /**
     * Get URL to some page.
     * Returns the URL to page $id with $target and an array of additional url-parameters, $urlParameters
     * Simple example: $this->pi_getPageLink(123) to get the URL for page-id 123.
     *
     * The function basically calls $this->cObj->getTypoLink_URL()
     *
     * @param int $id Page id
     * @param string $target Target value to use. Affects the &type-value of the URL, defaults to current.
     * @param array|string $urlParameters As an array key/value pairs represent URL parameters to set. Values NOT URL-encoded yet, keys should be URL-encoded if needed. As a string the parameter is expected to be URL-encoded already.
     * @return string The resulting URL
     * @see pi_linkToPage()
     * @see ContentObjectRenderer->getTypoLink()
     */
    public function pi_getPageLink($id, $target = '', $urlParameters = [])
    {
        return $this->cObj->getTypoLink_URL($id, $urlParameters, $target);
    }
}

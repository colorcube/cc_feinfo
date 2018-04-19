.. ==================================================
.. FOR YOUR INFORMATION
.. ================================================--
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


==============================
Usage
==============================


You can use the plugin in 3 ways:


1. Include Plugin
=================

Just insert the plugin on a page with an “insert plugin” record.


2. Include TypoScript
=====================

Include the plugin into your TypoScript template to make it visible on the whole site. Example:

.. code-block:: typoscript

    page.50 < plugin.tx_ccfeinfo_pi1


In a fluid template this would look like this:

.. code-block:: html

    <f:cObject typoscriptObjectPath="plugin.tx_ccfeinfo_pi1"/>



3. use the PHP class in your own plugin
=======================================

If you want to include cc_feinfo into your own plugin for testing purposes you can do that like this:

.. code-block:: php

    $info = new \Colorcube\CcFeinfo\FeInfo();
    $info->init($this);
    $content .= $info->pi_getInfoOutput();


If you want to get a toggle-able output frame use this as the last line:

.. code-block:: php

    $content .= $info->pi_getToggledInfoOutput();


Remarks
=======

If you think this all looks ugly , keep in mind that this is code from 2004.
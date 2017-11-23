<?php
/* For licensing terms, see /vendor/license.txt */

/**
 * This script is included by main/admin/settings.lib.php and generally
 * includes things to execute in the main database (settings_current table)
 *
 * @package chamilo.plugin.clockworksms
 * @author  Imanol Losada <imanol.losada@beeznest.com>
 */
/**
 * Initialization
 */
require_once dirname(__FILE__).'/config.php';
ClockworksmsPlugin::create()->install();

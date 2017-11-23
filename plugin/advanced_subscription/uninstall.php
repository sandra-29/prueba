<?php
/* For license terms, see /license.txt */
/**
 * This script is included by main/admin/settings.lib.php when unselecting a plugin
 * and is meant to remove things installed by the install.php script in both
 * the global database and the courses tables
 * @package chamilo.plugin.advanced_subscription
*/

/**
 * Queries
 */
require_once dirname(__FILE__) . '/config.php';
if (!api_is_platform_admin()) {
    die ('You must have admin permissions to uninstall plugins');
}
AdvancedSubscriptionPlugin::create()->uninstall();

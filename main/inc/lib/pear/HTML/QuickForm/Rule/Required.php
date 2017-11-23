<?php

/**
 * Required elements validation
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @copyright   2001-2009 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: Required.php,v 1.6 2009/04/04 21:34:04 avb Exp $
 * @link        http://pear.php.net/package/HTML_QuickForm
 */

/**
 * Required elements validation
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @version     Release: 3.2.11
 * @since       3.2
 */
class HTML_QuickForm_Rule_Required extends HTML_QuickForm_Rule
{
    /**
     * Checks if an element is empty
     *
     * @param     string    $value      Value to check
     * @param     mixed     $options    Not used yet
     * @access    public
     * @return    boolean   true if value is not empty
     */
    public function validate($value, $options = null)
    {
        // It seems this is a file.
        if (is_array($value)) {
            if (isset($value['name']) &&
                isset($value['type']) &&
                isset($value['tmp_name']) &&
                isset($value['size']) &&
                isset($value['error'])
            ){
                if (empty($value['tmp_name'])) {

                    return false;
                }
            }
        } else {
            if ((string)$value == '') {

                return false;
            }
        }

        return true;
    }


    public function getValidationScript($options = null)
    {
        return array('', "{jsVar} == ''");
    }
}

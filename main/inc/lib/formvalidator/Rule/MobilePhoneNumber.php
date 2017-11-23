<?php

/**
 * Abstract base class for QuickForm validation rules
 */

/**
 * Validate telephones
 *
 */
class HTML_QuickForm_Rule_Mobile_Phone_Number extends HTML_QuickForm_Rule
{

    /**
     * Validates mobile phone number
     *
     * @param string Mobile phone number to be validated
     * @param string Not using it. Just to respect the declaration
     * @return boolean Returns true if valid, false otherwise.
     */
    function validate($mobilePhoneNumber, $options = null)
    {
        $rule = "/^\d{11}$/";
        return preg_match($rule, $mobilePhoneNumber);
    }
}

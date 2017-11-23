<?php
/* For licensing terms, see /license.txt */

/**
 * Form element to select a date.
 *
 * Class DatePicker
 */
class DatePicker extends HTML_QuickForm_text
{
    /**
     * @param string $elementName
     * @param string $elementLabel
     * @param array  $attributes
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null)
    {
        if (!isset($attributes['id'])) {
            $attributes['id'] = $elementName;
        }
        $attributes['class'] = 'form-control';

        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_appendName = true;
        $this->_type = 'date_picker';
    }

    /**
     * HTML code to display this datepicker
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id = $this->getAttribute('id');
        $value = $this->getValue();
        $label = $this->getLabel();

        if (!empty($value)) {
            $value = api_format_date($value, DATE_TIME_FORMAT_LONG_24H);
        }

        return '
            <div class="input-group">
                <span class="input-group-addon">
                    <input ' . $this->_getAttrString($this->_attributes) . '>
                </span>
                <input class="form-control" type="text" readonly id="' . $id . '_alt" value="' . $value . '">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">
                        <span class="fa fa-times text-danger" aria-hidden="true"></span>
                        <span class="sr-only">' . get_lang('Reset') . '</span>
                    </button>
                </span>
            </div>
        ' . $this->getElementJS();
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $value = substr($value, 0, 16);
        $this->updateAttributes(
            array(
                'value' => $value
            )
        );
    }

    /**
     * Get the necessary javascript for this datepicker
     * @return string
     */
    private function getElementJS()
    {
        $js = null;
        $id = $this->getAttribute('id');

        $js .= "<script>                    
            $(function() {
                var txtDate = $('#$id'),
                    inputGroup = txtDate.parents('.input-group');
                    
                txtDate
                    .hide()
                    .datepicker({
                        defaultDate: '" . $this->getValue() . "',
                        dateFormat: 'yy-mm-dd',
                        altField: '#{$id}_alt',
                        altFormat: \"" . get_lang('DateFormatLongNoDayJS') . "\",
                        showOn: 'both',
                        buttonImage: '" . Display::return_icon('attendance.png', null, [], ICON_SIZE_TINY, true, true) . "',
                        buttonImageOnly: true,
                        buttonText: '" . get_lang('SelectDate') . "',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: 'c-60y:c+5y'
                    });

                inputGroup
                    .find('button')
                    .on('click', function (e) {
                        e.preventDefault();

                        $('#$id, #{$id}_alt').val('');
                    });
            });
        </script>";

        return $js;
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function getTemplate($layout)
    {
        $size = $this->getColumnsSize();
        $id = $this->getAttribute('id');
        $value = $this->getValue();

        if (empty($size)) {
            $sizeTemp = $this->getInputSize();
            if (empty($size)) {
                $sizeTemp = 8;
            }
            $size = array(2, $sizeTemp, 2);
        } else {
            if (is_array($size)) {
                if (count($size) != 3) {
                    $sizeTemp = $this->getInputSize();
                    if (empty($size)) {
                        $sizeTemp = 8;
                    }
                    $size = array(2, $sizeTemp, 2);
                }
                // else just keep the $size array as received
            } else {
                $size = array(2, intval($size), 2);
            }
        }

        if (!empty($value)) {
            $value = api_format_date($value, DATE_FORMAT_LONG_NO_DAY);
        }

        switch ($layout) {
            case FormValidator::LAYOUT_INLINE:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>

                    {element}
                </div>';
                break;
            case FormValidator::LAYOUT_HORIZONTAL:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} class="col-sm-'.$size[0].' control-label {extra_label_class}" >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>
                    <div class="col-sm-'.$size[1].'">
                        {icon}

                        {element}

                        <!-- BEGIN label_2 -->
                            <p class="help-block">{label_2}</p>
                        <!-- END label_2 -->

                        <!-- BEGIN error -->
                            <span class="help-inline">{error}</span>
                        <!-- END error -->
                    </div>
                    <div class="col-sm-'.$size[2].'">
                        <!-- BEGIN label_3 -->
                            {label_3}
                        <!-- END label_3 -->
                    </div>
                </div>';
                break;
            case FormValidator::LAYOUT_BOX_NO_LABEL:
                return '{element}';
                break;
        }
    }
}

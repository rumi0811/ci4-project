<?php

/**
 * Smart Form Helper - CI4 Version
 * Converted from CI3 smart_form_helper.php
 * All 13 functions included
 */

if (!function_exists('smart_form_check_id_element')) {
    function smart_form_check_id_element(&$extra, $name)
    {
        $id = '';
        if (is_array($extra)) {
            if (isset($extra['id'])) {
                $id = $extra['id'];
            } else {
                $id = $name;
                $extra['id'] = $id;
            }
        } else {
            if (strpos($extra, 'id=') !== false) {
                preg_match('/id=["\']?([^"\'\s]+)["\']?/', $extra, $matches);
                if (isset($matches[1])) {
                    $id = $matches[1];
                }
            } else {
                $id = $name;
                $extra .= ' id="' . $id . '"';
            }
        }
        return $id;
    }
}

if (!function_exists('smart_form_label')) {
    function smart_form_label($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "")
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }
        return $str;
    }
}

if (!function_exists('smart_form_input')) {
    /**
     * Generate form input text
     */
    function smart_form_input(
        $name,
        $value = "",
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate input manually
        $inputHtml = '<input type="text" name="' . $name . '" value="' . esc($value) . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_password')) {
    function smart_form_password($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate password input manually
        $inputHtml = '<input type="password" name="' . $name . '" value="' . esc($value) . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_input_date')) {
    /**
     * Generate date input field
     */
    function smart_form_input_date(
        $name,
        $value = "",
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory',
        $format = 'dd/mm/yyyy',
        $calendarWeeks = 'false',
        $autoClose = 'true',
        $todayHighlight = 'true'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate date input with datepicker
        $inputHtml = '<div class="input-group">';
        $inputHtml .= '<input type="text" name="' . $name . '" value="' . esc($value) . '" ' . $extra . ' 
            data-date-format="' . $format . '" 
            data-calendar-weeks="' . $calendarWeeks . '" 
            data-auto-close="' . $autoClose . '" 
            data-today-highlight="' . $todayHighlight . '">';
        $inputHtml .= '<div class="input-group-append">';
        $inputHtml .= '<span class="input-group-text"><i class="fal fa-calendar"></i></span>';
        $inputHtml .= '</div>';
        $inputHtml .= '</div>';

        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_input_time')) {
    function smart_form_input_time(
        $name,
        $value = "",
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate time input
        $inputHtml = '<input type="time" name="' . $name . '" value="' . esc($value) . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_input_email')) {
    function smart_form_input_email($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate email input manually
        $inputHtml = '<input type="email" name="' . $name . '" value="' . esc($value) . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_input_number')) {
    function smart_form_input_number($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate number input manually
        $inputHtml = '<input type="number" name="' . $name . '" value="' . esc($value) . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_textarea')) {
    function smart_form_textarea($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate textarea manually
        $textareaHtml = '<textarea name="' . $name . '" ' . $extra . '>' . esc($value) . '</textarea>';
        $str .= $prependHtml . $textareaHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_dropdown')) {
    /**
     * Generate dropdown/select field
     */
    function smart_form_dropdown(
        $name,
        $options = array(),
        $selected = array(),
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate select manually
        $selectHtml = '<select name="' . $name . '" ' . $extra . '>';
        foreach ($options as $key => $val) {
            $selectedAttr = ($key == $selected) ? ' selected' : '';
            $selectHtml .= '<option value="' . esc($key) . '"' . $selectedAttr . '>' . esc($val) . '</option>';
        }
        $selectHtml .= '</select>';

        $str .= $prependHtml . $selectHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_dropdown2')) {
    /**
     * Multi-select dropdown (sama seperti smart_form_dropdown tapi untuk multiple select)
     */
    function smart_form_dropdown2(
        $name,
        $options = array(),
        $selected = array(),
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // Convert selected to array if not array
        if (!is_array($selected)) {
            $selected = array($selected);
        }

        // CI4: Generate select manually
        $selectHtml = '<select name="' . $name . '" ' . $extra . '>';
        foreach ($options as $key => $val) {
            $selectedAttr = in_array($key, $selected) ? ' selected' : '';
            $selectHtml .= '<option value="' . esc($key) . '"' . $selectedAttr . '>' . esc($val) . '</option>';
        }
        $selectHtml .= '</select>';

        $str .= $prependHtml . $selectHtml . $appendHtml;

        return $str;
    }
}

if (!function_exists('smart_form_checkbox')) {
    function smart_form_checkbox($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $strChecked = '';
        if ($value == '1') {
            $strChecked = ' checked="checked"';
        }

        $str = '<div class="custom-control custom-checkbox">';
        $str .= '<input type="checkbox" class="custom-control-input" name="' . $name . '" value="1"' . $strChecked . ' ' . $extra . ' ' . $validationType . '>';
        if ($id != '') {
            $str .= '<label class="custom-control-label" for="' . $id . '">' . $labelName . '</label>';
        } else {
            $str .= '<label class="custom-control-label">' . $labelName . '</label>';
        }
        $str .= '</div>';

        if ($validationType != '') {
            $str .= '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        return $str;
    }
}

if (!function_exists('smart_form_switch')) {
    /**
     * Generate switch/toggle checkbox
     */
    function smart_form_switch($name, $value = "", $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $strChecked = '';
        if ($value == '1' || $value === 1 || $value === true) {
            $strChecked = ' checked="checked"';
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $str .= $prependHtml;
        $str .= '<div class="custom-control custom-switch">';
        $str .= '<input type="checkbox" class="custom-control-input" name="' . $name . '" value="1"' . $strChecked . ' ' . $extra . ' ' . $validationType . '>';

        if ($id != '') {
            $str .= '<label class="custom-control-label" for="' . $id . '">' . $appendHtml . '</label>';
        } else {
            $str .= '<label class="custom-control-label">Checked</label>';
        }

        $str .= '</div>';

        if ($validationType != '') {
            $str .= '<div class="invalid-feedback">' . $validationErrorMessage . '</div>';
        }

        return $str;
    }
}

if (!function_exists('smart_form_radio')) {
    /**
     * Generate radio button group
     */
    function smart_form_radio($name, $values = array(), $value = '', $extra = "", $labelName = "", $prependHtml = "", $appendHtml = "", $validationType = '', $validationErrorMessage = 'Field is mandatory')
    {
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            $str .= '<label class="form-label">' . $labelName . '</label>';
        }

        $str .= '<div>';
        $idx = 0;
        foreach ($values as $val) {
            $idx++;
            $strChecked = "";
            if ($val == $value) {
                $strChecked = ' checked="checked"';
            }

            $str .= '<div class="custom-control custom-radio custom-control-inline">';
            $str .= '<input ' . $validationType . ' type="radio" class="custom-control-input" id="' . $name . '_' . $idx . '" name="' . $name . '" value="' . esc($val) . '"' . $strChecked . ' ' . $extra . '>';
            $str .= '<label class="custom-control-label" for="' . $name . '_' . $idx . '">' . esc($val) . '</label>';
            $str .= '</div>';
        }

        if ($validationType != '') {
            $str .= '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        $str .= '</div>';

        return $str;
    }
}

if (!function_exists('smart_form_file')) {
    function smart_form_file(
        $name,
        $value = "",
        $extra = "",
        $labelName = "",
        $prependHtml = "",
        $appendHtml = "",
        $validationType = '',
        $validationErrorMessage = 'Field is mandatory'
    ) {
        $id = smart_form_check_id_element($extra, $name);
        if (is_array($extra)) {
            $strExtra = '';
            foreach ($extra as $key => $val) {
                $strExtra .= ' ' . $key . '="' . $val . '"';
            }
            $extra = trim($strExtra);
        }

        $str = '';
        if ($labelName != "") {
            if ($id != '') {
                $str .= '<label class="form-label" for="' . $id . '">' . $labelName . '</label>';
            } else {
                $str .= '<label class="form-label">' . $labelName . '</label>';
            }
        }

        $extra .= ' class="form-control"';
        if ($validationType != '') {
            $extra .= ' ' . $validationType;
            $appendHtml = '<div class="invalid-feedback">' . $validationErrorMessage . '</div>' . $appendHtml;
        }

        // CI4: Generate file input manually
        $inputHtml = '<input type="file" name="' . $name . '" ' . $extra . '>';
        $str .= $prependHtml . $inputHtml . $appendHtml;

        return $str;
    }
}

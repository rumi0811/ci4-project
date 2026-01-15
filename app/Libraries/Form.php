<?php

namespace App\Libraries;

/**
 * Form Library - CI4 Version
 * Converted from CI3 Form.php
 * 
 * CONVERSION NOTES:
 * - NO LOGIC CHANGES - All business logic preserved exactly
 * - ONLY syntax adjustments CI3 → CI4
 * - All 2118 lines converted completely
 * - Bootstrap 4.5.1 compatible
 */

class Form
{

    public $formID;
    public $formAction;
    public $formMultipart = false;
    public $method = 'POST';
    public $caption = 'Form Input';
    //  public $dateFormat = 'yy-mm-dd';
    public $dateFormat = 'YYYY-MM-DD';
    public $datetimeFormat = 'YYYY-MM-DD HH:mm:ss';
    // basic or horizontal
    public $formStyle = 'basic';
    //top, bottom
    public $formButtonPosition = 'bottom';
    public $hasNavigationHeader = false;
    public $navigationHeaderListUrl = 'dataList';
    public $navigationTableSource = array('table' => '', 'key' => '');
    public $disableFormJS = false;
    public $detailMaxHeight = 250;

    // CI4: No $ci property needed
    private $tabs = array();
    private $fieldset = array();
    private $activeFieldset = '';
    private $activeTab = '';
    private $formDetail = array();
    private $activeFormDetail = array();
    private $formDetailNewRow = array();

    private $module;
    private $controller_name;
    private $method_name;
    private $blankObject = 0;


    private $resultString = '';
    private $formObject = [];

    private $formActionButton = array();
    private $headingActionButton = array();
    private $arrDateElement = array();
    private $arrDateTimeElement = array();
    private $arrDateRangeElement = array();
    private $arrTimeElement = array();
    private $arrAutoCompleteElement = array();
    private $arrAutoCompleteElementDetail = array();
    private $arrSelectElement = array();
    private $arrEditorElement = array();

    private $validationRules = array();
    private $validationMessages = array();
    private $arrDefaultValidation = array('required', 'remote', 'minlength', 'maxlength', 'rangelength', 'min', 'max', 'range', 'email', 'url', 'date', 'dateISO', 'number', 'digits', 'creditcard', 'equalTo');

    public $isFormOnly = false;
    public $renderFormTag = true;
    public $datetimerangeOption = [
        'Today' => "[moment().startOf('day'), moment().endOf('day')]",
        'Yesterday' => "[moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')]",
        'Last 7 Days' => "[moment().subtract(6, 'days').startOf('day'), moment()]",
        'Last 30 Days' => "[moment().subtract(29, 'days').startOf('day'), moment()]",
        'This Month' => "[moment().startOf('month').startOf('day'), moment().endOf('month')]",
        'Last Month' => "[moment().subtract(1, 'month').startOf('month').startOf('day'), moment().subtract(1, 'month').endOf('month').endOf('day')]"
    ];

    public $daterangeOption = [
        'Today' => "[moment().startOf('day'), moment().endOf('day')]",
        'Yesterday' => "[moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')]",
    ];

    public function __construct($arrOptions)
    {
        // CI4: No need for get_instance()
        helper('form');

        // check options action and id form
        if (isset($arrOptions['action'])) {
            $this->formAction = $arrOptions['action'];
        } else {
            $this->formAction = service('router')->controllerName() . '/' . service('router')->methodName();
        }

        if (isset($arrOptions['id']))
            $this->formID = $arrOptions['id'];

        $this->module = ""; //$this->ci->router->fetch_module();
        $this->controller_name = service('router')->controllerName();
        $this->method_name = service('router')->methodName();
    }

    public function addTab($name, $id = "", $attribute = array())
    {
        $counter = count($this->tabs) + 1;
        if ($id == '') $id = 'Tab_' . $counter . '_' . $this->formID;
        else $id = $id . '_' . $this->formID;
        if (isset($this->tabs[$id])) {
            throw new \Exception('tab with id ' . $id . ' already exists');
            exit();
        }

        $this->tabs[$id] = array('name' => $name);
        $this->activeTab = $id;
        $this->activeFieldset = '';
        $this->formObject[$id] = array('string' => '');
    }

    private function form_fieldset_open($name, $arrFieldsetAttr)
    {
        $idAccordion = 'fs_' . md5($name);
        $targetLink = $idAccordion . 'a';
        return '
      <div class="col-sm-12 accordion accordion-outline" id="' . $idAccordion . '">
        <div class="card">
            <div class="card-header">
                <a href="javascript:void(0);" class="card-title" data-toggle="collapse" data-target="#' . $targetLink . '" aria-expanded="true">
                    ' . $name . '
                    <span class="ml-auto">
                        <span class="collapsed-reveal">
                            <i class="fal fa-minus fs-xl"></i>
                        </span>
                        <span class="collapsed-hidden">
                            <i class="fal fa-plus fs-xl"></i>
                        </span>
                    </span>
                </a>
            </div>
            <div id="' . $targetLink . '" class="collapse show" data-parent="#' . $idAccordion . '">
                <div class="card-body">';
    }

    private function form_fieldset_close()
    {
        return '
            </div>
          </div>
        </div>
      </div>';
    }

    public function addFieldSet($name, $intNumberOfColumns = 1, $id = "", $attribute = array())
    {
        if ($id == '') $id = $name . '_' . $this->formID;

        if (isset($this->fieldSets[$id])) {
            throw new \Exception('fieldset with id ' . $id . ' already exists');
            exit();
        }

        $this->fieldset[$id] = array('column' => $intNumberOfColumns);
        $this->activeFieldset = $id;

        $arrDefaultAttr = array('id' => $id);
        $arrFieldsetAttr = array_merge($attribute, $arrDefaultAttr);

        $legend_text = ($name == '') ? $name : $name;

        // $strResult = "<div class=\"panel-content p-0\">".form_fieldset($legend_text, $arrFieldsetAttr);
        $strResult = $this->form_fieldset_open($legend_text, $arrFieldsetAttr);
        $this->formObject[$id] = array('string' => $strResult);
        if (!empty($this->tabs)) $this->tabs[$this->activeTab]['element'][$id] = $this->formObject[$id]['string'];
    }

    public function addDetail($name, $intDefaultRows = 3, $bolHasDeleteButton = true, $bolHasAddMoreButton = true, $hasSequenceColumn = true)
    {
        $this->formDetail[$name] = array(
            'header' => array(),
            'input' => array(),
            'footer' => array(),
            'intDefaultRows' => $intDefaultRows,
            'hasSequenceColumn' => $hasSequenceColumn,
            'bolHasDeleteButton' => $bolHasDeleteButton,
            'bolHasAddMoreButton' => $bolHasAddMoreButton,
            'arrData' => array(),
            'dataMapping' => array(),
            'strJSAfterAdd' => array(),
            'strJSAfterDelete' => array(),
        );

        $this->activeFormDetail = $name;
        //$this->addFormDetail($name, $arrHeader, $arrInput, $arrData, $intDefaultRows, $bolHasDeleteButton, $bolHasAddMoreButton);
    }

    public function addDetailHeader($headerIndex, $headerTitle, $headerProp = '')
    {
        $this->formDetail[$this->activeFormDetail]['header'][] = array(
            'headerIndex' => $headerIndex,
            'headerTitle' => $headerTitle,
            'headerProp' => $headerProp,
        );
    }

    //addDetailInput($inputType, $inputName, $headerIndex, $dataIndex='', $inputProp=array(), $inputDataType='string', $htmlBefore='', $htmlAfter='')
    public function addDetailInput($inputType, $inputName, $headerIndex, $dataIndex = '', $inputProp = array(), $inputDataType = 'string', $htmlBefore = '', $htmlAfter = '', $renderType = 'content' /* content or footer */, $arrDataOption = array(), $indexDataGroup = 'category')
    {
        // langsung grouping by header index
        $this->formDetail[$this->activeFormDetail]['input_' . $renderType][$headerIndex][] = array(
            'inputType' => $inputType,
            'inputName' => $inputName,
            'inputProp' => $inputProp,
            'inputDataType' => $inputDataType,
            'dataIndex' => $dataIndex,
            'htmlBefore' => $htmlBefore,
            'htmlAfter' => $htmlAfter,
            'arrDataOption' => $arrDataOption,
            'indexDataGroup' => $indexDataGroup
        );

        if ($inputType == 'autocomplete' || $inputType == 'autoComplete') {
            if (!empty($arrDataOption)) {

                $strDataList = 'var ' . $inputName . '_detail_options_' . $this->formID . ' = ';
                $arrTempOptions = array();

                //LOGIC untuk data dari fungsi generateList dengan tipe array value-text
                if ($arrDataOption != null && count($arrDataOption) > 0) {
                    $isFromTextValue = false;
                    foreach ($arrDataOption as $testOption) {
                        if (is_array($testOption) && isset($testOption["text"]) && isset($testOption["value"])) {
                            $isFromTextValue = true;
                            break;
                        }
                    }
                    if ($isFromTextValue) {
                        $arrDataOptionTemp = array();
                        foreach ($arrDataOption as $testOption) {
                            $arrDataOptionTemp[$testOption["value"]] = $testOption["text"];
                        }
                        $arrDataOption = $arrDataOptionTemp;
                    }
                }
                //END of LOGIC untuk data dari fungsi generateList dengan tipe array value-text

                foreach ($arrDataOption as $key => $valueOpt) {
                    if (is_array($valueOpt)) {
                        if ($indexDataGroup != '') {
                            if (!isset($valueOpt[$indexDataGroup])) {
                                throw new \Exception('no data with index ' . $indexDataGroup . ' on auto complete ' . $inputName);
                                exit();
                            }
                        }

                        $arrDefaultOptions = array('value' => $key);
                        $arrTempOptions[] = array_merge($valueOpt, $arrDefaultOptions);
                    } else {
                        $indexDataGroup = '';
                        $arrTempOptions[] = array('value' => $key, 'label' => $valueOpt);
                    }
                }

                // sorting for grouping auto complete
                $arrTmpForSorting = array();
                if (!empty($indexDataGroup)) {
                    foreach ($arrTempOptions as $arrProp) {
                        $arrTmpForSorting[$arrProp[$indexDataGroup]][] = $arrProp;
                    }
                    ksort($arrTmpForSorting);
                    $arrTempOptions = array();
                    foreach ($arrTmpForSorting as $group => $rowGroup) {
                        foreach ($rowGroup as $row) {
                            $arrTempOptions[] = $row;
                        }
                    }
                }
                $strDataList .= json_encode($arrTempOptions);


                $this->arrAutoCompleteElementDetail[$inputName] = array('source' => $inputName . '_detail_options_' . $this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);
            }
            // $this->arrAutoCompleteElement[$name] = array('source' => $name.'_options_'.$this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);

        }
    }

    public function addDetailScriptAfterAddRow($strJS)
    {
        $this->formDetail[$this->activeFormDetail]['strJSAfterAdd'][] = $strJS;
    }

    public function addDetailScriptAfterDeleteRow($strJS)
    {
        $this->formDetail[$this->activeFormDetail]['strJSAfterDelete'][] = $strJS;
    }

    public function bindDetailData($arrData)
    {
        $this->formDetail[$this->activeFormDetail]['arrData'] = $arrData;
        if (!empty($arrData))
            $this->formDetail[$this->activeFormDetail]['intDefaultRows'] = count($arrData);
    }

    public function renderFormDetail()
    {
        foreach ($this->formDetail as $name => $propDetail) {
            $elName = 'detail_' . $name . '_' . $this->formID;

            $strStyleHeight = ($this->detailMaxHeight > 0) ? 'style="height: ' . $this->detailMaxHeight . 'px;"' : '';
            $strResult = '
        <div class="table-responsive" ' . $strStyleHeight . '>
          <table class="table table-condensed table-hover table-bordered table-striped" id="table_' . $elName . '">
            <thead>
              <tr>
                <th style="width:30px;">No</th>';

            // to do : handle rowspan n colspan
            foreach ($propDetail['header'] as $header) {
                $strResult .= "<th " . $header['headerProp'] . ">" . $header['headerTitle'] . "</th>";
            }

            if ($propDetail['bolHasDeleteButton']) {
                $strResult .= '<th style="width:30px;">&nbsp;</th>';
            }

            $strResult .= "
              </tr>
            </thead>";

            if (isset($propDetail['input_content'])) $strResult .= '<tbody>';

            if (!isset($this->formDetailNewRow[$name])) $this->formDetailNewRow[$name] = '';

            for ($i = 0; $i < $propDetail['intDefaultRows']; $i++) {
                $isInitStrNewRowJS = false;
                $rowSequence = ($i + 1);
                $strResult .= '<tr id="rowDetail_' . $name . '_' . $this->formID . '_' . $rowSequence . '">';
                if (!$isInitStrNewRowJS)
                    $strNewRowJS = '<tr id="rowDetail_' . $name . '_' . $this->formID . '_{{counter}}">';

                if ($propDetail['hasSequenceColumn']) {
                    $strHiddenDeleted = $this->addHidden('isDeleted_' . $name . '_' . $rowSequence, 0, '', true);
                    $strResult .= '<td style="width:30px;" class="text-right"><span id="counterSequence_' . $name . '_' . $rowSequence . '">' . $rowSequence . '</span>' . $strHiddenDeleted . '</td>';

                    $strHiddenDeleted = $this->addHidden('isDeleted_' . $name . '_{{counter}}', 0, '', true);
                    if (!$isInitStrNewRowJS)
                        $strNewRowJS .= '<td style="width:30px;" class="text-right"><span id="counterSequence_' . $name . '_{{counter}}">' . $rowSequence . '</span>' . $strHiddenDeleted . '</td>';
                }

                foreach ($propDetail['header'] as $header) {
                    if (isset($propDetail['input_content'][$header['headerIndex']])) {
                        $arrInputColumn = $propDetail['input_content'][$header['headerIndex']];
                        $arrStrInputColumn = array();
                        $arrStrInputColumnJS = array();
                        foreach ($arrInputColumn as $column) {
                            $colummProp = $column;
                            $colummProp['inputName'] = $colummProp['inputName'] . '_' . ($i + 1);

                            // auto complete
                            if (isset($this->arrAutoCompleteElementDetail[$column['inputName']])) {
                                $this->arrAutoCompleteElement[$colummProp['inputName']] = $this->arrAutoCompleteElementDetail[$column['inputName']];
                            }

                            $colummProp['value'] = (isset($propDetail['arrData'][$i][$column['dataIndex']])) ? $propDetail['arrData'][$i][$column['dataIndex']] : '';

                            if (isset($colummProp['inputProp']['defaultValue']) && ($colummProp['value'] == ''))
                                $colummProp['value'] = $colummProp['inputProp']['defaultValue'];

                            $arrStrInputColumn[] = $this->mappDetailInputToForm($colummProp);
                            if (!$isInitStrNewRowJS) {
                                $colummProp = $column;
                                $colummProp['inputName'] = $column['inputName'] . '_{{counter}}';
                                $colummProp['value'] = '';
                                if (isset($colummProp['inputProp']['defaultValue']) && ($colummProp['value'] == ''))
                                    $colummProp['value'] = $colummProp['inputProp']['defaultValue'];

                                $arrStrInputColumnJS[] = $this->mappDetailInputToForm($colummProp, false);
                            }
                        }
                        $strResult .= '<td>' . implode('', $arrStrInputColumn) . '</td>';
                        if (!$isInitStrNewRowJS)
                            $strNewRowJS .= '<td>' . implode('', $arrStrInputColumnJS) . '</td>';
                    } else {
                        $strResult .= '<td>&nbsp;</td>';
                        if (!$isInitStrNewRowJS)
                            $strNewRowJS .= '<td>&nbsp;</td>';
                    }
                }

                if ($propDetail['bolHasDeleteButton']) {
                    $strResult .= '
          <td style="width:30px;">
            <button type="button" class="btn btn-danger btn-xs" onclick="javascript:deleteRowDetail(\'' . $this->formID . '\', \'' . $this->activeFormDetail . '\', ' . $rowSequence . ');">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </td>';
                    if (!$isInitStrNewRowJS)
                        $strNewRowJS .= '
              <td style="width:30px;">
                <button type="button" class="btn btn-danger btn-xs" onclick="javascript:deleteRowDetail(\'' . $this->formID . '\', \'' . $this->activeFormDetail . '\', {{counter}});">
                  <span class="glyphicon glyphicon-remove"></span>
                </button>
              </td>';
                }

                $strResult .= '</tr>';
                if (!$isInitStrNewRowJS) {
                    $strNewRowJS .= '</tr>';
                    if ($this->formDetailNewRow[$name] == '') $this->formDetailNewRow[$name] = $strNewRowJS;
                    $isInitStrNewRowJS = true;
                }
            }

            if (isset($propDetail['input'])) $strResult .= '</tbody>';

            $strResult .= '<tfoot>';

            if ($propDetail['bolHasAddMoreButton']) {
                $colSpan = count($this->formDetail[$this->activeFormDetail]['header']);
                if ($propDetail['bolHasDeleteButton']) $colSpan++;
                if ($propDetail['hasSequenceColumn']) $colSpan++;

                $strResult .= '
          <tr>
            <td colspan=' . $colSpan . '>
              <button class="btn btn-sm btn-default btn btn-primary" name="btnAddNew" id="btnAddNew_' . $name . '" type="button" onclick="javascript:addRowDetail(\'' . $this->formID . '\', \'' . $this->activeFormDetail . '\');">
                <i class="fal fa-plus"></i> Tambah Data
              </button>
            </td>
          </tr>';
            }

            $strResult .= '</tfoot>';

            $strResult .= '
          <input type="hidden" id="numShow_' . $this->activeFormDetail . '" name="numShow_' . $this->activeFormDetail . '" value="' . $i . '"/>
          <input type="hidden" id="numSequence_' . $this->activeFormDetail . '" name="numSequence_' . $this->activeFormDetail . '" value="' . $i . '"/>
      </table></div>';
            $this->formObject[$elName] = array('string' => $strResult, 'title' => $name);

            if (!empty($this->fieldset))
                $this->fieldset[$this->activeFieldset]['element'][$elName] = $this->formObject[$elName]['string'];
        }
    }

    private function mappDetailInputToForm($detailInput, $generateValidation = true)
    {
        /*
      'inputType' => $inputType,
      'inputName' => $inputName,
      'inputProp' => $inputProp,
      'dataIndex' => $dataIndex,
      'htmlBefore' => $htmlBefore,
      'htmlAfter' => $htmlAfter,    
    */
        $strResult = '';
        switch ($detailInput['inputType']) {
            case 'label':
            case 'text':
            case 'autocomplete':
            case 'autoComplete':
            case 'checkbox':

                // addFormObject($type, $title, $name, $value, $arrAttribute, $dataType, $bolRequired = true, $bolEnable = true, $htmlBefore='', $htmlAfter='', $renderLabel = true, $serverAction="", $jsFunction = '',$intInputWidth=12, $arrDataOption=array(), $indexDataGroup='category', $useInDetail = false)
                $strResult = $this->addFormObject($detailInput['inputType'], '', $detailInput['inputName'], $detailInput['value'], $detailInput['inputProp'], $detailInput['inputDataType'], false, true, $detailInput['htmlBefore'], $detailInput['htmlAfter'],  false, '', '', 12, $detailInput['arrDataOption'], $detailInput['indexDataGroup'], true, $generateValidation);
                # code...
                break;
            case 'hidden':
                $strResult = $this->addHidden($detailInput['inputName'], $detailInput['value'], '', true);
                break;
            default:
                # code...
                break;
        }

        return $strResult;
    }


    public function addHidden($name, $value = '', $strAttr = '', $useInDetail = false)
    {
        if (isset($this->formObject[$name])) {
            throw new \Exception('input with id ' . $name . ' already exists');
            exit();
        }
        $strResult = '<input type="hidden" name="' . $name . '" id="' . $name . '_' . $this->formID . '" value="' . esc($value) . "\" " . $strAttr . " />\n";

        if ($useInDetail) return $strResult;
        $this->formObject[$name] = array('string' => $strResult);
    }

    public function addBlank()
    {
        $this->blankObject++;
        $this->addFormObject('blank', "", "blankLabel" . $this->blankObject, "", array(), 'string', false, true);
    }

    public function addLabel($title, $name, $value = "", $arrAttribute = array(), $dataType = "string", $bolRequired = false, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('label', $title, $name, $value, $arrAttribute, $dataType, false, true, '', '', $renderLabel, "", '', $intInputWidth);
    }

    public function addInput($title, $name, $value = "", $arrAttribute = array(), $dataType = "string", $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('text', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth);
    }

    // $arrDataOption = array('key' =>  'value');
    // $arrDataOption = array('key' =>  array('label' => '', 'category'=> '',...);
    public function addInputAutoComplete($title, $name, $arrDataOption = array(), $value, $indexDataGroup = 'category', $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('autoComplete', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption, $indexDataGroup);
    }

    public function addFile($title, $name, $value = "", $arrAttribute = array(), $dataType = "string", $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('file', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth);
    }

    public function addTextArea($title, $name, $value = "", $arrAttribute = array(), $dataType = "string", $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('textArea', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth);
    }

    public function addSelect($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('select', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption, '');
    }

    public function addCheckBox($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('checkBox', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
    }

    public function addCheckBoxInline($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('checkBoxInline', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
    }

    public function addCheckBoxToggle($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('checkBoxToggle', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
    }

    public function addRadio($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true,  $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('radio', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
    }

    public function addRadioInline($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $renderLabel = true, $intInputWidth = 12, $jsFunction = '')
    {
        $this->addFormObject('radioInline', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
    }

    public function addEditor($title, $name, $value, $bolRequired = true, $bolEnable = true, $renderLabel = true, $intInputWidth = 12)
    {
        $this->addFormObject('editor', $title, $name, $value, array(), '', $bolRequired, $bolEnable, '', '', $renderLabel, "", '', $intInputWidth, array());
    }

    public function addSubmit($name, $value, $arrAttribute, $bolEnable = true, $htmlBefore = "", $htmlAfter = "", $serverAction = "")
    {
        $this->addCommonButton("submit", $name, $value, $arrAttribute, $bolEnable, $htmlBefore, $htmlAfter, $serverAction);
    }

    public function addReset($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore = "", $htmlAfter = "", $serverAction = null)
    {
        $this->addCommonButton("reset", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore, $htmlAfter, "");
    }

    public function addButton($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore = "", $htmlAfter = "", $serverAction = null)
    {
        $this->addCommonButton("button", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore, $htmlAfter, "");
    }
    // public function addHeadingButton($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = null)
    // {
    //   $this->addHeadingButton("button", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore , $htmlAfter, "");
    // }

    function addLiteral($title, $name, $literalValue, $renderLabel = true, $arrLabelAttribute = null, $intInputWidth = 12)
    {
        $this->addFormObject('literal', $title, $name, $literalValue, array(), 'string', false, true, '', '', $renderLabel, "", "", $intInputWidth);
    }

    public function addValidationRule($idElement, $arrRule, $arrMessage)
    {
        if (isset($this->validationRules[$idElement]))
            $this->validationRules[$idElement] = array_merge($this->validationRules[$idElement], $arrRule);
        else
            $this->validationRules[$idElement] = $arrRule;

        if (isset($this->validationMessages[$idElement]))
            $this->validationMessages[$idElement] = array_merge($this->validationMessages[$idElement], $arrMessage);
        else
            $this->validationMessages[$idElement] = $arrMessage;
    }

    public function render()
    {
        $strMultipart = ($this->formMultipart) ? 'enctype="multipart/form-data"' : '';

        if (!$this->disableFormJS)
            $this->resultString .= $this->generateJSString();

        $strCaption = ($this->caption == '') ? $this->caption : $this->caption;

        if (!service("request")->getGetPost('is-nav-ajax') && !$this->isFormOnly) {

            $this->resultString .= '
      <div class="row">
        <div class="col-xl-12">
          <div id="panel_' . $this->formID . '" class="panel">
            <div class="panel-hdr">
              <h2>' . $strCaption . '</h2>
              ';
            $this->resultString .= $this->renderHeadingButton();
            $this->resultString .= '        
            </div>

            <div class="panel-container show">
				      <div class="panel-content">
                <div class="alert alert-success fade in" id="' . $this->formID . '_success_alert" style="display:none;">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                  </button>  
                  <i class="fa-fw fal fa-check shake animated"></i>
                  <strong>Success</strong>
                </div>

                <div class="alert alert-danger fade in" id="' . $this->formID . '_error_alert" style="display:none;">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                  </button>  
                  <i class="fa-fw fal fa-times shake animated"></i>
                  <strong>Error</strong>
                </div>';
        }

        $strClassForm = "";
        if (strtolower($this->formStyle) == 'inline') {
            $strClassForm = 'class="form-horizontal"';
        }
        $filler = "\n                            ";
        if ($this->renderFormTag) {
            $this->resultString .= $filler . '<form class="needs-validation" name="' . $this->formID . '" ' . $strClassForm . ' ' . $strMultipart . ' id="' . $this->formID . '" action="' . site_url($this->formAction) . '" method="' . $this->method . '">';
        }
        $this->resultString .= $filler . '  <div class="alert alert-danger" id="alert_' . $this->formID . '" role="alert" style="display:none;"></div>';

        $this->resultString .= $filler . "  <div class=\"panel-content p-0\">\n";

        if ($this->formButtonPosition == 'top')
            $this->resultString .= $this->renderFormButton();

        $this->resultString .= $this->renderFormObject();

        if ($this->formButtonPosition == 'bottom')
            $this->resultString .= $this->renderFormButton();

        $this->resultString .= $filler . "  </div>\n";
        if ($this->renderFormTag) {
            $this->resultString .= "
                </form>\n";
        }
        if (!service("request")->getGetPost('is-nav-ajax') && !$this->isFormOnly) {
            $this->resultString .= '
              </div>
            </div>
          </div>
        </div>
      </div>';
        }

        if (service("request")->getGetPost('is-nav-ajax')) {
            echo $this->resultString;
            exit();
        }

        return $this->resultString;
    }

    private function addCommonButton($buttonType, $name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore = "", $htmlAfter = "", $serverAction = "")
    {
        $strResult = '';
        $arrAttr = array('name' => $name . "_" . $this->formID, 'id' => $name . "_" . $this->formID, 'value' => $value);
        $arrInputAttr = array_merge($arrAttribute, $arrAttr);

        $value = ($value == '') ? $value : $value;

        // if ($buttonType == 'button') {
        //   $this->addFormObject('button', '', $name, $value, $arrInputAttr, 'string', false, $bolEnabled, $htmlBefore, $htmlAfter, false, $serverAction);
        // }else {
        switch ($buttonType) {
            case 'submit':
                $strJS = "onclick=\"javascript:doSubmit_" . $this->formID . "('" . $name . "', '" . $serverAction . "');\"";
                if (!isset($arrInputAttr['class'])) {
                    $arrInputAttr['class'] = 'pull-left btn btn-sm btn-primary btn-submit-form btn-custom-report';
                }
                if (!isset($arrInputAttr['style'])) {
                    $arrInputAttr['style'] = '';
                } else {
                    $arrInputAttr['style'] = trim($arrInputAttr['style']);
                    if (substr($arrInputAttr['style'], strlen($arrInputAttr['style']) - 1, 1) != ';') {
                        $arrInputAttr['style'] .= ';';
                    }
                }
                $arrInputAttr['style'] .= 'margin-right: 8px';
                $arrInputAttr['type'] = 'submit';
                if (isset($arrInputAttr['fa-icon'])) {
                    $content = '<i class="fal ' . $arrInputAttr['fa-icon'] . '"></i> ' . $value;
                } else {
                    $content = '<i class="fal fa-save"></i> ' . $value;
                }
                $strResult .= form_button($arrInputAttr, $content, $strJS) . "";
                break;
            case 'button':
                if (!isset($arrInputAttr['class'])) {
                    $arrInputAttr['class'] = 'pull-left btn btn-sm btn-primary';
                }
                $arrInputAttr['type'] = 'button';
                if (!isset($arrInputAttr['style'])) {
                    $arrInputAttr['style'] = '';
                } else {
                    $arrInputAttr['style'] = trim($arrInputAttr['style']);
                    if (substr($arrInputAttr['style'], strlen($arrInputAttr['style']) - 1, 1) != ';') {
                        $arrInputAttr['style'] .= ';';
                    }
                }
                $arrInputAttr['style'] .= 'margin-right: 8px';
                if (isset($arrInputAttr['fa-icon'])) {
                    $content = '<i class="fal ' . $arrInputAttr['fa-icon'] . '"></i> ' . $value;
                } else {
                    $content = '<i class="fal fa-save"></i> ' . $value;
                }
                $strJSString = ''; //'onclick="$(\'#'.$this->formID.'\')[0].reset(); $(\'#'.$this->formID.' input:hidden\').val(\'\');$(\'#'.$this->formID.' input:checkbox\').removeAttr(\'checked\'); $(\'#'.$this->formID.' input:radio\').removeAttr(\'checked\');"';

                $strResult .= form_button($arrInputAttr, $content, $strJSString);
                break;
            case 'reset':
                $arrInputAttr['class'] = 'pull-left btn btn-sm btn-default btn-reset-report';
                $arrInputAttr['type'] = 'reset';
                $arrInputAttr['style'] = 'margin-right: 8px';
                if (isset($arrInputAttr['fa-icon'])) {
                    $content = '<i class="fa ' . $arrInputAttr['fa-icon'] . '"></i> ' . $value;
                } else {
                    $content = '<i class="fal fa-refresh"></i> ' . $value;
                }
                $strJSString = 'onclick="$(\'#' . $this->formID . '\')[0].reset(); $(\'#' . $this->formID . ' input:hidden\').val(\'\');$(\'#' . $this->formID . ' input:checkbox\').removeAttr(\'checked\'); $(\'#' . $this->formID . ' input:radio\').removeAttr(\'checked\');"';

                $strResult .= form_button($arrInputAttr, $content, $strJSString);
                break;
        }
        $this->formActionButton[$arrAttr['name']] = $strResult;

        // }
    }

    public function addHeadingButton($buttonType, $name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore = "", $htmlAfter = "", $serverAction = "")
    {
        $strResult = '';
        $arrAttr = array(
            'name' => $name . "_" . $this->formID,
            'id' => $name . "_" . $this->formID,
            'form-target' => $this->formID,
            'action-target' => site_url($this->controller_name . '/' . $this->method_name)
        );

        $arrInputAttr = array_merge($arrAttribute, $arrAttr);

        $value = ($value == '') ? $value : $value;
        $strResult .= '<span ' . _parse_form_attributes($arrInputAttr, array()) . '>' . $value . '</span>';

        $this->headingActionButton[$arrAttr['name']] = $strResult;
    }

    private function addFormObject(
        $type,
        $title,
        $name,
        $value,
        $arrAttribute,
        $dataType,
        $bolRequired = true,
        $bolEnable = true,
        $htmlBefore = '',
        $htmlAfter = '',
        $renderLabel = true,
        $serverAction = "",
        $jsFunction = '',
        $intInputWidth = 0,
        $arrDataOption = array(),
        $indexDataGroup = 'category',
        $useInDetail = false,
        $generateValidation = true
    ) {
        if ($type != 'blank') {
            if (isset($this->formObject[$name])) {
                throw new \Exception('input with id ' . $name . ' already exists');
                exit();
            }
        }

        $strHelpBlock = '';
        if (isset($arrAttribute['helpblock'])) {
            $strHelpBlock = $arrAttribute['helpblock'];
            unset($arrAttribute['helpblock']);
        }

        if (!isset($arrAttribute["class"])) {
            if ($type == 'select')
                $arrAttribute["class"] = "select2 custom-select";
            else
                $arrAttribute["class"] = 'form-control';
        }

        if (!isset($arrAttribute["autocomplete"])) {
            $arrAttribute["autocomplete"] = 'off';
        }


        $arrAttr = array(
            'name' => $name,
            'id' => $name . '_' . $this->formID,
            'class' => $arrAttribute["class"],
            'value' => $value
        );

        $arrInputAttr = array_merge($arrAttribute, $arrAttr);
        if (!$bolEnable) $arrInputAttr['disabled'] = 'true';

        if ($bolRequired) $arrInputAttr['required'] = 'required';

        $titleLabel = ($title == '') ? $title : $title;

        $groupWidth = ($type == 'literal') ? 12 : 9;
        $groupWidthMD = ($type == 'literal') ? 12 : 10;
        $strClassLabel = (!$renderLabel) ? 'sr-only' : 'col-md-2 col-sm-3 control-label text-left';

        if (strtolower($this->formStyle) == 'basic') {
            $strResult = '
      <div class="form-group col-sm-' . $intInputWidth . ' col-xs-12">';
            if (strtolower($type) != 'checkbox' && strtolower($type) != 'checkboxtoggle' && strtolower($type) != 'literal') {
                if ($renderLabel) {
                    $strResult .= '<label class="form-label" for="' . $name . '_' . $this->formID . '">' . $titleLabel . '</label>';
                }
            }

            $useInputGroup = true;
            if ($type != 'literal') {
                $strInputGroup = '<div class="input-group-validation input-group">';
            } else {
                $strInputGroup = '<div>';
            }
        } else {
            $strResult = '
      <div class="form-group">
        <label class="' . $strClassLabel . '" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">' . $titleLabel . '</label>
        <div class="col-md-' . $groupWidthMD . ' col-sm-' . $groupWidth . '">';


            if ($intInputWidth == 0) {
                $strInputGroupClass = "input-group ";
            } else {
                $strInputGroupClass = "input-group col-sm-" . $intInputWidth . " col-xs-12";
            }

            $useInputGroup = true;
            $strInputGroup = '<div class="input-group-validation ' . $strInputGroupClass . '">';
        }

        $strLabelBefore = ($htmlBefore != '') ? '<div class="input-group-prepend"><span class="input-group-text">' . $htmlBefore . '</span></div>' : '';
        $strLabelAfter = ($htmlAfter != '') ? '<div class="input-group-append"><span class="input-group-text">' . $htmlAfter . '</span></div>' : '';


        //$strInputGroupClass = ( ($strLabelBefore != '') || ($strLabelAfter != '') || ($dataType == 'date') || ($dataType == 'time')) ? "input-group col-md-".$intInputWidth : "";


        $strFormElement = '';

        switch ($type) {
            case 'button':
                if (isset($arrAttribute['class'])) $arrInputAttr['class'] = $arrAttribute['class'];
                else $arrInputAttr['class'] = 'btn btn-sm btn-default';
                $value = ($value == '') ? $value : $value;
                $strResult .= form_button($arrInputAttr, $value, '');
                if ($useInDetail) return $strFormElement;
                break;

            case 'literal':
                $strFormElement .= ($value == '') ? $value : $value;
                if ($useInDetail) return $strFormElement;
                break;

            case 'blank':
                $strFormElement .= $value;
                break;

            case 'label':
                $strFormElement .= '<p class="form-control-static" id="' . $name . '_' . $this->formID . '">' . $value . '</p>';
                if ($useInDetail) return $strFormElement;
                break;

            case 'text':
                switch ($dataType) {
                    case "date":
                        $this->arrDateTimeElement[$name] = array(
                            'name' => $name,
                            'value' => $value,
                            'format' => $this->dateFormat,
                            'dateRangeOption' => array(
                                'singleDatePicker' => 'true',
                                'timePicker' => 'false',
                                'timePicker24Hour' => 'true',
                                'locale' => "{format: '" . $this->dateFormat . "'}",
                            )
                        );
                        //$this->arrDateElement[$name] = array('name' => $name, 'datePickerOption' => array());
                        if (isset($arrAttribute['datePickerOption'])) {
                            $this->arrDateTimeElement[$name]['datePickerOption'] = $arrAttribute['datePickerOption'];
                            // handle error php > 5.3
                            unset($arrInputAttr['datePickerOption']);
                        }

                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        $strFormElement .= '<div class="input-group-append"><span class="input-group-text fs-xl"><i class="fal fa-calendar-alt"></i></span></div>';
                        if ($useInDetail) return $strFormElement;

                        if ($generateValidation)
                            $this->addValidationRule($name . '_' . $this->formID, array('date' => 'true'), array('date' => 'please input valid date'));
                        break;
                    case "datetime":
                        $this->arrDateTimeElement[$name] = array(
                            'name' => $name,
                            'value' => $value,
                            'format' => $this->datetimeFormat,
                            'dateRangeOption' => array(
                                'singleDatePicker' => 'true',
                                'timePicker' => 'true',
                                'timePicker24Hour' => 'true',
                                'locale' => "{format: '" . $this->datetimeFormat . "'}",
                            )
                        );
                        //$this->arrDateElement[$name] = array('name' => $name, 'datePickerOption' => array());
                        if (isset($arrAttribute['datePickerOption'])) {
                            $this->arrDateTimeElement[$name]['datePickerOption'] = $arrAttribute['datePickerOption'];
                            // handle error php > 5.3
                            unset($arrInputAttr['datePickerOption']);
                        }

                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        $strFormElement .= '<div class="input-group-append"><span class="input-group-text fs-xl"><i class="fal fa-calendar-alt"></i></span></div>';
                        if ($useInDetail) return $strFormElement;

                        if ($generateValidation)
                            $this->addValidationRule($name . '_' . $this->formID, array('date' => 'true'), array('date' => 'please input valid date'));
                        break;
                    case "datetimerange":
                        $this->arrDateRangeElement[$name] = array(
                            'name' => $name,
                            'value' => $value,
                            'format' => $this->datetimeFormat,
                            'dateRangeOption' => array(
                                'timePicker' => 'true',
                                'timePicker24Hour' => 'true',
                                'locale' => "{format: '" . $this->datetimeFormat . "'}",
                            )
                        );


                        //$strFormElement .= '<div class="input-group">';
                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        $strFormElement .= '<div class="input-group-append"><span class="input-group-text fs-xl"><i class="fal fa-calendar-alt"></i></span></div>';
                        $strFormElement .= '<input type="hidden" name="' . $name . '_from' . '_' . $this->formID . '" id="' . $name . '_from' . '_' . $this->formID . '" />';
                        $strFormElement .= '<input type="hidden" name="' . $name . '_thru' . '_' . $this->formID . '" id="' . $name . '_thru' . '_' . $this->formID . '" />';

                        $this->addValidationRule($name . '_' . $this->formID, array('date' => 'true'), array('date' => 'please input valid date'));
                        //$strFormElement .= '</div>';

                        if ($useInDetail) return $strFormElement;

                        break;
                    case "daterange":
                        $this->arrDateRangeElement[$name] = array(
                            'name' => $name,
                            'value' => $value,
                            'format' => str_replace(':ss', '', $this->dateFormat),
                            'dateRangeOption' => array(
                                'timePicker' => 'false',
                                'timePicker24Hour' => 'true',
                                'locale' => "{format: '" . $this->dateFormat . "'}",
                            )
                        );
                        // $strFormElement .= '<div class="input-group">';
                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        $strFormElement .= '<div class="input-group-append"><span class="input-group-text fs-xl"><i class="fal fa-calendar-alt"></i></span></div>';
                        $strFormElement .= '<input type="hidden" name="' . $name . '_from' . '_' . $this->formID . '" id="' . $name . '_from' . '_' . $this->formID . '" />';
                        $strFormElement .= '<input type="hidden" name="' . $name . '_thru' . '_' . $this->formID . '" id="' . $name . '_thru' . '_' . $this->formID . '" />';
                        // $strFormElement .= '</div>';
                        if ($useInDetail) return $strFormElement;

                        break;
                    case "time":
                        //$arrInputAttr['readonly'] = 'readonly';
                        $this->arrTimeElement[$name] = $name;
                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        $strFormElement .= '<div class="input-group-append"><span class="input-group-text fs-xl"><i class="fal fa-clock"></i></span></div>';
                        //$this->addValidationRule($name, array('date' => 'true'), array('date' => 'please input valid date'));
                        if ($useInDetail) return $strFormElement;
                        break;
                    case "numeric":
                        if (isset($arrAttribute['class'])) $arrInputAttr['class'] = $arrAttribute['class'];
                        else $arrInputAttr['class'] = 'form-control text-right';
                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        if ($generateValidation)
                            $this->addValidationRule($name . '_' . $this->formID, array('number' => 'true'), array('date' => 'please input valid number'));
                        if ($useInDetail) return $strFormElement;
                        break;
                    case "password":
                        $strFormElement .= form_password($arrInputAttr, $value, $jsFunction);
                        if ($useInDetail) return $strFormElement;
                        break;
                    default:
                        $arrInputAttr['type'] = ($dataType == 'string') ? 'text' : $dataType;
                        if ($arrInputAttr['type'] == 'float') {
                            $arrInputAttr['type'] = 'number';
                            $arrInputAttr['step'] = '0.01';
                        }
                        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
                        if ($useInDetail) return $strFormElement;
                        break;
                }
                break;

            case 'autoComplete':
                $arrInputAttr['list'] = 'list_' . $name . '_' . $this->formID;

                $strLabelAutoCompleteBefore = ($htmlBefore != '') ? '<div class="input-group-addon" style="visibility: hidden;">' . $htmlBefore . '</div>' : '';
                $strLabelAutoCompleteAfter = ($htmlAfter != '') ? '<div class="input-group-addon" style="visibility: hidden;">' . $htmlAfter . '</div>' : '';

                // draw input text
                $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);

                if (!$useInDetail) {
                    //draw option auto complete
                    $strDataList = 'var ' . $name . '_options_' . $this->formID . ' = ';
                    $arrTempOptions = array();


                    //LOGIC untuk data dari fungsi generateList dengan tipe array value-text
                    if ($arrDataOption != null && count($arrDataOption) > 0) {
                        $isFromTextValue = false;
                        foreach ($arrDataOption as $testOption) {
                            if (is_array($testOption) && isset($testOption["text"]) && isset($testOption["value"])) {
                                $isFromTextValue = true;
                                break;
                            }
                        }
                        if ($isFromTextValue) {
                            $arrDataOptionTemp = array();
                            foreach ($arrDataOption as $testOption) {
                                $arrDataOptionTemp[$testOption["value"]] = $testOption["text"];
                            }
                            $arrDataOption = $arrDataOptionTemp;
                        }
                    }
                    //END of LOGIC untuk data dari fungsi generateList dengan tipe array value-text

                    foreach ($arrDataOption as $key => $valueOpt) {
                        if (is_array($valueOpt)) {
                            if ($indexDataGroup != '') {
                                if (!isset($valueOpt[$indexDataGroup])) {
                                    throw new \Exception('no data with index ' . $indexDataGroup . ' on auto complete ' . $name);
                                    exit();
                                }
                            }

                            $arrDefaultOptions = array('value' => $key);
                            $arrTempOptions[] = array_merge($valueOpt, $arrDefaultOptions);
                        } else {
                            $indexDataGroup = '';
                            $arrTempOptions[] = array('value' => $key, 'label' => $valueOpt);
                        }
                    }

                    // sorting for grouping auto complete
                    $arrTmpForSorting = array();
                    if (!empty($indexDataGroup)) {
                        foreach ($arrTempOptions as $arrProp) {
                            $arrTmpForSorting[$arrProp[$indexDataGroup]][] = $arrProp;
                        }
                        ksort($arrTmpForSorting);
                        $arrTempOptions = array();
                        foreach ($arrTmpForSorting as $group => $rowGroup) {
                            foreach ($rowGroup as $row) {
                                $arrTempOptions[] = $row;
                            }
                        }
                    }
                    $strDataList .= json_encode($arrTempOptions);
                    $this->arrAutoCompleteElement[$name] = array('source' => $name . '_options_' . $this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);
                }

                $strHelpText = '';

                if ($value != '')
                    $strHelpText = (isset($arrDataOption[$value])) ? $arrDataOption[$value] : '';

                $strFormElement .= $strLabelAfter;
                if ($strLabelAutoCompleteBefore == '' && $strLabelAutoCompleteAfter == '') {
                    $strFormElement .= '<p class="help-block" id="label_' . $name . '">' . $strHelpText . '</p>';
                    //$strFormElement .= '</>';
                } else {
                    $strFormElement .= '</div>';
                    $strFormElement .= '<div class="input-group">';
                    $strFormElement .= $strLabelAutoCompleteBefore;
                    $strFormElement .= '<p class="help-block" id="label_' . $name . '">' . $strHelpText . '</p>';
                    $strFormElement .= $strLabelAutoCompleteAfter;
                }

                if ($useInDetail)
                    return $strFormElement;

                $strLabelAfter = '';
                break;

            case 'textArea':
                //$arrInputAttr['style'] = 'resize:vertical;';
                $strFormElement .= form_textarea($arrInputAttr, $value, $jsFunction);
                break;

            case 'textAreaHtml':
                //$arrInputAttr['style'] = 'resize:vertical;';
                $strFormElement .= form_textarea($arrInputAttr, $value, $jsFunction);
                break;
            case 'select':
                /*if (!isset($arrInputAttr['class']) || $arrInputAttr['class'] == "")
        {
          $arrInputAttr['class'] = 'select2';
        }*/
                $arrInputAttr['style'] = 'width:100%';

                $this->arrSelectElement[$name] = array(
                    'id' => $name . '_' . $this->formID,
                    'htmlAfter' => $strLabelAfter,
                    'hasEmptyOption' => false,
                    'placeHolder' => ''
                );

                if (isset($arrDataOption[0])) {
                    $this->arrSelectElement[$name]['hasEmptyOption'] = true;
                    $this->arrSelectElement[$name]['placeHolder'] = current($arrDataOption);
                }


                $isSelectMultiple = false;
                if (isset($arrInputAttr['multiple'])) {
                    $isSelectMultiple = true;
                    $elName = $arrInputAttr['name'] . '[]';
                    $arrInputAttr['name'] = $elName;
                }

                //LOGIC untuk data dari fungsi generateList dengan tipe array value-text
                if ($arrDataOption != null && count($arrDataOption) > 0) {
                    $isFromTextValue = false;
                    foreach ($arrDataOption as $testKey => $testOption) {
                        if (is_array($testOption) && isset($testOption["text"]) && isset($testOption["value"])) {
                            $isFromTextValue = true;
                            break;
                        }
                    }
                    if ($isFromTextValue) {
                        $arrDataOptionTemp = array();
                        foreach ($arrDataOption as $testOption) {
                            $arrDataOptionTemp[$testOption["value"]] = $testOption["text"];
                        }
                        $arrDataOption = $arrDataOptionTemp;
                    }
                }
                //End of LOGIC untuk data dari fungsi generateList dengan tipe array value-text
                //print_r($jsFunction);
                if ($isSelectMultiple) {
                    $arrInputAttr2 = $arrInputAttr;
                    unset($arrInputAttr2['value']);
                    $strFormElement .= form_dropdown($arrInputAttr2, $arrDataOption, $value, $jsFunction);
                    //echo form_dropdown($arrInputAttr2, $arrDataOption, $value, $jsFunction);
                } else {
                    $strFormElement .= form_dropdown($arrInputAttr, $arrDataOption, $value, $jsFunction);
                }
                break;

            case 'checkBox':
                $arrInputAttr['class'] = 'checkbox style-0';
                $useInputGroup = false;
                foreach ($arrDataOption as $key => $val) {
                    $arrInputAttr['value'] = $key;
                    $arrInputAttr['id'] = $name . '_' . $this->formID . '_' . $key;
                    $strFormElement .= '<div class="checkbox"><label>';
                    $checked = ($key == $value) ? true : false;
                    $strFormElement .= form_checkbox($arrInputAttr, $key, $checked, $jsFunction) . '<span>' . $val . '</span>';
                    $strFormElement .= '</label></div>';
                }
                break;

            case 'checkBoxInline':
                $arrInputAttr['class'] = 'checkbox style-0';
                $useInputGroup = false;
                if (strtolower($this->formStyle) == 'basic') $strFormElement .= '<div class="input-group">';
                foreach ($arrDataOption as $key => $val) {
                    $arrInputAttr['value'] = $key;
                    $arrInputAttr['id'] = $name . '_' . $this->formID . '_' . $key;

                    $strFormElement .= '<label class="checkbox-inline" style="margin-top: 10px">';
                    $checked = ($key == $value) ? true : false;
                    $strFormElement .= form_checkbox($arrInputAttr, $key, $checked, $jsFunction) . '<span>' . $val . '</span>';
                    $strFormElement .= '</label>';
                }
                if (strtolower($this->formStyle) == 'basic') $strFormElement .= '</div>';
                break;

            case 'checkBoxToggle':
                $arrInputAttr['class'] = 'custom-control-input';
                $useInputGroup = false;
                $strFormElement .= '
          <div class="form-group">
            <label class="form-label">' . $titleLabel . '</label>
            <div class="custom-control custom-switch">';
                $valCheckbox = 1;
                $valYes = "Yes";
                $valNo = "No";
                foreach ($arrDataOption as $key => $val) {
                    if ($key == 0) {
                        $valNo = $val;
                    } else {
                        $valYes = $arrDataOption[1];
                        $valCheckbox = $key;
                    }
                }

                $arrInputAttr['value'] = $valCheckbox;
                $arrInputAttr['id'] = $name . '_' . $this->formID . '_' . $valCheckbox;

                //$strFormElement .= '<label class="toggle" style="font-size: unset; margin-top: 10px">';
                $checked = ($valCheckbox == $value) ? true : false;
                $strFormElement .= form_checkbox($arrInputAttr, $key, $checked, $jsFunction) .
                    '<label class="custom-control-label" for="' . $arrInputAttr['id'] . '">' . $valYes . '</label>';
                //$strFormElement .= '</label>';
                $strFormElement .= '</div></div>';
                break;
            case 'radio':
                $arrInputAttr['class'] = 'radiobox style-0';
                $useInputGroup = false;
                foreach ($arrDataOption as $key => $val) {
                    $arrInputAttr['value'] = $key;
                    $arrInputAttr['id'] = $name . '_' . $this->formID . '_' . $key;
                    $strFormElement .= '<div class="radio"><label>';
                    $checked = ($key == $value) ? true : false;
                    $strFormElement .= form_radio($arrInputAttr, $key, $checked, $jsFunction) . '<span>' . $val . '</span>';
                    $strFormElement .= '</label></div>';
                }
                break;

            case 'radioInline':
                $arrInputAttr['class'] = 'radiobox style-0';
                $useInputGroup = false;
                if (strtolower($this->formStyle) == 'basic') $strFormElement .= '<div class="input-group">';
                foreach ($arrDataOption as $key => $val) {
                    $arrInputAttr['value'] = $key;
                    $arrInputAttr['id'] = $name . '_' . $this->formID . '_' . $key;

                    $strFormElement .= '<label class="radio-inline" style="padding-left: 0px">';
                    $checked = ($key == $value) ? true : false;
                    $strFormElement .= form_radio($arrInputAttr, $key, $checked, $jsFunction) . '<span>' . $val . '</span>';
                    $strFormElement .= '</label>';
                }
                if (strtolower($this->formStyle) == 'basic') $strFormElement .= '</div>';
                break;

            case 'file':
                $this->formMultipart = true;
                $strLabelBefore = ($htmlBefore != '') ? '' . $htmlBefore . '</div><div style="margin-top: 12px" class="input-group-validation input-group">' : '';
                $strFormElement .= form_upload($arrInputAttr, '', $jsFunction);
                break;

            case 'editor':
                $editorName = "editor_" . $name . '_' . $this->formID;
                $strFormElement .= '<div class="summernote" id="' . $editorName . '">' . $value . '</div>';
                $this->arrEditorElement[$editorName] = array('id' => $editorName, 'hiddenEl' => $name);
                if (!empty($value)) $this->arrEditorElement[$editorName]['code'] = $value;
                $this->addHidden($name, $value, "editor-id-dest='" . $editorName . "'");
                $name = $editorName;
                break;
        }
        if (isset($arrInputAttr['required'])) {
            if (isset($arrInputAttr['data-invalid-message'])) {
                $strLabelAfter .= '<div class="invalid-feedback">' . $arrInputAttr['data-invalid-message'] . '</div>';
            } else {
                $strLabelAfter .= '<div class="invalid-feedback">Field is mandatory</div>';
            }
        }

        if ($useInputGroup) $strResult .= $strInputGroup . $strLabelBefore . $strFormElement . $strLabelAfter . '</div>';
        else $strResult .= $strLabelBefore . $strFormElement . $strLabelAfter;


        if ($strHelpBlock != '') {
            if ($strHelpBlock == strip_tags($strHelpBlock)) {
                $strHelpBlock = ($strHelpBlock == '') ? $strHelpBlock : $strHelpBlock;
            }
            $strResult .= '<span id="helpBlock_' . $name . '_' . $this->formID . '" class="help-block"><small>' . $strHelpBlock . '</small></span>';
        }

        if (strtolower($this->formStyle) == 'basic') {
            $strResult .= '</div>';
        } else {
            $strResult .= '</div></div>';
        }

        if ($htmlAfter != '') {
            //$strResult .= '<div style="clear:both"></div>';
        }


        if ($bolRequired) $this->addValidationRule($name . '_' . $this->formID, array('required' => 'true'), array('required' => 'please enter ' . $title));
        $this->formObject[$name] = array('string' => $strResult, 'title' => $title);
        if (!empty($this->fieldset) && ($this->activeFieldset != '')) $this->fieldset[$this->activeFieldset]['element'][$name] = $this->formObject[$name]['string'];
        if (!empty($this->tabs)) $this->tabs[$this->activeTab]['element'][$name] = $this->formObject[$name]['string'];
    }

    private function renderFormObject()
    {

        //print_r($this->tabs);
        //die();
        $this->addHidden('submitButton_' . $this->formID, '');
        $this->addHidden('submitAction_' . $this->formID, '');

        if ($this->hasNavigationHeader) {
            if (isset($this->navigationTableSource['table']) && !empty($this->navigationTableSource['table'])) {
                $tableKey = (isset($this->navigationTableSource['key'])) ? $this->navigationTableSource['key'] : 'id';

                $db = \Config\Database::connect();
                $arrQueryMax = $db->table($this->navigationTableSource['table'])->selectMax($tableKey)->get()->getRowArray();
                $arrQueryMin = $db->table($this->navigationTableSource['table'])->selectMin($tableKey)->get()->getRowArray();
                $idMax = intval($arrQueryMax[$tableKey]);
                $idMin = intval($arrQueryMin[$tableKey]);

                $this->addHidden('maxIDTrans', $idMax);
                $this->addHidden('minIDTrans', $idMin);
            }
        }
        /*
    print_r($this->tabs);
    echo "<br>";
    print_r($this->fieldset);
    */
        if (!empty($this->tabs)) {
            $strResult = '<div class="form-group col-sm-12 col-xs-12 tabbable"><ul class="nav nav-tabs" role="tablist">';
            $counter = 0;

            //generate tab link
            foreach ($this->tabs as $id => $tabs) {
                $strTitleTab = ($tabs['name'] == '') ? $tabs['name'] : $tabs['name'];

                $strClass = ($counter == 0) ? ' active' : '';
                $strResult .= '<li class="nav-item"><a class="nav-link' . $strClass . '" href="#tabContent_' . $id . '" id="' . $id . '" data-toggle="tab" role="tab">' . $strTitleTab . '</a>';
                $strResult .= '</li>';
                $counter++;
            }
            $strResult .= '</ul>';

            // generate tab content
            $strResult .= '<div class="tab-content border border-top-0 p-3">';
            $counter = 0;
            foreach ($this->tabs as $id => $tabs) {
                $strClass = ($counter == 0) ? 'active' : '';
                $strResult .= '<div class="tab-pane ' . $strClass . '" id="tabContent_' . $id . '">';
                if (isset($tabs['element'])) {
                    //$isFieldset = false;
                    $hasFieldSet = false;

                    $activeFieldSet = '';
                    $strElement = '';
                    foreach ($tabs['element'] as $name => $stringElement) {
                        if (isset($this->fieldset[$name])) {
                            $activeFieldSet =  $name;
                            $stringElement .= $this->groupElementByFieldset($this->fieldset[$name]);

                            // unset tab element within fieldset
                            //print_r($this->fieldset[$name]);die();
                            if (isset($this->fieldset[$name]['element'])) {
                                foreach ($this->fieldset[$name]['element'] as $fieldsetElement => $arrFieldsetElement) {
                                    unset($tabs['element'][$fieldsetElement]);
                                }
                            }
                        } else {
                            if (isset($this->fieldset[$activeFieldSet]['element'][$name]))
                                continue;
                        }

                        $strElement .= $stringElement;
                        unset($this->formObject[$name]);
                    }
                    $strResult .= (!$hasFieldSet) ? '<fieldset class="form-row">' . $strElement . '</fieldset>' : $strElement;
                }
                $strResult .= '</div>';
                $counter++;
            }
            $strResult .= '</div></div>';
            $this->formObject[$id]['string'] .= $strResult;
        } else {
            foreach ($this->fieldset as $id => $fieldset) {
                $numOfColumn = ($fieldset['column'] > 3) ? 3 : $fieldset['column'];
                $elementCount = (isset($fieldset['element'])) ? count($fieldset['element']) : 0;
                $elementPerRow = ceil($elementCount / $numOfColumn);
                $maxGridCount = 12;
                $rowGridLength = ($maxGridCount / $numOfColumn);

                $counter = 0;

                $strResult = '';
                if (isset($fieldset['element'])) {
                    foreach ($fieldset['element'] as $name => $stringElement) {
                        $counter++;
                        if ($counter == 1) {
                            // if (strtolower($this->formStyle) == 'inline') {
                            $strResult .= '<div class="form-row col-sm-' . $rowGridLength . ' no-padding">';
                            // }
                        }
                        $strResult .= $stringElement;

                        if ($counter == $elementPerRow) {
                            // if (strtolower($this->formStyle) == 'inline') {
                            $strResult .= '</div>';
                            // }
                            $counter = 0;
                        }
                        unset($this->formObject[$name]);
                    }
                }
                $this->formObject[$id]['string'] .= $strResult . $this->form_fieldset_close();
            }
        }

        $strObj = '';
        $strObj .= '<div class="form-row col-sm-12 no-padding">';
        foreach ($this->formObject as $idObject => $arrObj) {
            $strObj .= ' ' . $arrObj['string'];
        }
        $strObj .= '</div>';

        $strResult = $strObj;
        return $strResult;
    }

    private function groupElementByFieldset($fieldset)
    {
        //foreach ($arrFieldset AS $id => $fieldset) {
        $numOfColumn = ($fieldset['column'] > 3) ? 3 : $fieldset['column'];
        $elementCount = (isset($fieldset['element'])) ? count($fieldset['element']) : 0;
        $elementPerRow = ceil($elementCount / $numOfColumn);
        $maxGridCount = 12;
        $rowGridLength = ($maxGridCount / $numOfColumn);

        $counter = 0;

        $strResult = '';
        if (isset($fieldset['element'])) {
            foreach ($fieldset['element'] as $name => $stringElement) {
                $counter++;
                if ($counter == 1) $strResult .= '<div class="form-row col-sm-' . $rowGridLength . '">';

                $strResult .= $stringElement;

                if ($counter == $elementPerRow) {
                    $strResult .= '</div>';
                    $counter = 0;
                }
                unset($this->formObject[$name]);
            }
        }
        return $strResult . $this->form_fieldset_close();
        //}
    }

    private function renderFormButton()
    {
        $strResult = '';

        if (!empty($this->formActionButton)) {
            $strResult .= "
        <div class=\"form-footer panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row mt-3\">
          <div class=\"w-100 mt-3\">
            ";

            $strResult .= implode(' ', $this->formActionButton);

            $strResult .= "
          </div>
        </div>";
        }

        return $strResult;
    }

    private function renderHeadingButton()
    {
        $strResult = '';

        if ((empty($this->headingActionButton)) && ($this->hasNavigationHeader)) {
            $this->addHeadingButton('button', 'form-nav-action-save', '<i class="fa  fa-save"></i> Simpan', array('class' => 'form-nav-action-header', 'action-request-type' => 'save', 'title' => 'F10'));
            $this->addHeadingButton('button', 'form-nav-action-delete', '<i class="fa  fa-trash-o"></i> Hapus', array('class' => 'form-nav-action-header', 'action-request-type' => 'delete', 'title' => 'F7'));
            $this->addHeadingButton('button', 'form-nav-action-search', '<i class="fa  fa-search"></i> Cari', array('class' => 'form-nav-action-header', 'action-request-type' => 'search', 'title' => 'F4', 'action-search-url' => site_url($this->navigationHeaderListUrl)));
            $this->addHeadingButton('button', 'form-nav-action-edit', '<i class="fal fa-edit"></i> Ubah', array('class' => 'form-nav-action-header', 'action-request-type' => 'edit', 'title' => 'F11'));
            $this->addHeadingButton('button', 'form-nav-action-new', '<i class="fal fa-file-o"></i> Buat Baru', array('class' => 'form-nav-action-header', 'action-request-type' => 'new', 'title' => 'F9'));
            $this->addHeadingButton('button', 'form-nav-last', 'Akhir <i class="fal fa-forward"></i>', array('class' => 'form-nav-header', 'action-request-type' => 'last'));
            $this->addHeadingButton('button', 'form-nav-next', '<i class="fal fa-chevron-right"></i>', array('class' => 'form-nav-header', 'action-request-type' => 'next'));
            $this->addHeadingButton('button', 'form-nav-prev', '<i class="fal fa-chevron-left"></i>', array('class' => 'form-nav-header', 'action-request-type' => 'prev'));
            $this->addHeadingButton('button', 'form-nav-first', '<i class="fal fa-backward"></i> Awal', array('class' => 'form-nav-header', 'action-request-type' => 'first', 'title' => 'Go to Last Transaction'));
        }

        if (!empty($this->headingActionButton)) {
            foreach ($this->headingActionButton as $btn) {
                $strResult .= '<div class="widget-toolbar" role="menu">';
                $strResult .= $btn;
                $strResult .= '</div>';
            }
        }

        return $strResult;
    }

    // load component js
    private function generateJSString()
    {
        $strJSstring = '<link rel="stylesheet" href="' . base_url() . 'assets/Form/form.css" media="screen" />';
        $strJSstring .= '<script type="text/javascript" src="' . base_url() . 'assets/Form/form.js"></script>';
        $filler = "\n                            ";
        $strJSstring .= $filler . '<script type="text/javascript">';
        //added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
        $strJSstring .= $filler . "  jQuery.ajaxSetup({cache:true});";
        $strJSstring .= $filler . "  var doSubmit_" . $this->formID . " = function(name, serverAction)
                              {
                                $('input[name=submitButton_" . $this->formID . "]').val(name);
                                $('input[name=submitAction_" . $this->formID . "]').val(serverAction);

                                var form = $('#" . $this->formID . "');
                                if (form.length) {
                                  form.addClass('was-validated');
                                  if (form[0].checkValidity() === false)
                                  {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return;
                                  }
                                }
                              };";
        //end---added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
        $strJSstring .= $filler . '  $(document).ready(function() {';
        $strJSstring .= $this->generateValidationJS();

        if (!empty($this->arrDateElement) || !empty($this->arrTimeElement) || !empty($this->arrAutoCompleteElement)) {
            /*$strJSstring .= "
        if (typeof jQuery.ui == 'undefined') {
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = '".base_url()."assets/js/jquery-ui-1.10.3.min.js';
          jQuery('head').append(script);
        }";

      $strJSstring .= "
        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '".base_url()."assets/css/smartadmin/smartadmin-production-plugins.min.css';
        jQuery('head').append(style);
        ";*/
        }

        if (!empty($this->arrSelectElement)) {
            //$strJSstring .= $this->_generateLoadJSString(base_url().'assets/js/plugin/select2/select2.min.js');
        }

        if (!empty($this->arrEditorElement)) {
            $strJSstring .= "
        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '" . base_url() . "assets/4.5.1/css/formplugins/summernote/summernote.css';
        jQuery('head').append(style);";

            $strJSstring .= "
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '" . base_url() . "assets/4.5.1/js/formplugins/summernote/summernote.js';
        jQuery('head').append(script);";
        }

        // foreach ($this->arrDateElement AS $elDate) {

        //   $arrAdditionalOption = array();
        //   /*if (isset($elDate['datePickerOption'])) {
        //     if (!isset($elDate['datePickerOption']['showOn'])) $elDate['datePickerOption']['showOn'] = "'key'";
        //     foreach ($elDate['datePickerOption'] AS $key => $val) {
        //       $arrAdditionalOption[] = $key .":". $val;
        //     }
        //   }*/

        //     $strJSstring .= "
        //     jQuery('#".$elDate['name']."_".$this->formID."').daterangepicker({
        //       singleDatePicker: true,
        //       showDropdowns: true,
        //       timePicker: true,
        //       timePicker24Hour: true,
        //       autoApply: false,
        //       autoUpdateInput: false,
        //       timePickerSeconds: true,
        //       drops: 'up',
        //       // startDate: start,
        //       ".implode(',', $arrAdditionalOption)."
        //     }, function cb(start, end) {
        //       jQuery('#".$elDate['name']."_".$this->formID."').val(start.format('".$this->datetimeFormat."'));
        //     });";

        //   // $strJSstring .= "
        //   //   jQuery('#".$elDate['name']."_".$this->formID."').datepicker({
        //   //     format: '".$this->dateFormat."',
        //   //     autoclose: true,
        //   //     keyboardNavigation : true ,
        //   //     // yearRange: 'c-50:c+10',
        //   //     // changeMonth: true,
        //   //     // changeYear: true,
        //   //     // prevText: '<i class=\"fal fa-chevron-left\"></i>',
        //   //     // nextText: '<i class=\"fal fa-chevron-right\"></i>',
        //   //     ".implode(',', $arrAdditionalOption)."
        //   //   });

        //   //     ";
        // }


        foreach ($this->arrDateTimeElement as $elDate) {

            $arrAdditionalOption = array();
            if (isset($elDate['dateRangeOption'])) {
                foreach ($elDate['dateRangeOption'] as $key => $val) {
                    $arrAdditionalOption[] = $key . ": " . $val;
                }
            }


            $strJSstring .= "
        jQuery('#" . $elDate['name'] . "_" . $this->formID . "').daterangepicker({
          ranges: {\n";
            foreach ($this->daterangeOption as $rangeKey => $rangeVal) {
                $strJSstring .= "           '" . $rangeKey . "': " . $rangeVal . ",\n";
            }
            $strJSstring .= "},\n";
            $strJSstring .= implode(',', $arrAdditionalOption) . "
        }, function cb(start, end) {
          jQuery('#" . $elDate['name'] . "_" . $this->formID . "').val(start.format('" . $elDate['format'] . "'));
        });";
        }

        // print_r($this->arrDateRangeElement);die();
        foreach ($this->arrDateRangeElement as $elDate) {

            $arrAdditionalOption = array();
            if (isset($elDate['dateRangeOption'])) {
                foreach ($elDate['dateRangeOption'] as $key => $val) {
                    $arrAdditionalOption[] = $key . ": " . $val;
                }
            }
            $initValueDate = '';
            if (isset($elDate['value']) && $elDate['value'] != '') {
                if (strtolower($elDate['value']) == 'today') {
                    $initValueDate .= "startDate: moment().startOf('day'),";
                    $initValueDate .= "endDate: moment().endOf('day'),";
                } else if (strtolower($elDate['value']) == 'yesterday') {
                    $initValueDate .= "startDate: moment().subtract(1, 'days').startOf('day'),";
                    $initValueDate .= "endDate: moment().subtract(1, 'days').endOf('day'),";
                } else if (stripos($elDate['value'], 'last 7 day') !== false) {
                    $initValueDate .= "startDate: moment().subtract(6, 'days').startOf('day'),";
                    $initValueDate .= "endDate: moment(),";
                } else if (stripos($elDate['value'], 'last 30 day') !== false) {
                    $initValueDate .= "startDate: moment().subtract(29, 'days').startOf('day'),";
                    $initValueDate .= "endDate: moment(),";
                } else if (stripos($elDate['value'], 'this month') !== false) {
                    $initValueDate .= "startDate: moment().startOf('month').startOf('day'),";
                    $initValueDate .= "endDate: moment().endOf('month'),";
                } else if (stripos($elDate['value'], 'last month') !== false) {
                    $initValueDate .= "startDate: moment().subtract(1, 'month').startOf('month').startOf('day'),";
                    $initValueDate .= "endDate: moment().subtract(1, 'month').endOf('month').endOf('day'),";
                }
            }
            //echo "val=".$initValueDate;

            $strJSstring .= "
      jQuery('#" . $elDate['name'] . "_" . $this->formID . "').daterangepicker({" . $initValueDate . "
      ranges: {\n";
            foreach ($this->datetimerangeOption as $rangeKey => $rangeVal) {
                $strJSstring .= "         '" . $rangeKey . "': " . $rangeVal . ",\n";
            }
            $strJSstring .= "        },
        " . implode(",\n        ", $arrAdditionalOption) . "
      },  
      function(start, end) {
      });\n\n
      
      tempPicker = $('#" . $elDate['name'] . "_" . $this->formID . "').data('daterangepicker');";
            if (isset($elDate['value']) && $elDate['value'] != '') {
                $arrDateRange = explode(' - ', $elDate['value']);
                $strJSstring .= "tempPicker.setStartDate('" . $arrDateRange[0] . "');";
                if (count($arrDateRange) == 2) {
                    $strJSstring .= "tempPicker.setEndDate('" . $arrDateRange[1] . "');";
                } else {
                    $strJSstring .= "tempPicker.setEndDate('" . $arrDateRange[0] . "');";
                }
            }

            $strJSstring .= "if (tempPicker.startDate.isSame(tempPicker.endDate, 'day')) {
            $('#" . $elDate['name'] . "_" . $this->formID . "').val(tempPicker.startDate.format('" . $elDate['format'] . "'));
        } else {
            $('#" . $elDate['name'] . "_" . $this->formID . "').val(tempPicker.startDate.format('" . $elDate['format'] . "') + ' - ' + tempPicker.endDate.format('" . $elDate['format'] . "'));
        }";


            $strJSstring .= "$('#" . $elDate['name'] . "_" . $this->formID . "').on('apply.daterangepicker', function(ev, picker) {
        if (picker.startDate.isSame(picker.endDate, 'day')) {
            // Single date
            $(this).val(picker.startDate.format('" . $elDate['format'] . "'));
        } else {
            // Date range
            $(this).val(
                picker.startDate.format('" . $elDate['format'] . "') + ' - ' + picker.endDate.format('" . $elDate['format'] . "')
            );
        }
    });
      
      ";
        }

        if (count($this->arrTimeElement) > 0) {
            $strJSstring .= "      function runTimePicker() { ";
            foreach ($this->arrTimeElement as $elTime) {
                $strJSstring .= "$('#" . $elTime . "_" . $this->formID . "').timepicker({ timeFormat: 'HH:mm:ss', showMeridian: false, showSeconds: true});";
            }
            $strJSstring .= " }; ";

            $strJSstring .= " runTimePicker(); ";
        }

        foreach ($this->arrAutoCompleteElement as $elAutoComplete => $rowAutoComplete) {
            $strGroupingAuJS = '
          _renderItem: function( ul, item ) {
            return $( "<li>" )
              .append( "<a>" + item.value + " : " + item.label + "</a>" )
              .appendTo( ul );
          }
        ';
            if (!empty($rowAutoComplete['groupByField'])) {
                $strGroupingAuJS = '
          _renderMenu: function( ul, items ) {
            var that = this,
              currentCategory = "";
            $.each( items, function( index, item ) {
              var li;
              if ( item.' . $rowAutoComplete['groupByField'] . ' != currentCategory ) {
                ul.append( "<li class=\'ui-autocomplete-category\'>" + item.' . $rowAutoComplete['groupByField'] . ' + "</li>" );
                currentCategory = item.' . $rowAutoComplete['groupByField'] . ';
              }
              li = that._renderItemData( ul, item );
              if ( item.' . $rowAutoComplete['groupByField'] . ' ) {
                li.attr( "aria-label", item.' . $rowAutoComplete['groupByField'] . ' + " : " + item.label );
              }
            });
          }
        ';
            }

            $strJSstring .= '
        $.widget( "custom.autocomplete", $.ui.autocomplete, {
          _create: function() {
            this._super();
            this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
          },' . $strGroupingAuJS . '
        });';

            $strJSstring .= $rowAutoComplete['sourceString'];

            $strJSstring .= '
        $( "#' . $elAutoComplete . "_" . $this->formID . '" ).autocomplete({
          minLength: 0,
          source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
            response( $.grep( ' . $rowAutoComplete['source'] . ', function( data ) {
              return (matcher.test( data.label ) || matcher.test( data.value ) || matcher.test( data ));
            }) );
          },
          focus: function( event, ui ) {
            $( "#' . $elAutoComplete . '" ).val( ui.item.value );
            return false;
          },
          select: function( event, ui ) {
            $( "#label_' . $elAutoComplete . '" ).html( ui.item.label );
            $( "#' . $elAutoComplete . '" ).val( ui.item.value );
          }
        });';
        }

        foreach ($this->arrSelectElement as $name => $rowProp) {
            if ($rowProp['hasEmptyOption']) {
                //$strJSstring .= ' $("#'.$name."_".$this->formID.' option:first-child").remove();';
                $strJSstring .= ' $("#' . $name . "_" . $this->formID . '").prepend("<option></option>");';
            }

            //$strJSstring .= '$("#'.$name."_".$this->formID.'").select2({allowClear:true, placeholder: "'.$rowProp['placeHolder'].'"});';

            $strJSstringAfter = '';
            if (!empty($rowProp['htmlAfter']))
                $strJSstringAfter = '$("#input-group-' . $name . '-' . $this->formID . '").append("' . str_replace('"', "'", $rowProp['htmlAfter']) . '");';

            $strResult = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strJSstringAfter);
            $strResult = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strResult);

            $strJSstring .= $strResult;
        }

        foreach ($this->arrEditorElement as $elDate => $arrEditor) {
            $strJSstring .= "jQuery('#" . $arrEditor['id'] . "').summernote({height: 200});";

            if (isset($arrEditor['code']))
                $strJSstring .= "jQuery('#" . $arrEditor['id'] . "').summernote('code', '" . $arrEditor['code'] . "');";
        }

        if (session()->getFlashdata($this->formID . '_success_message') != '') {
            $strJSstring .= "jQuery('#" . $this->formID . "_success_alert').append('" . session()->getFlashdata($this->formID . '_success_message') . "');";
            $strJSstring .= "jQuery('#" . $this->formID . "_success_alert').show();";
        } else if (session()->getFlashdata($this->formID . '_error_message') != '') {
            $strJSstring .= "jQuery('#" . $this->formID . "_error_alert').append('" . session()->getFlashdata($this->formID . '_error_message') . "');";
            $strJSstring .= "jQuery('#" . $this->formID . "_error_alert').show();";
        }

        //javascript untuk navigation header
        if ($this->hasNavigationHeader) {
            $strJSstring .= "
          jQuery(document).keydown(function(e) {
            if (jQuery('#content').hasClass('in-use')) {
              var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
              //F9
              if (key==120) {
                jQuery('#form-nav-action-new_" . $this->formID . "').click();
              }          
              //F11
              if (key ==122) {
                e.preventDefault();
                jQuery('#form-nav-action-edit_" . $this->formID . "').click();
              }
              //F4
              if (key ==115) {
                e.preventDefault();
                jQuery('#form-nav-action-search_" . $this->formID . "').click();
              }
              //F7
              if (key ==118) {
                e.preventDefault();
                jQuery('#form-nav-action-delete_" . $this->formID . "').click();
              }
              //F10
              if (key==121) {
                e.preventDefault();
                jQuery('#form-nav-action-save_" . $this->formID . "').click();                
              }
              //F8
              if (key ==119) {
                e.preventDefault();
                jQuery('#form-nav-last_" . $this->formID . "').click();
              }
            }

          });        
        ";
        }

        $strJSstring .= $filler . '  });';

        // javascript untuk detail form input
        if (!empty($this->formDetail)) {
            $strInitJSAfterAdd = '';
            $strInitJSAfterDelete = '';

            foreach ($this->formDetail as $detailName => $detailProp) {
                if (isset($this->formDetailNewRow[$detailName])) {
                    $strJSstring .= 'var dhtml' . $detailName . $this->formID . ' = "' . addcslashes(str_replace(array("\r", "\n"), '', trim($this->formDetailNewRow[$detailName])), '"\\/') . '";';
                }

                if (!empty($detailProp['strJSAfterAdd'])) {
                    $strInitJSAfterAdd = implode(';', $detailProp['strJSAfterAdd']);
                }
                if (!empty($detailProp['strJSAfterDelete'])) {
                    $strInitJSAfterDelete = implode(';', $detailProp['strJSAfterDelete']);
                }
            }

            $strJSstring .= "
        function addRowDetail (formName, detailName) {
          var numShow = $('#numShow_'+detailName).val();
          numShow = parseInt(numShow) + 1;

          var tableId = 'table_detail_'+detailName+'_'+formName;
          var strNewRow = eval('dhtml'+detailName+formName).replace(/{{counter}}/gi, numShow);
          $('#'+tableId+' tbody').append(strNewRow);

          $('#counterSequence_'+detailName+'_'+numShow).html(numShow);
          $('#numShow_'+detailName).val(numShow);
          " . $strInitJSAfterAdd . "        
        };

        function deleteRowDetail(formName, detailName, idx) {
          var numShow = $('#numShow_'+detailName).val();

          $('#isDeleted_'+detailName+'_'+idx+'_'+formName).val(1);
          $('#rowDetail_'+detailName+'_'+formName+'_'+idx).hide(\"slow\");
          " . $strInitJSAfterDelete . " 
        }
      ";
        }

        $strJSstring .= $filler . '</script>';
        return $strJSstring;
    }

    // form validation js
    private function generateValidationJS()
    {
        // $strResult = $this->_generateLoadJSString(base_url().'assets/js/jquery.validate.min.js');

        // $strResult .= "

        //     if (typeof alertify === 'undefined') {
        //       var script = document.createElement('script');
        //       script.type = 'text/javascript';
        //       script.src = '".base_url()."assets/js/alertify.min.js';
        //       jQuery('head').append(script);

        //       var style = document.createElement('link');
        //       style.rel = 'stylesheet';
        //       style.type = 'text/css';
        //       style.href = '".base_url()."assets/css/alertify/alertify.min.css';
        //       jQuery('head').append(style);

        //       var style = document.createElement('link');
        //       style.rel = 'stylesheet';
        //       style.type = 'text/css';
        //       style.href = '".base_url()."assets/css/alertify/alertify.bootstrap.min.css';
        //       jQuery('head').append(style);
        //     }
        //     ";
        return "
      
";

        $strResult = "
        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '" . base_url() . "assets/Form/form.css';
        jQuery('head').append(style);

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '" . base_url() . "assets/Form/form.js';
        jQuery('head').append(script);
        ";

        $strResult = "";

        $strResult .= '$("#' . $this->formID . '").validate({
      ignore:[],
      errorClass:"has-error",
      errorClassLabel:"label label-danger",
      errorPlacement: function(error, element) {
        if ( element.attr("type") == "checkbox") {
          if (element.parent("label").hasClass("checkbox-inline"))
            element.closest("div").append(error);
          else
            element.closest("div").parent().append(error);
        } else if (element.attr("type") == "radio") {
          if (element.parent("label").hasClass("radio-inline"))
            element.closest("div").append(error);
          else
            element.closest("div").parent().append(error);
        } else {
          if (element.closest("div.input-group-validation").nextAll("span:first").length == 0)
            error.insertAfter(element.closest("div.input-group-validation"));
          else
            error.insertAfter(element.closest("div.input-group-validation").nextAll("span:first"));
        }
      },
      highlight: function(element, errorClass, validClass) {
        if ( $(element).attr("type") == "checkbox") {
          if ($(element).parent("label").hasClass("checkbox-inline")) {
            $(element).closest("div").addClass(errorClass).removeClass(validClass);
          }
          else {
            $(element).closest("div").parent().addClass(errorClass).removeClass(validClass);
          }
        } else if ( $(element).attr("type") == "radio") {
          if ($(element).parent("label").hasClass("radio-inline")) {
            $(element).closest("div").addClass(errorClass).removeClass(validClass);
          }
          else {
            $(element).closest("div").parent().addClass(errorClass).removeClass(validClass);
          }
        }else {
          $(element).closest("div.input-group-validation").addClass(errorClass).removeClass(validClass);
        }
      },
      unhighlight: function(element, errorClass, validClass) {
        if ( $(element).attr("type") == "checkbox") {
          if ($(element).parent("label").hasClass("checkbox-inline")) {
            $(element).closest("div").addClass(validClass).removeClass(errorClass);
          }
          else {
            $(element).closest("div").parent().addClass(validClass).removeClass(errorClass);
          }
        } else if ( $(element).attr("type") == "radio") {
          if ($(element).parent("label").hasClass("radio-inline")) {
            $(element).closest("div").addClass(validClass).removeClass(errorClass);
          }
          else {
            $(element).closest("div").parent().addClass(validClass).removeClass(errorClass);
          }
        }else {
          $(element).closest("div.input-group-validation").addClass(validClass).removeClass(errorClass);
        }
      },';

        $arrRules = array();
        $arrMessages = array();

        foreach ($this->validationRules as $idElement => $arrElementRules) {
            $tempRule = json_encode($arrElementRules);
            $strRule  = preg_replace('/"([a-zA-Z_]+[a-zA-Z0-9_]*)":/', '$1:', $tempRule);
            $strRule  = preg_replace('/"(function[^"]*)"/', '${1}', $strRule);
            $arrRules[] = $idElement . ' : ' . $strRule;
        }
        foreach ($this->validationMessages as $idElement => $arrElementMessages) {
            $arrMessages[] = $idElement . ' : ' . json_encode($arrElementMessages);
        }

        if (!empty($arrRules))
            $strResult .= 'rules:{' . implode(",", $arrRules) . '},';
        if (!empty($arrMessages))
            $strResult .= 'messages:{' . implode(",", $arrMessages) . '},';

        $strResult .= '
      submitHandler: function(form) {
        var moduleName = "";
        var controllerName = "";
        var methodName = "";

        var submitButton = $("input[name=submitButton_' . $this->formID . ']").val();
        var submitButtonText = $("#"+submitButton+"_' . $this->formID . '").val();';

        if (!empty($this->arrEditorElement)) {
            foreach ($this->arrEditorElement as $elDate => $arrEditor) {
                $strResult .= "jQuery('#" . $arrEditor['hiddenEl'] . "_" . $this->formID . "').val(jQuery('#" . $arrEditor['id'] . "').summernote('code'));";
            }
        }

        /*
        alertify.confirm(submitButtonText + " this data ?", function (ok) {
          if (ok) {
            var submitAction = $("input[name=submitAction_'.$this->formID.']").val();
            var actionSegment = submitAction.split("/");
            actionSegment.reverse();

            if (actionSegment.hasOwnProperty(0)) methodName = actionSegment[0];
            if (actionSegment.hasOwnProperty(1)) controllerName = actionSegment[1];
            else controllerName = "'.$this->controller_name.'";
            if (actionSegment.hasOwnProperty(2)) moduleName = actionSegment[2];
            else moduleName = "'.$this->module.'";
            var arrSubmitUrl = [moduleName, controllerName, methodName];
            var submitUrl = arrSubmitUrl.join("/");

            $("#'.$this->formID.'").attr("action", "'.base_url().'"+submitUrl);
            form.submit();
          }
        });
*/
        $strResult .= '
//         if (confirm(submitButtonText + " this data ?")) {
            var submitAction = $("input[name=submitAction_' . $this->formID . ']").val();
            var actionSegment = submitAction.split("/");
            actionSegment.reverse();

            if (actionSegment.hasOwnProperty(0)) methodName = actionSegment[0];
            if (actionSegment.hasOwnProperty(1)) controllerName = actionSegment[1];
            else controllerName = "' . $this->controller_name . '";
            if (actionSegment.hasOwnProperty(2)) moduleName = actionSegment[2];
            else moduleName = "' . $this->module . '";
            var arrSubmitUrl = [];//[moduleName, controllerName, methodName];
            if (moduleName != "") arrSubmitUrl.push(moduleName);
            if (controllerName != "") arrSubmitUrl.push(controllerName);
            if (methodName != "") arrSubmitUrl.push(methodName);
            var submitUrl = arrSubmitUrl.join("/");

            $("#' . $this->formID . '").attr("action", "' . base_url() . '"+submitUrl);
            $("#' . $this->formID . ' input[type=\'checkbox\']").each( function () {
              var checkbox_this = $(this);
              if( checkbox_this.is(":checked") == true ) {
              } else {
                $(form).append(\'<input type="hidden" name="\' + checkbox_this.attr("name") + \'" value=0 />\');
              }
            });
            form.submit();
//           }
      }
    ';
        $strResult .= '});';

        return $strResult;
    }

    private function _generateLoadJSString($jsPath)
    {
        if (empty($jsPath)) return '';
        return "

      var scriptsJSLoaded = document.getElementsByTagName('script');
      var isScriptJsLoaded = false;
      for(var i = 0; i < scriptsJSLoaded.length; i++) {
         if(scriptsJSLoaded[i].getAttribute('src') == '" . $jsPath . "')
          isScriptJsLoaded = true;
      }

      if (!isScriptJsLoaded) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '" . $jsPath . "';
        jQuery('head').append(script);
      }";
    }
}

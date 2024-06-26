<?php
/**
 * @author      Laurent Jouanneau
 * @contributor Julien Issler, Dominique Papin, Claudio Bernardes
 *
 * @copyright   2006-2024 Laurent Jouanneau
 * @copyright   2008-2011 Julien Issler, 2008 Dominique Papin
 * @copyright   2012 Claudio Bernardes
 *
 * @see         https://www.jelix.org
 * @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

namespace Jelix\Forms\HtmlWidget;

use Jelix\Locale\Locale;

abstract class WidgetBase implements WidgetInterface
{
    /**
     * The form builder.
     *
     * @var \Jelix\Forms\Builder\HtmlBuilder
     */
    protected $builder;

    /**
     * the parent widget.
     *
     * @var \Jelix\Forms\HtmlWidget\ParentWidgetInterface
     */
    protected $parentWidget;

    /**
     * The control.
     *
     * @var \Jelix\Forms\Controls\AbstractControl
     */
    protected $ctrl;

    /**
     * default html attributes for the control.
     *
     * @var array
     */
    protected $defaultAttributes = array();

    /**
     * html attributes for the control.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * html attributes for the control label.
     *
     * @var array
     */
    protected $labelAttributes = array();

    protected $valuesSeparator = ' ';

    protected $_endt = '/>';

    public function __construct($args)
    {
        $this->ctrl = $args[0];
        $this->builder = $args[1];
        $this->parentWidget = $args[2];
        $this->_endt = $this->builder->endOfTag();
    }

    /**
     * Get the control id.
     */
    public function getId()
    {
        return $this->builder->getName().'_'.$this->ctrl->ref;
    }

    /**
     * Get the control name.
     */
    public function getName()
    {
        return $this->ctrl->ref;
    }

    /**
     * Get the control class.
     */
    protected function getCSSClass()
    {
        $ro = $this->ctrl->isReadOnly();

        if (isset($this->attributes['class'])) {
            $class = $this->attributes['class'].' ';
        } else {
            $class = '';
        }

        $class .= 'jforms-ctrl-'.$this->ctrl->type;
        $class .= ($this->ctrl->required == false || $ro ? '' : ' jforms-required');
        $class .= (isset($this->builder->getForm()->getContainer()->errors[$this->ctrl->ref]) ? ' jforms-error' : '');
        $class .= ($ro && $this->ctrl->type != 'captcha' ? ' jforms-readonly' : '');

        $attrClass = $this->ctrl->getAttribute('class');
        if ($attrClass) {
            $class .= ' '.$attrClass;
        }

        return $class;
    }

    public function getValue()
    {
        return $this->builder->getForm()->getData($this->ctrl->ref);
    }

    public function setDefaultAttributes($attr)
    {
        $this->defaultAttributes = $attr;
    }

    public function setAttributes($attr)
    {
        if (isset($attr['separator'])) {
            $this->valuesSeparator = $attr['separator'];
            unset($attr['separator']);
        }
        $this->attributes = array_merge($this->defaultAttributes, $attr);
    }

    public function setLabelAttributes($attributes)
    {
        $this->labelAttributes = $attributes;
    }

    public function outputMetaContent($resp)
    { // do nothing
    }

    /**
     * Retrieve the label attributes.
     *
     * @param mixed $editMode
     */
    protected function getLabelAttributes($editMode)
    {
        $attr = $this->labelAttributes;

        $attr['hint'] = ($this->ctrl->hint == '' ? '' : ' title="'.htmlspecialchars($this->ctrl->hint).'"');
        $attr['idLabel'] = ' id="'.$this->getId().'_label"';

        if ($editMode) {
            $required = ($this->ctrl->required == false || $this->ctrl->isReadOnly() ? '' : ' jforms-required');
            $attr['reqHtml'] = ($required ? '<span class="jforms-required-star">*</span>' : '');
        } else {
            $attr['reqHtml'] = '';
        }
        if (!isset($attr['class'])) {
            $attr['class'] = '';
        } else {
            $attr['class'] .= ' ';
        }
        $attr['class'] .= 'jforms-label';
        $attr['class'] .= (isset($this->builder->getForm()->getContainer()->errors[$this->ctrl->ref]) ? ' jforms-error' : '');
        if ($editMode) {
            $attr['class'] .= ($this->ctrl->required == false || $this->ctrl->isReadOnly() ? '' : ' jforms-required');
        }

        return $attr;
    }

    /**
     * Returns an array containing all the control attributes.
     */
    protected function getControlAttributes()
    {
        $attr = $this->attributes;
        $attr['name'] = $this->getName();
        $attr['id'] = $this->getId();
        if ($this->ctrl->isReadOnly()) {
            $attr['readonly'] = 'readonly';
        } else {
            unset($attr['readonly']);
        }
        if ($this->ctrl->hint) {
            $attr['title'] = $this->ctrl->hint;
        }
        $attr['class'] = $this->getCSSClass();

        return $attr;
    }

    protected function getValueAttributes()
    {
        $attr = $this->attributes;
        $attr['id'] = $this->getId();
        $class = 'jforms-value jforms-value-'.$this->ctrl->type;
        if (isset($attr['class'])) {
            $attr['class'] .= ' '.$class;
        } else {
            $attr['class'] = $class;
        }

        return $attr;
    }

    protected function commonJs()
    {
        $jsContent = $this->commonGetJsConstraints();

        if (!$this->parentWidget->controlJsChild()) {
            $jsContent .= $this->builder->getJFormsJsVarName().".tForm.addControl(c);\n";
        }

        $this->parentWidget->addJs($jsContent);
    }

    protected function commonGetJsConstraints()
    {
        $jsContent = '';

        if ($this->ctrl->isReadOnly()) {
            $jsContent .= "c.readOnly = true;\n";
        }

        if ($this->ctrl->required) {
            $jsContent .= "c.required = true;\n";
        }

        if ($this->ctrl->alertRequired) {
            $jsContent .= 'c.errRequired='.$this->escJsStr($this->ctrl->alertRequired).";\n";
        } else {
            $jsContent .= 'c.errRequired='.$this->escJsStr(Locale::get('jelix~formserr.js.err.required', $this->ctrl->label)).";\n";
        }

        if ($this->ctrl->alertInvalid) {
            $jsContent .= 'c.errInvalid='.$this->escJsStr($this->ctrl->alertInvalid).";\n";
        } else {
            $jsContent .= 'c.errInvalid='.$this->escJsStr(Locale::get('jelix~formserr.js.err.invalid', $this->ctrl->label)).";\n";
        }

        return $jsContent;
    }

    protected function escJsStr($str)
    {
        return '\''.str_replace(array("'", "\n"), array("\\'", '\\n'), $str).'\'';
    }

    protected function _outputAttr(&$attributes)
    {
        foreach ($attributes as $name => $val) {
            echo ' '.$name.'="'.htmlspecialchars($val, ENT_COMPAT | ENT_SUBSTITUTE).'"';
        }
    }

    /**
     * This function displays the blue question mark near the form field.
     */
    public function outputHelp()
    {
        if (method_exists($this->builder, 'outputControlHelp')) {
            $this->builder->outputControlHelp($this->ctrl);
        }
        // deprecated. only for compatibility of plugins for jelix 1.6
        elseif ($this->ctrl->help) {
            // additionnal &nbsp, else background icon is not shown in webkit
            echo '<span class="jforms-help" id="'.$this->getId().'-help">&nbsp;<span>'.htmlspecialchars($this->ctrl->help, ENT_COMPAT | ENT_SUBSTITUTE).'</span></span>';
        }
    }

    /**
     * This function displays the form field label.
     *
     * @param mixed $format
     * @param mixed $editMode
     */
    public function outputLabel($format = '', $editMode = true)
    {
        $ctrl = $this->ctrl;
        $attr = $this->getLabelAttributes($editMode);
        if ($format) {
            $label = sprintf($format, $this->ctrl->label);
        } else {
            $label = $this->ctrl->label;
        }

        if ($ctrl->type == 'output' || $ctrl->type == 'checkboxes'
            || $ctrl->type == 'radiobuttons' || $ctrl->type == 'date'
            || $ctrl->type == 'datetime' || $ctrl->type == 'time'
            || $ctrl->type == 'choice'
        ) {
            $this->outputLabelAsTitle($label, $attr);
        } elseif ($ctrl->type != 'submit' && $ctrl->type != 'reset') {
            $this->outputLabelAsFormLabel($label, $attr);
        }
    }

    protected function outputLabelAsFormLabel($label, $attr)
    {
        echo '<label class="',$attr['class'],'" for="',$this->getId(),'"',$attr['idLabel'],$attr['hint'],'>';
        echo htmlspecialchars($label, ENT_COMPAT | ENT_SUBSTITUTE), $attr['reqHtml'];
        echo "</label>\n";
    }

    protected function outputLabelAsTitle($label, $attr)
    {
        echo '<span class="',$attr['class'],'"',$attr['idLabel'],$attr['hint'],'>';
        echo htmlspecialchars($label, ENT_COMPAT | ENT_SUBSTITUTE), $attr['reqHtml'];
        echo "</span>\n";
    }

    abstract public function outputControl();

    public function outputControlValue()
    {
        $attr = $this->getValueAttributes();
        echo '<span ';
        $this->_outputAttr($attr);
        echo '>';
        $value = $this->getValue();
        $value = $this->ctrl->getDisplayValue($value);
        if (is_array($value)) {
            $s = '';
            foreach ($value as $v) {
                $s .= $this->valuesSeparator.htmlspecialchars($v, ENT_COMPAT | ENT_SUBSTITUTE);
            }
            echo substr($s, strlen($this->valuesSeparator));
        } elseif ($this->ctrl->isHtmlContent()) {
            echo $value;
        } else if ($value !== null) {
            echo htmlspecialchars($value, ENT_COMPAT | ENT_SUBSTITUTE);
        }
        echo '</span>';
    }

    public function outputControlRawValue()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $s = '';
            foreach ($value as $v) {
                $s .= $this->valuesSeparator.htmlspecialchars($v);
            }
            echo substr($s, strlen($this->valuesSeparator));
        } elseif ($this->ctrl->type == 'password') {
            echo "*****";
        } else if ($value !== null) {
            echo htmlspecialchars($value);
        }
    }

    protected function fillSelect($ctrl, $value)
    {
        $data = $ctrl->datasource->getData($this->builder->getForm());
        if (($this->ctrl->datasource instanceof \Jelix\Forms\Datasource\DatasourceInterface
                || $this->ctrl->datasource instanceof \jIFormsDatasource2)
            && $ctrl->datasource->hasGroupedData()
        ) {
            if (isset($data[''])) {
                foreach ($data[''] as $v => $label) {
                    if (is_array($value)) {
                        $selected = in_array((string) $v, $value, true);
                    } else {
                        $selected = ((string) $v === $value);
                    }
                    echo '<option value="',htmlspecialchars($v),'"',($selected ? ' selected="selected"' : ''),'>',htmlspecialchars($label),"</option>\n";
                }
            }
            foreach ($data as $group => $values) {
                if ($group === '') {
                    continue;
                }
                echo '<optgroup label="'.htmlspecialchars($group).'">';
                foreach ($values as $v => $label) {
                    if (is_array($value)) {
                        $selected = in_array((string) $v, $value, true);
                    } else {
                        $selected = ((string) $v === $value);
                    }
                    echo '<option value="',htmlspecialchars($v),'"',($selected ? ' selected="selected"' : ''),'>',htmlspecialchars($label),"</option>\n";
                }
                echo '</optgroup>';
            }
        } else {
            foreach ($data as $v => $label) {
                if (is_array($value)) {
                    $selected = in_array((string) $v, $value, true);
                } else {
                    $selected = ((string) $v === $value);
                }
                echo '<option value="',htmlspecialchars($v),'"',($selected ? ' selected="selected"' : ''),'>',htmlspecialchars($label),"</option>\n";
            }
        }
    }
}

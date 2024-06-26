<?php
/**
 * @author      Laurent Jouanneau
 * @contributor Loic Mathaud, Dominique Papin, Claudio Bernardes
 *
 * @copyright   2006-2024 Laurent Jouanneau, 2007 Dominique Papin
 * @copyright   2007 Loic Mathaud, 2012 Claudio Bernardes
 *
 * @see         https://www.jelix.org
 * @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

namespace Jelix\Forms\Builder;

use Jelix\Core\App;
use Jelix\Forms\FormInstance;

/**
 * base class of all builder form classes generated by the jform compiler.
 *
 * a builder form class is a class which help to generate a form for the output
 * (html form for example)
 *
 * @package     jelix
 * @subpackage  forms
 */
abstract class BuilderBase
{
    /**
     * a form object.
     *
     * @var FormInstance
     */
    protected $_form;

    /**
     * the action selector.
     *
     * @var string
     */
    protected $_action;

    /**
     * params for the action.
     *
     * @var array
     */
    protected $_actionParams = array();

    /**
     * form name.
     */
    protected $_name;

    /**
     * @var array options for the builder
     */
    protected $options = array();

    protected $_endt = '/>';

    /**
     * @param FormInstance $form a form object
     */
    public function __construct($form)
    {
        $this->_form = $form;
    }

    /**
     * @param string $action       action selector where form will be submitted
     * @param array  $actionParams parameters for the action
     */
    public function setAction(string $action, array $actionParams)
    {
        $this->_action = $action;
        $this->_actionParams = $actionParams;
        $this->_name = self::generateFormName($this->_form->getSelector());
        if (App::router()->response != null && App::router()->response->getType() == 'html') {
            $this->_endt = (App::router()->response->isXhtml() ? '/>' : '>');
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getForm()
    {
        return $this->_form;
    }

    public function endOfTag()
    {
        return $this->_endt;
    }

    /**
     * set options.
     *
     * @param array $options associative array
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param string $name name of an option
     *
     * @return mixed the value of the option
     */
    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * called during the meta content processing in templates
     * This method should set things on the response, like adding
     * css styles, javascript links etc.
     *
     * @param \jTpl $tpl the template object
     */
    abstract public function outputMetaContent($tpl);

    /**
     * output the header content of the form.
     */
    abstract public function outputHeader();

    /**
     * output the footer content of the form.
     */
    abstract public function outputFooter();

    /**
     * displays all the form. outputMetaContent, outputHeader and outputFooters are also called.
     *
     * @since 1.1
     */
    abstract public function outputAllControls();


    /**
     * displays all data of the form.
     *
     * @since 1.8
     */
    abstract public function outputAllControlsValues();

    /**
     * displays the content corresponding of the given control.
     *
     * @param \Jelix\Forms\Controls\AbstractControl $ctrl       the control to display
     * @param array          $attributes attribute to add on the generated code (html attributes for example)
     */
    abstract public function outputControl($ctrl, $attributes = array());

    /**
     * displays the label corresponding of the given control.
     *
     * @param \Jelix\Forms\Controls\AbstractControl $ctrl     the control to display
     * @param mixed          $format
     * @param mixed          $editMode
     */
    abstract public function outputControlLabel($ctrl, $format = '', $editMode = true);

    /**
     * displays the value of the control (without the control).
     *
     * @param \Jelix\Forms\Controls\AbstractControl $ctrl       the control to display
     * @param array          $attributes attribute to add on the generated code (html attributes for example)
     */
    abstract public function outputControlValue($ctrl, $attributes = array());

    /**
     * displays the raw value of the control (without the control).
     *
     * @param \Jelix\Forms\Controls\AbstractControl $ctrl       the control to display
     * @param array          $attributes attribute to add on the generated code (html attributes for example)
     */
    abstract public function outputControlRawValue($ctrl, $attributes = array());

    /**
     * generates a name for the form.
     *
     * @param mixed $sel
     */
    protected static function generateFormName($sel)
    {
        static $forms = array();
        $name = 'jforms_'.str_replace('~', '_', $sel);
        if (isset($forms[$sel])) {
            return $name.(++$forms[$sel]);
        }
        $forms[$sel] = 0;

        return $name;
    }
}

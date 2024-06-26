<?php
/**
 *
 * @author      Julien Issler
 * @contributor Thomas, Zeffyr, Laurent Jouanneau
 *
 * @copyright   2008 Julien Issler, 2009 Thomas, 2010 Zeffyr, 2013-2024 Laurent Jouanneau
 *
 * @see        https://www.jelix.org
 * @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
namespace Jelix\Forms\Controls;

/**
 */
class DateControl extends AbstractControl
{
    public $type = 'date';
    public $datepickerConfig = '';

    public function __construct($ref)
    {
        parent::__construct($ref);
        $this->datatype = new \jDatatypeDate();
    }

    public function setValueFromRequest($request)
    {
        $value = $request->getParam($this->ref, '');
        if (is_array($value)) {
            $value = $value['year'].'-'.$value['month'].'-'.$value['day'];
        }
        if ($value == '--') {
            $value = '';
        }
        $this->setData($value);
    }

    public function getDisplayValue($value)
    {
        if ($value != '') {
            $dt = new \jDateTime();
            $dt->setFromString($value, \jDateTime::DB_DFORMAT);
            $value = $dt->toString(\jDateTime::LANG_DFORMAT);
        } elseif ($this->emptyValueLabel !== null) {
            return $this->emptyValueLabel;
        }

        return $value;
    }
}

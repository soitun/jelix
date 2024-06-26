<?php
/**
 *
 * @author      Julien Issler
 * @contributor Thomas, Zeffyr, Michgeek
 *
 * @copyright   2008 Julien Issler, 2009 Thomas, 2010 Zeffyr, 2012 Michgeek
 *
 * @see        https://www.jelix.org
 * @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
namespace Jelix\Forms\Controls;

/**
 */
class DatetimeControl extends DateControl
{
    public $type = 'datetime';
    public $enableSeconds = false;

    public function __construct($ref)
    {
        parent::__construct($ref);
        $this->datatype = new \jDatatypeDateTime();
    }

    public function setValueFromRequest($request)
    {
        $value = $request->getParam($this->ref, '');
        $this->setData($value);
        if (is_array($value)) {
            if ($value['year'] === '' && $value['month'] === '' && $value['day'] === '' && $value['hour'] === '' && $value['minutes'] === '' && (!$this->enableSeconds || $value['seconds'] === '')) {
                $this->setData('');
            } else {
                if ($value['seconds'] === '') {
                    $value['seconds'] = '00';
                }
                $this->setData($value['year'].'-'.$value['month'].'-'.$value['day'].' '.$value['hour'].':'.$value['minutes'].':'.$value['seconds']);
            }
        }
    }

    public function getDisplayValue($value)
    {
        if ($value != '') {
            $dt = new \jDateTime();
            $dt->setFromString($value, \jDateTime::DB_DTFORMAT);
            $value = $dt->toString(\jDateTime::LANG_DTFORMAT);
        } elseif ($this->emptyValueLabel !== null) {
            return $this->emptyValueLabel;
        }

        return $value;
    }
}

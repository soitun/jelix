<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2005-2018 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

namespace Jelix\Routing\UrlMapping;

use Jelix\Core\App;

/**
 * a specific selector for the xml files which contains the configuration of the UrlMapper.
 */
class SelectorUrlXmlMap extends \jSelectorSimpleFile
{
    public $type = 'urlxmlmap';

    public $localFile = '';

    protected $_localPath = '';

    protected $_localBasePath = '';

    /**
     * SelectorUrlXmlMap constructor.
     *
     * @param string $selInApp
     * @param string $selInVar
     *
     * @throws \jExceptionSelector
     */
    public function __construct($selInApp, $selInVar = '')
    {
        $this->_basePath = App::appSystemPath();
        $this->_localBasePath = App::varConfigPath();

        parent::__construct($selInApp);

        if (preg_match('/^([\\w\\.\\/]+)$/', $selInVar, $m)) {
            $this->localFile = $m[1];
            $this->_localPath = $this->_localBasePath.$m[1];
        } elseif ($selInVar != '') {
            throw new \jExceptionSelector(
                'jelix~errors.selector.invalid.syntax',
                array($selInVar, $this->type)
            );
        }
    }

    public function getCompiledFilePath()
    {
        return App::buildPath('router/'.$this->file.'.creationinfos_20.php');
    }

    public function getCompiledEntrypointFilePath($entryPointName)
    {
        return App::buildPath('router/'.$this->file.'.'.rawurlencode($entryPointName).'.entrypoint.php');
    }

    public function getLocalPath()
    {
        return $this->_localPath;
    }
}

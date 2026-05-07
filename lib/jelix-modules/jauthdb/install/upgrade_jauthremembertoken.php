<?php

/**
 * @package     jelix
 * @subpackage  jauthdb
 *
 * @author      Laurent Jouanneau
 * @copyright   2026 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

use Jelix\Installer\Module\Installer;
use Jelix\Installer\Module\API\InstallHelpers;

class jauthdbModuleUpgrader_jauthremembertoken extends Installer
{
    public $targetVersions = array('1.8.24-a1');
    public $date = '2026-05-06 16:30';

    public function install(InstallHelpers $helpers)
    {
        $helpers->getLiveConfigIni()->removeValue('persistant_encryption_key', 'coordplugin_auth');

        $confList = array();
        foreach ($helpers->getEntryPointsList() as $entryPoint) {
            $authConfig = $entryPoint->getCoordPluginConfig('auth');
            if (!$authConfig) {
                continue;
            }
            /** @var \Jelix\IniFile\IniModifier $conf */
            list($conf, $section) = $authConfig;

            $path = Jelix\FileUtilities\Path::shortestPath(jApp::appPath(), $conf->getFileName());
            if (!isset($confList[$path])) {
                $confList[$path] = true;
                $this->setupAuth($helpers, $conf, $section, $entryPoint->getConfigObj());
            }
        }
    }

    protected function setupAuth(InstallHelpers $helpers, Jelix\IniFile\IniModifier $conf, $section_auth, $epConfig)
    {
        // load the configuration of jAuth
        $configContent = $conf->getValues($section_auth);
        if ($section_auth === 0) {
            foreach($conf->getSectionList() as $section) {
                $configContent[$section] = $conf->getValues($section);
            }
        }

        $authConfig = jAuth::loadConfig($configContent, $epConfig);
        $driver = $authConfig['driver'];
        $driverConfig = $authConfig[$driver];

        $profile = (isset($driverConfig['profile']) ? $driverConfig['profile']: '');
        $dbConn = $helpers->database();
        $dbConn->useDbProfile($profile);
        // the script is into the jelix module, because the jauthdb module may not be installed and
        // replaced by another one, for example jcommunity.
        $dbConn->execSQLScript('sql/install_jauthremembertoken.schema', 'jelix');
    }
}

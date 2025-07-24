<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2018-2025 Laurent Jouanneau
 *
 * @see         https://www.jelix.org
 * @licence     MIT
 */

namespace Jelix\Scripts;

use Jelix\Core\Config\AppConfig;
use Jelix\Routing\Router;
use Symfony\Component\Console\Application;
use Jelix\Core\App;
use Jelix\Core\Config\Compiler;

/**
 * Launch commands from modules.
 */
class ModulesCommands
{
    public static function run()
    {
        Utils::checkEnv();

        // init Jelix environment

        App::setEnv('console');

        Utils::checkTempPath();

        $fmkInfos = \Jelix\Core\Infos\FrameworkInfos::load();
        $ep = $fmkInfos->getDefaultEntryPointInfo();

        $config = AppConfig::loadForCli($ep->getConfigFile(), 'console.php');
        App::setConfig($config);
        App::setRouter(new Router());

        \jFile::createDir(App::tempPath(), App::config()->chmodDir);

        // ----- init the Application object
        $projectInfos = \Jelix\Core\Infos\AppInfos::load();
        $application = new Application($projectInfos->name.' commands');

        // try to read a commands.php file from each modules
        foreach (App::getEnabledModulesPaths() as $module => $path) {
            if (file_exists($path.'commands.php')) {
                require $path.'commands.php';
            }
        }
        return $application->run();
    }
}

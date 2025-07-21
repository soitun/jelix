<?php
/**
 * @package   jelix
 * @subpackage master_admin
 *
 * @author    Laurent Jouanneau
 * @copyright 2008-2025 Laurent Jouanneau
 *
 * @see      http://jelix.org
 * @licence  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public Licence, see LICENCE file
 */
use Jelix\Locale\Locale;

class defaultCtrl extends jController
{
    public $pluginParams = array(
        '*' => array('auth.required' => true),
    );

    public function index()
    {
        $resp = $this->getResponse('html');
        $resp->title = Locale::get('gui.dashboard.title');
        $resp->body->assignZone('MAIN', 'dashboard');
        $user = jAuth::getUserSession();
        $driver = jAuth::getDriver();
        if (method_exists($driver, 'checkPassword')
            && $driver->checkPassword($user->login, $user->password)
        ) {
            jMessage::add(Locale::get('gui.message.admin.password'), 'error');
        }
        $resp->body->assign('selectedMenuItem', 'dashboard');

        return $resp;
    }
}

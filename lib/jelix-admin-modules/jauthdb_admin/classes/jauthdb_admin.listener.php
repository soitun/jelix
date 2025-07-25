<?php
/**
 * @package     jelix
 * @subpackage  jauthdb_admin
 *
 * @author    Laurent Jouanneau
 * @copyright 2008-2012 Laurent Jouanneau
 *
 * @see        http://jelix.org
 * @licence     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public Licence, see LICENCE file
 */
use Jelix\Event\EventListener;

class jauthdb_adminListener extends EventListener
{
    /**
     * @param mixed $event
     */
    public function onmasteradminGetMenuContent($event)
    {
        $plugin = jApp::coord()->getPlugin('auth', false);
        $driver = $plugin->config['driver'];
        $hasDao = isset($plugin->config[$driver]['dao'], $plugin->config[$driver]['compatiblewithdb']) && $plugin->config[$driver]['compatiblewithdb'];
        if ($plugin && ($driver == 'Db' || $hasDao) && jAcl2::check('auth.users.list')) {
            $item = new masterAdminMenuItem('users', \Jelix\Locale\Locale::get('jauthdb_admin~auth.adminmenu.item.list'), jUrl::get('jauthdb_admin~default:index'), 10, 'system');
            $item->icon = jApp::urlJelixWWWPath().'design/images/user.png';
            $event->add($item);
        }
    }
}

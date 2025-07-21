<?php
/**
 * @package     jelix
 * @subpackage  jacl2db_admin
 *
 * @author    Laurent Jouanneau
 * @copyright 2008-2025 Laurent Jouanneau
 *
 * @see        http://jelix.org
 * @licence     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public Licence, see LICENCE file
 */
class jacl2db_adminListener extends jEventListener
{
    /**
     * @param mixed $event
     */
    public function onmasteradminGetMenuContent($event)
    {
        $url = '';
        if (jAcl2::check('acl.group.view')) {
            $url = jUrl::get('jacl2db_admin~groups:index');
        }
        else if (jAcl2::check('acl.user.view')) {
            $url = jUrl::get('jacl2db_admin~users:index');
        }

        if ($url) {
            $item = new masterAdminMenuItem('rights', \Jelix\Locale\Locale::get('jacl2db_admin~acl2.menu.item.rights'), $url, 30, 'system');
            $item->icon = jApp::urlJelixWWWPath().'design/images/rights.png';
            $event->add($item);
        }
    }
}

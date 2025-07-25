<?php
/**
 * @package   jelix
 * @subpackage master_admin
 *
 * @author    Laurent Jouanneau
 * @contributor Kévin Lepeltier
 *
 * @copyright 2008 Laurent Jouanneau, 2009 Kévin Lepeltier
 *
 * @see      http://jelix.org
 * @licence  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public Licence, see LICENCE file
 */
use Jelix\Event\Event;

class admin_infoboxZone extends jZone
{
    protected $_tplname = 'zone_admin_infobox';

    protected function _prepareTpl()
    {
        jClasses::inc('masterAdminMenuItem');

        $items = Event::notify('masteradminGetInfoBoxContent')->getResponse();

        usort($items, 'masterAdminMenuItem::sortItems');

        $this->_tpl->assign('infoboxitems', $items);
        $this->_tpl->assign('user', jAuth::getUserSession());
    }
}

<?php

/**
 * @author    Gérald Croes, Patrice Ferlet, Laurent Jouanneau
 *
 * @copyright 2001-2005 CopixTeam, 2005-2025 Laurent Jouanneau
 *
 * @see       https://www.jelix.org
 * @licence   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

use Jelix\Event\Event;

/**
 * @deprecated use `Jelix\Event\Event` instead.
 */
class jEvent extends Event
{
    /**
     * do nothing. Use \jApp::reloadServices() instead
     *
     * @deprecated
     */
    public static function clearCache()
    {
    }
}
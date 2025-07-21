<?php
/**
 * @package     jelix
 * @subpackage  core_selector
 *
 * @author      Laurent Jouanneau
 * @copyright   2005-2025 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence    GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

use Jelix\Core\Selector\ModuleSelector;

/**
 * base class for all selector concerning module files.
 *
 * General syntax for them : "module~resource".
 * Syntax of resource depend on the selector type.
 * module is optional.
 *
 * @package    jelix
 * @subpackage core_selector
 * @deprecated use Jelix\Core\Selector\ModuleSelector instead
 */
abstract class jSelectorModule extends ModuleSelector implements jISelector
{

}

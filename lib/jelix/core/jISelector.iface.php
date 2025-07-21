<?php
/**
 * a selector is a string refering to a file or a ressource, by indicating its module and its name.
 * For example : "moduleName~resourceName". There are several type of selector, depending on the
 * resource type. Selector objects get the real path of the corresponding file, the name of the
 * compiler (if the file has to be compile) etc.
 * So here, there is a selector class for each selector type.
 *
 * @package     jelix
 * @subpackage  core_selector
 *
 * @author      Laurent Jouanneau
 * @contributor Christophe Thiriot
 *
 * @copyright   2005-2008 Laurent Jouanneau, 2008 Christophe Thiriot
 *
 * @see        http://www.jelix.org
 * @licence    GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

use Jelix\Core\Selector\SelectorInterface;

/**
 * interface of selector classes.
 *
 * @package    jelix
 * @subpackage core_selector
 * @deprecated use Jelix\Core\Selector\SelectorInterface instead
 */
interface jISelector extends SelectorInterface
{

}

<?php
/**
 * @package     jelix
 * @subpackage  jtpl_plugin
 *
 * @author      Laurent Jouanneau
 * @contributor Julien Issler, Bastien Jaillot, Dominique Papin
 *
 * @copyright   2006-2024 Laurent Jouanneau
 * @copyright   2008 Julien Issler, 2008 Bastien Jaillot, 2008 Dominique Papin
 *
 * @see        http://www.jelix.org
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

use Jelix\Forms\Forms;

/**
 * a block to display an html form, with data from a jforms.
 *
 * usage : {form $theformobject,'submit_action', $submit_action_params} here form content {/form}
 *
 * You can add this others parameters :<ul>
 *   <li>string $builderName  (default is 'html')</li>
 *   <li>array  $options for the builder. Example, for the 'html' builder : <ul>
 *      <li>"errorDecorator"=>"name of your javascript object for error listener"</li>
 *      <li>"method" => "post" or "get". default is "post"</li>
 *      </ul>
 *    </li>
 *  </ul>
 *
 * @param jTplCompiler $compiler the template compiler
 * @param bool         $begin    true if it is the begin of block, else false
 * @param array        $param    0=>form object
 *                               1=>selector of submit action
 *                               2=>array of parameters for submit action
 *                               3=>name of the builder : default is html
 *                               4=>array of options for the builder
 *
 * @return string the php code corresponding to the begin or end of the block
 *
 * @see Forms
 */
function jtpl_block_html_form($compiler, $begin, $param = array())
{
    if (!$begin) {
        return '$t->_privateVars[\'__formTplController\']->endForm();
unset($t->_privateVars[\'__formTplController\']);';
    }

    if (count($param) < 2 || count($param) > 5) {
        $compiler->doError2('errors.tplplugin.block.bad.argument.number', 'form', '2-5');

        return '';
    }
    if (count($param) == 2) {
        $param[2] = 'array()';
    }

    if (isset($param[3]) && trim($param[3]) != '""' && trim($param[3]) != "''") {
        $builder = $param[3];
    } else {
        $builder = "'".jApp::config()->tplplugins['defaultJformsBuilder']."'";
    }

    if (isset($param[4])) {
        $options = $param[4];
    } else {
        $options = 'array()';
    }

    $content = ' $t->_privateVars[\'__formTplController\'] = new \\Jelix\\Forms\\HtmlWidget\\TemplateController('.$param[0].','.$builder.','.$options.','.$param[1].','.$param[2].');';
    $content .= '$t->_privateVars[\'__formTplController\']->startForm();';

    $metacontent = 'if(isset('.$param[0].')) { $builder = '.$param[0].'->getBuilder('.$builder.');
    $builder->setOptions('.$options.');
    $builder->outputMetaContent($t);}';
    $compiler->addMetaContent($metacontent);

    return $content;
}

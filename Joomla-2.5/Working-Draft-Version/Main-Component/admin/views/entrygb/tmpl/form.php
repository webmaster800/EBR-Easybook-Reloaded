<?php
/**
 * EBR - Easybook Reloaded for Joomla! 2.5
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');
$pane = JPane::getInstance('sliders');
jimport('joomla.html.editor');
$editor = JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_EASYBOOKRELOADED_DETAILS'); ?></legend>
            <?php
            echo $pane->startPane("menu-pane");
            echo $pane->startPanel(JText::_('COM_EASYBOOKRELOADED_BASIC_DATA'), "param-page");
            ?>
            <table class="admintable">
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="title">
                            <?php echo JText::_('COM_EASYBOOKRELOADED_GB_TITLE'); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="text_area" type="text" name="title" id="title" size="32"
                               value="<?php echo $this->entry->title; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="introtext">
                            <?php echo JText::_('COM_EASYBOOKRELOADED_GB_INTROTEXT'); ?>:
                        </label>
                    </td>
                    <td>
                        <?php echo $editor->display('introtext', $this->entry->introtext, '100%', '300', '75', '20', false); ?>
                    </td>
                </tr>
            </table>
            <?php
            echo $pane->endPanel();
            echo $pane->endPane();
            ?>
        </fieldset>
    </div>

    <div class="clr"></div>

    <input type="hidden" name="option" value="com_easybookreloaded"/>
    <input type="hidden" name="id" value="<?php echo $this->entry->id; ?>"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="controller" value="entrygb"/>
    <?php echo JHTML::_('form.token'); ?>
</form>
<div style="text-align: center;">
    <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', _EASYBOOK_VERSION) ?></p>
</div>
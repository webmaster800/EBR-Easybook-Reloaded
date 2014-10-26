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
?>

    <form action="<?php echo JRoute::_('index.php?option=com_easybookreloaded'); ?>" method="post" name="adminForm"
          id="adminForm">
        <fieldset id="filter-bar">
            <div class="filter-search fltlft">
                <input type="text" name="filter_search" id="filter_search"
                       value="<?php echo $this->escape($this->_state->get('filter.search')); ?>"
                       title="<?php echo JText::_('COM_EASYBOOKRELOADED_FILTERSEARCH'); ?>"/>
                <button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                <button type="button" onclick="document.id('filter_search').value = '';
        this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <div class="filter-select fltrt">
                <select name="filter_gb_id" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('COM_EASYBOOKRELOADED_SELECT_GUESTBOOKS'); ?></option>
                    <?php echo JHtml::_('select.options', $this->guestbooks, 'id', 'title', $this->_state->get('filter.gb_id')); ?>
                </select>
            </div>
        </fieldset>
        <div id="editcell">
            <table class="adminlist">
                <thead>
                <tr>
                    <th width="20">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <th width="6%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_AUTHOR'); ?>
                    </th>
                    <th width="12%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_TITLE'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_EASYBOOKRELOADED_ENTRY'); ?>
                    </th>
                    <th width="16%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_DATE'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_EASYBOOKRELOADED_RATING'); ?>
                    </th>
                    <th width="15%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_COMMENT'); ?>
                    </th>
                    <th width="6%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_GB'); ?>
                    </th>
                    <th width="2%">
                        <?php echo JText::_('COM_EASYBOOKRELOADED_PUBLISHED'); ?>
                    </th>
                </tr>
                </thead>
                <?php
                $k = 0;
                $n = count($this->items);

                for($i = 0; $i < $n; $i++)
                {
                    $row = $this->items[$i];
                    $checked = JHTML::_('grid.id', $i, $row->id);
                    $published = JHTML::_('grid.published', $row, $i);
                    $link = JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=edit&cid[]='.$row->id);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td>
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <?php echo $row->gbname; ?>
                        </td>
                        <td>
                        <span class="hasTip" title="<?php echo $row->gbtitle ?>">
                            <a href="<?php echo $link ?>">
                                <?php
                                if(strlen($row->gbtitle) > 45)
                                {
                                    echo substr($row->gbtitle, 0, 45)."...";
                                }
                                else
                                {
                                    echo $row->gbtitle;
                                }
                                ?>
                            </a>
                        </span>
                        </td>
                        <td>
                        <span class="hasTip" title="<?php echo $row->gbtext ?>">
                            <a href="<?php echo $link ?>">
                                <?php
                                if(strlen($row->gbtext) > 165)
                                {
                                    echo substr($row->gbtext, 0, 165)."...";
                                }
                                else
                                {
                                    echo $row->gbtext;
                                }
                                ?>
                            </a>
                        </span>
                        </td>
                        <td>
                            <?php echo JHTML::_('date', $row->gbdate, JText::_('DATE_FORMAT_LC2')); ?>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $row->gbvote; ?>
                        </td>
                        <td>
                            <?php
                            if($row->gbcomment)
                            {
                                if(strlen($row->gbcomment) > 75)
                                {
                                    echo substr($row->gbcomment, 0, 75)."...";
                                }
                                else
                                {
                                    echo $row->gbcomment;
                                }
                            }
                            ?>
                        </td>
                        <td>
                        <span class="hasTip" title="<?php echo $row->gbid_title ?>">
                                <?php
                                if(strlen($row->gbid_title) > 165)
                                {
                                    echo substr($row->gbid_title, 0, 165)."...";
                                }
                                else
                                {
                                    echo $row->gbid_title;
                                }
                                ?>
                        </span>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $published; ?>
                        </td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
                <tfoot>
                <tr>
                    <td colspan="9">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <input type="hidden" name="option" value="com_easybookreloaded"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="controller" value="entry"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
    <div style="text-align: center;">
        <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', _EASYBOOK_VERSION) ?></p>
    </div>
<?php echo $this->donation_code_message; ?>
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
<!-- Easybook Reloaded - Joomla! 2.5 Component - Kubik-Rubik Joomla! Extensions -->
<div id="easybook">
    <?php if($this->params->get('show_page_title', 1)) : ?>
        <h2 class="componentheading"><?php echo $this->heading ?></h2>
    <?php endif; ?>
    <div class="easy_entrylink">
        <?php if(_EASYBOOK_CANADD AND !$this->params->get('offline')) : ?>
            <strong>
                <a class="sign" href="<?php echo JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add'); ?>" style="text-decoration: none !important;">
                    <?php echo JText::_('COM_EASYBOOKRELOADED_SIGN_GUESTBOOK'); ?>
                    <?php echo JHTML::_('image', 'components/com_easybookreloaded/images/new.png', JText::_('COM_EASYBOOKRELOADED_SIGN_GUESTBOOK'), 'height="16" border="0" width="16" class="png" style="vertical-align: middle; padding-left: 3px;"') ?>
                </a>
            </strong>
        <?php endif; ?>
        <?php if($this->params->get('show_introtext') == 1) : ?>
            <div class="easy_intro">
                <?php echo nl2br($this->params->get('introtext')); ?>
            </div>
        <?php elseif($this->params->get('show_introtext') == 2) : ?>
            <div class="easy_intro">
                <?php echo JTEXT::_('COM_EASYBOOKRELOADED_INTROTEXT'); ?>
            </div>
        <?php endif; ?>
        <?php if($this->params->get('offline')) : ?>
            <?php echo JText::_('COM_EASYBOOKRELOADED_GUESTBOOK_OFFLINE_FRONTEND'); ?>
        <?php else : ?>
            <?php echo $this->loadTemplate('entries'); ?>
            <?php if($this->params->get('show_count_entries')) : ?>
                <div class="easy_pagination">
                    <strong><?php echo $this->count ?><br />
                        <?php if($this->count == 1) :
                            echo JText::_('COM_EASYBOOKRELOADED_ENTRY_IN_THE_GUESTBOOK');
                        else :
                            echo JText::_('COM_EASYBOOKRELOADED_ENTRIES_IN_THE_GUESTBOOK');
                        endif; ?>
                    </strong>
                </div>
            <?php endif; ?>
            <?php if($this->pagination->total > $this->pagination->limit) : ?>
                <div class="easy_pagination">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if($this->params->get('show_logo', true) == 1) : ?>
            <p id="easyfooter">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank"><img src="<?php echo JURI::base(); ?>components/com_easybookreloaded/images/logo.png" class="png" alt="EasyBook Reloaded - Logo" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" border="0" width="80" height="36" /></a>
            </p>
        <?php elseif($this->params->get('show_logo', true) == 2) : ?>
            <p id="easyfooter">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank">Easybook Reloaded</a>
            </p>
        <?php elseif($this->params->get('show_logo', true) == 3) : ?>
            <p id="easyfooterinv">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank">Easybook Reloaded</a>
            </p>
        <?php endif; ?>
    </div>
</div>

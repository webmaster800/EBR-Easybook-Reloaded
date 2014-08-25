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

<h1>Easybook Reloaded</h1>
<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ABOUT_EASYBOOK_RELOADED'); ?>

    <p><strong><?php echo JTEXT::_('COM_EASYBOOKRELOADED_ABOUT_VERSION'); ?>: <?php echo _EASYBOOK_VERSION; ?></strong></p>
    <p><?php echo JText::_('COM_EASYBOOKRELOADED_ABOUT_DONATION_CODE'); ?></p>
    <div style="text-align: center;">
        <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', _EASYBOOK_VERSION) ?></p>
    </div>
<?php echo $this->donation_code_message; ?>
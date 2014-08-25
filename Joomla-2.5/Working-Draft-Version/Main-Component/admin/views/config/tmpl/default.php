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
    <script type="text/javascript">
        function calcHeight() {
            var config_height =
                document.getElementById('config_iframe').contentWindow.
                    document.body.scrollHeight;

            document.getElementById('config_iframe').height =
                config_height + 10;
        }
    </script>

    <iframe src="index.php?option=com_config&view=component&component=com_easybookreloaded&amp;path=&amp;tmpl=component"
            width="100%" scrolling="no" id="config_iframe" onLoad="calcHeight();" height="1px" frameborder="0"
            name="Easybook Reloaded - Configuration">
        <p>
            <a rel="{handler: 'iframe', size: {x: 570, y: 500}}"
               href="index.php?option=com_config&amp;view=component&amp;component=com_easybookreloaded&amp;path=&amp;tmpl=component"
               class="modal">
                <?php echo JText::_('COM_EASYBOOKRELOADED_NOFRAMES'); ?>
            </a>
        </p>
    </iframe>
    <div style="text-align: center;">
        <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', _EASYBOOK_VERSION) ?></p>
    </div>
<?php echo $this->donation_code_message; ?>
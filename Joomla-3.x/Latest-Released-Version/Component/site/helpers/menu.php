<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3
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
jimport('joomla.application.component.helper');

class EasybookReloadedHelperMenu
{
    /**
     * Gets the name of the component from the menu entry
     *
     * @return type
     */
    function getName()
    {
        $component = JComponentHelper::getComponent('com_easybookreloaded');

        $menus = JApplication::getMenu('site', array());
        $items = $menus->getItems('component_id', $component->id);
        $match = null;

        foreach($items as $item)
        {
            if(@$item->query['view'] == 'easybookreloaded')
            {
                $match = $item->title;
                break;
            }
        }

        return $match;
    }
}

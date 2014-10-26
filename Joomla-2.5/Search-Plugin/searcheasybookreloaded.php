<?php
/**
 *  @Copyright
 *  @package    Easybook Reloaded - Search Plugin Joomla! 2.5
 *  @author     Viktor Vogel {@link http://www.kubik-rubik.de}
 *  @version    2.5-2
 *  @date       Created on 18-Aug-2012
 *  @link       Project Site {@link http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded}
 *
 *  @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class plgSearchSearchEasybookReloaded extends JPlugin
{
    public function onContentSearchAreas()
    {
        static $areas = array();

        $name = $this->getName();

        if(isset($name))
        {
            $areas['easybookreloaded'] = $name;
        }

        return $areas;
    }

    public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        $db = JFactory::getDBO();

        if(is_array($areas))
        {
            if(!array_intersect($areas, array_keys($this->onContentSearchAreas())))
            {
                return array();
            }
        }

        $limit = $this->params->get('search_limit', 20);

        $text = trim($text);

        if(empty($text))
        {
            return array();
        }

        switch($ordering)
        {
            case 'alpha':
                $order = 'gbtext ASC';
                break;

            case 'oldest':
                $order = 'gbdate ASC';
                break;

            case 'category':
            case 'popular':
            case 'newest':
            default:
                $order = 'gbdate DESC';
                break;
        }

        $text = $db->getEscaped($text);
        $query = 'SELECT CONCAT_WS(" - ", gbtitle, gbname) AS title, id AS slug , gbdate AS created,'
                .' gbtext AS text,'
                .' "2" AS browsernav'
                .' FROM #__easybook '
                .' WHERE (gbname LIKE "%'.$text.'%"'
                .' OR gbtitle LIKE "%'.$text.'%"'
                .' OR gbtext LIKE "%'.$text.'%"'
                .' OR gbcomment LIKE "%'.$text.'%")'
                .' AND published = 1'
                .' ORDER BY '.$order
        ;
        $db->setQuery($query, 0, $limit);
        $rows = $db->loadObjectList();

        if(!empty($rows))
        {
            // Get Itemid of Easybook Reloaded
            $query = "SELECT ".$db->nameQuote('id')." FROM ".$db->nameQuote('#__menu')." WHERE ".$db->nameQuote('link')." = 'index.php?option=com_easybookreloaded&view=easybookreloaded' AND ".$db->nameQuote('published')." = 1";
            $db->setQuery($query);
            $Itemid = $db->loadResult();

            // Get name from the menu entry
            $section = $this->getName();

            require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'route.php');

            foreach($rows as $key => $row)
            {
                $rows[$key]->href = EasybookReloadedHelperRoute::getEasybookReloadedRoute($row->slug, $Itemid);
                $rows[$key]->section = $section;
            }
        }

        return $rows;
    }

    // Get the name of the component from the menu entry
    private function getName()
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

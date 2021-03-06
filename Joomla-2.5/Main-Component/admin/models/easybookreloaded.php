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
jimport('joomla.application.component.model');

class EasybookReloadedModelEasybookReloaded extends JModel
{
    var $_entry;
    var $_total;
    var $_pagination;
    var $_version;

    function __construct()
    {
        parent::__construct();
        $mainframe = JFactory::getApplication();

        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest('easybookreloaded.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $search = $mainframe->getUserStateFromRequest('easybookreloaded.filter.search', 'filter_search', null);
        $gb_id = $mainframe->getUserStateFromRequest('easybookreloaded.filter.gb_id', 'filter_gb_id', null);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('filter.search', $search);
        $this->setState('filter.gb_id', $gb_id);
    }

    private function buildQuery()
    {
        $query = $this->_db->getQuery(true);

        $query->select('a.*');
        $query->select('b.title as gbid_title');
        $query->from('#__easybook AS a');
        $query->from('#__easybook_gb AS b');
        $query->where('(a.gbid = b.id)');

        // Is a search term provided?
        $search = $this->getState('filter.search');

        if(!empty($search))
        {
            $search = $this->_db->Quote('%'.$this->_db->escape($search, true).'%');
            $query->where('((a.gbname LIKE '.$search.') OR (a.gbmail LIKE '.$search.') OR (a.gbtext LIKE '.$search.') OR (a.gbtitle LIKE '.$search.') OR (a.gbcomment LIKE '.$search.'))');
        }

        // Is a category selected?
        $gb_id = $this->getState('filter.gb_id');

        if(!empty($gb_id))
        {
            $query->where('(a.gbid = '.(int)$gb_id.')');
        }

        $query->order($this->_db->escape('id DESC'));

        return $query;
    }

    function getData()
    {
        if(empty($this->_data))
        {
            $query = $this->buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    function getPagination()
    {
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    function getTotal()
    {
        if(empty($this->_total))
        {
            $query = $this->buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }
}

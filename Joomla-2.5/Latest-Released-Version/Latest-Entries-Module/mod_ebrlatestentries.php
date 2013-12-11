<?php
/**
 *  @Copyright
 *  @package    Easybook Reloaded - Latest Entries Module Joomla 2.5 - Module
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

jimport('joomla.application.component.helper');

if(!JComponentHelper::isEnabled('com_easybookreloaded', true))
{
    echo JText::_('MOD_EBRLATESTENTRIES_NOCOMP');
}
else
{
    require_once (dirname(__FILE__).DS.'helper.php');
    $suffix = $params->get('moduleclass_sfx');

    $start = new mod_ebrlatestentriesHelper;
    list($posts, $Itemid) = $start->getPosts($params);

    if(!empty($posts))
    {
        require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'smilie.php');

        $showname = $params->get('showname');
        $showtitle = $params->get('showtitle');
        $showentrylink = $params->get('showentrylink');
        $showsmilies = $params->get('showsmilies');
        $smiliesset = $params->get('smiliesset');
        $bbcode = $params->get('convertbbcode');
        $showcode = $params->get('showcode');
        $showdate = $params->get('showdate');

        if($params->get('length'))
        {
            $start->cutText($posts, intval($params->get('length')));
        }

        $start->replaceSmilies($posts, $showsmilies, $smiliesset);

        if(!$showcode)
        {
            $start->noCode($posts);
        }

        if($bbcode)
        {
            require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'content.php');
            $start->bbCode($posts);
        }

        if($params->get('wordlength'))
        {
            $start->wordWrap($posts, intval($params->get('wordlength')));
        }

        if($showname == 2 OR $showentrylink)
        {
            require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'route.php');
            $start->entryLink($posts, $Itemid);
        }

        $document = JFactory::getDocument();
        $document->addStyleSheet('modules/mod_ebrlatestentries/ebrlatestentries.css');

        require(JModuleHelper::getLayoutPath('mod_ebrlatestentries'));
    }
    else
    {
        require(JModuleHelper::getLayoutPath('mod_ebrlatestentries', 'empty'));
    }
}

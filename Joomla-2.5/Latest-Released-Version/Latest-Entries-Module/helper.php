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

class mod_ebrlatestentriesHelper extends JObject
{

    function getPosts($params)
    {
        $limit = intval($params->get('count'));
        $db = JFactory::getDBO();

        $ids = array_map('trim', explode(',', $params->get('ids')));

        if(!empty($ids[0]))
        {
            $entries = array();

            foreach($ids as $id)
            {
                $query = 'SELECT * FROM '.$db->nameQuote('#__easybook').' WHERE '.$db->nameQuote('published').' = 1 AND '.$db->nameQuote('id').' = '.$id.' ORDER BY '.$db->nameQuote('gbdate').' DESC';
                ;
                $db->setQuery($query);
                $custom_entry = $db->loadAssocList();

                $entries = array_merge($entries, $custom_entry);
            }
        }

        if($params->get('random'))
        {
            $query = 'SELECT * FROM '.$db->nameQuote('#__easybook').' WHERE '.$db->nameQuote('published').' = 1 ORDER BY '.$db->nameQuote('gbdate').' DESC';
        }
        else
        {
            $query = 'SELECT * FROM '.$db->nameQuote('#__easybook').' WHERE '.$db->nameQuote('published').' = 1 ORDER BY '.$db->nameQuote('gbdate').' DESC LIMIT 0, '.$limit.'';
        }

        $db->setQuery($query);
        $result = $db->loadAssocList();

        if($params->get('random'))
        {
            shuffle($result);
            $result = array_slice($result, 0, $limit);
        }

        $query = 'SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__menu').' WHERE '.$db->nameQuote('link').' = '.$db->quote("index.php?option=com_easybookreloaded&view=easybookreloaded").' AND '.$db->nameQuote('published').' = 1';
        $db->setQuery($query);
        $Itemid = $db->loadResult();

        if($db->getErrorNum())
        {
            JError::raiseWarning(500, $db->stderr());
        }

        if(!empty($entries))
        {
            $result = array_merge($entries, $result);
        }

        $result = array($result, $Itemid);

        return $result;
    }

    function cutText(&$posts, $length)
    {
        foreach($posts as &$post)
        {
            if(strlen($post['gbtext']) > $length)
            {
                $post['gbtext'] = substr($post['gbtext'], 0, $length);
                $post['gbtext'] .= '...';
            }
        }
    }

    function replaceSmilies(&$posts, $showsmilies, $smiliesset)
    {
        $smiley = EasybookReloadedHelperSmilie::getSmilies();

        foreach($posts as &$post)
        {
            preg_match_all("@\[code.*\].*\[/code\]@isU", $post['gbtext'], $matches);

            $j = 0;

            foreach($matches[0] as $match)
            {
                $post['gbtext'] = str_replace($match, '[codetemp'.$j.']', $post['gbtext']);
                $j++;
            }

            foreach($smiley as $tag => $sm)
            {
                if($showsmilies)
                {
                    if($smiliesset == 0)
                    {
                        $post['gbtext'] = str_replace($tag, "<img src='".JURI::base()."components/com_easybookreloaded/images/smilies/$sm' border='0' alt='$tag' title='$tag' />", $post['gbtext']);
                    }
                    elseif($smiliesset == 1)
                    {
                        $post['gbtext'] = str_replace($tag, "<img src='".JURI::base()."components/com_easybookreloaded/images/smilies2/$sm' border='0' alt='$tag' title='$tag' />", $post['gbtext']);
                    }
                }
                else
                {
                    $post['gbtext'] = str_replace($tag, '', $post['gbtext']);
                }
            }

            $j = 0;

            foreach($matches[0] as $match)
            {
                $post['gbtext'] = str_replace('[codetemp'.$j.']', $match, $post['gbtext']);
                $j++;
            }
        }
    }

    function wordWrap(&$posts, $length)
    {
        foreach($posts as &$post)
        {
            $text = str_replace('<br />', ' ', $post['gbtext']);
            $text_clean = preg_replace('@\s+@', ' ', strip_tags($text));

            $words = explode(' ', $text_clean);

            foreach($words as $value)
            {
                if(strlen($value) > $length)
                {
                    $value_cut = wordwrap($value, $length, ' ', true);
                    $post['gbtext'] = str_replace($value, $value_cut, $post['gbtext']);
                }
            }

            $post['text_clean'] = $text_clean;
        }
    }

    function bbCode(&$posts)
    {
        $app = JFactory::getApplication();
        $ebconfig = $app->getParams('com_easybookreloaded');

        foreach($posts as &$post)
        {
            $post['gbtext'] = preg_replace('@(\015\012)|(\015)|(\012)@', '<br />', $post['gbtext']);
            EasybookReloadedHelperContent::convertBbCode($post['gbtext'], $ebconfig);
        }
    }

    function noCode(&$posts)
    {
        foreach($posts as &$post)
        {
            $post['gbtext'] = preg_replace("@\[code.*\].*\[/code\]@isU", "<p><strong>[CODE]</strong></p>", $post['gbtext']);
        }
    }

    function entryLink(&$posts, $Itemid)
    {
        foreach($posts as &$post)
        {
            $post['entrylink'] = JRoute::_(EasybookReloadedHelperRoute::getEasybookReloadedRoute($post['id'], $Itemid));
        }
    }

}

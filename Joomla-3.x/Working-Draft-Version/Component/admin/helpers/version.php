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

class EasybookReloadedHelperVersion extends JObject
{
    /* TODO
    var $_current;

    function __construct()
    {
    $this->_loadVersion();
    }

    function _loadVersion()
    {
    require_once( JPATH_COMPONENT.DS.'libraries'.DS.'httpclient.class.php' );
    $client = new HttpClient('joomla-extensions.kubik-rubik.de');

    if (!$client->get('/')) {
    $this->setError($client->getError());
    return false;
    }

    $this->_current = $client->getContent();
    return true;
    }

    function checkVersion($version)
    {
    if ($version == $this->_current) {
    return 1;
    } elseif($this->getErrors()) {
    return -2;
    } else {
    return -1;
    }
    }

    function getVersion()
    {
    return $this->_current;
    } */
}

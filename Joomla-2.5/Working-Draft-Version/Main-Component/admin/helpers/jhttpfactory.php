<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTTP
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * EBR - Easybook Reloaded for Joomla! 2.5
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 * Description: This file is a backport from the official Joomla! 3.x release for Joomla! 2.5
 *
 * @license     GNU/GPL
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
defined('JPATH_PLATFORM') or die;

/**
 * HTTP factory class.
 *
 * @package     Joomla.Platform
 * @subpackage  HTTP
 * @since       12.1
 */
class JHttpFactory
{
    /**
     * Recieve an valid Http instance.
     *
     * @param   JRegistry $options  Client options object.
     * @param   mixed     $adapters Adapter (string) or queue of adapters (array) to use for communication.
     *
     * @return  JHttp      Joomla Http class
     *
     * @since   12.1
     */
    public static function getHttp(JRegistry $options = null, $adapters = null)
    {
        if(empty($options))
        {
            $options = new JRegistry;
        }

        // First check whether an adapter exists for the call
        $available_adapter = self::getAvailableDriver($options, $adapters);

        if(!empty($available_adapter))
        {
            return new JHttp($options, $available_adapter);
        }
        else
        {
            return false;
        }
    }

    /**
     * Finds an available http transport object for communication
     *
     * @param   JRegistry $options Option for creating http transport object
     * @param   mixed     $default Adapter (string) or queue of adapters (array) to use
     *
     * @return  JHttpTransport Interface sub-class
     *
     * @since   12.1
     */
    public static function getAvailableDriver(JRegistry $options, $default = null)
    {
        if(is_null($default))
        {
            $availableAdapters = self::getHttpTransports();
        }
        else
        {
            settype($default, 'array');
            $availableAdapters = $default;
        }

        // Check if there is available http transport adapters
        if(!count($availableAdapters))
        {
            return false;
        }

        foreach($availableAdapters as $adapter)
        {
            if(self::isSupported($adapter))
            {
                $class = 'JHttpTransport'.ucfirst($adapter);

                return new $class($options);
            }
        }

        return false;
    }

    /**
     * Check whether the available adapters are supported by the server configuration
     * Yeah, this is a limitation on the three adapters but it is necessary because the classes in Joomla! 2.5 do not
     * provide the isSuppored function as the transport classes in Joomla! 3 do. These are the default handlers, so it
     * is okay to limit the checks on them.
     *
     * @param   string $adapter
     */
    private static function isSupported($adapter)
    {
        if($adapter == 'curl')
        {
            return (function_exists('curl_init') AND is_callable('curl_init') AND curl_version());
        }
        elseif($adapter == 'stream')
        {
            return (function_exists('fopen') AND is_callable('fopen') AND ini_get('allow_url_fopen'));
        }
        elseif($adapter == 'socket')
        {
            return (function_exists('fsockopen') AND is_callable('fsockopen'));
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the http transport handlers
     *
     * @return  array  An array of available transport handlers
     */
    public static function getHttpTransports()
    {
        $names = array();

        // Get the handlers from the correct libraries folder
        $iterator = new DirectoryIterator(JPATH_LIBRARIES.'/joomla/http/transport');

        foreach($iterator as $file)
        {
            $fileName = $file->getFilename();

            if($file->isFile() && substr($fileName, strrpos($fileName, '.') + 1) == 'php')
            {
                $names[] = substr($fileName, 0, strrpos($fileName, '.'));
            }
        }

        return $names;
    }
}

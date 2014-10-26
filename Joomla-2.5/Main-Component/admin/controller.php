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
jimport('joomla.application.component.controller');

class EasybookReloadedController extends JController
{
    function display($cachable = false, $urlparams = false)
    {
        $task = JRequest::getCmd('task');

        if($task == 'config')
        {
            JRequest::setVar('view', 'config');
            JRequest::setVar('layout', 'default');
        }
        elseif($task == 'about')
        {
            JRequest::setVar('view', 'about');
            JRequest::setVar('layout', 'default');
        }
        elseif($task == 'gb')
        {
            JRequest::setVar('view', 'gb');
            JRequest::setVar('layout', 'default');
        }

        // We need the gb model also in the default view for the selection list
        $view = $this->getView('easybookreloaded', 'html');

        if($model_gb = $this->getModel('gb'))
        {
            $view->setModel($model_gb, false);
        }

        require_once JPATH_COMPONENT.'/helpers/easybookreloaded.php';
        EasybookReloadedHelper::addSubmenu(JRequest::getCmd('view', 'easybookreloaded'));

        parent::display();
    }
}

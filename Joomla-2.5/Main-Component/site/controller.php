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
        parent::display();
    }

    function publish_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            $model = $this->getModel('entry');

            switch($model->publish())
            {
                case -1:
                    $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS');
                    $type = 'error';
                    break;
                case 0:
                    $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_UNPUBLISHED');
                    $type = 'message';
                    break;
                case 1:
                    $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_PUBLISHED');
                    $type = 'message';
                    break;
            }
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS');
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
    }

    function remove_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            $model = $this->getModel('entry');

            if(!$model->delete())
            {
                $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_ENTRY_COULD_NOT_BE_DELETED');
                $type = 'error';
            }
            else
            {
                $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_DELETED');
                $type = 'message';
            }
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_ENTRY_COULD_NOT_BE_DELETED');
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
    }

    function comment_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            JRequest::setVar('view', 'entry');
            JRequest::setVar('layout', 'commentform_mail');
            JRequest::setVar('hidemainmenu', 1);
            parent::display();
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
            $type = 'error';
            $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
        }
    }

    function savecomment_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            $model = $this->getModel('entry');

            if(!$row = $model->savecomment())
            {
                $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
                $type = 'error';
            }
            else
            {
                // Change state of the guestbook entry
                if(isset($row['toggle_state']) AND $row['toggle_state'] == 1)
                {
                    $model->publish();
                }

                if(isset($row['inform']) AND $row['inform'] == 1)
                {
                    $data = $model->getRow($row['id']);
                    $uri = JFactory::getURI();
                    $mail = JFactory::getMailer();
                    $params = JComponentHelper::getParams('com_easybookreloaded');
                    require_once(JPATH_SITE.'/components/com_easybookreloaded/helpers/route.php');

                    $href = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRoute($data->get('id'), $gbid);
                    $mail->setsubject(JTEXT::_('COM_EASYBOOKRELOADED_ADMIN_COMMENT_SUBJECT'));

                    if($params->get('send_mail_html'))
                    {
                        $mail->IsHTML(true);
                        $mail->setbody(JTEXT::sprintf('COM_EASYBOOKRELOADED_ADMIN_COMMENT_BODY_HTML', $data->get('gbname'), $uri->base(), $href));
                    }
                    else
                    {
                        $mail->setbody(JTEXT::sprintf('COM_EASYBOOKRELOADED_ADMIN_COMMENT_BODY', $data->get('gbname'), $uri->base(), $href));
                    }

                    $mail->addrecipient($data->get('gbmail'));
                    $mail->send();

                    $msg = JText::_('COM_EASYBOOKRELOADED_COMMENT_SAVED_INFORM');
                }
                else
                {
                    $msg = JText::_('COM_EASYBOOKRELOADED_COMMENT_SAVED');
                }

                $type = 'message';
            }
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
    }

    function edit_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            JRequest::setVar('view', 'entry');
            JRequest::setVar('layout', 'form_mail');
            parent::display();
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_PLEASE_VALIDATE_YOUR_INPUTS');
            $type = 'error';
            $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
        }
    }

    function save_mail()
    {
        $hashrequest = JRequest::getString('hash');
        $check_hash = $this->performMail($hashrequest);
        $gbid = JRequest::getInt('gbid');

        if($check_hash == true)
        {
            $params = JComponentHelper::getParams('com_easybookreloaded');

            // Reset the time to avoid error in the spam check
            $session = JFactory::getSession();
            $time = $session->get('time', null, 'easybookreloaded');
            $session->set('time', $time - $params->get('type_time_sec'), 'easybookreloaded');

            $model = $this->getModel('entry');

            if($model->store())
            {
                if($params->get('default_published', true))
                {
                    $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_SAVED');
                    $type = 'message';
                }
                else
                {
                    $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_SAVED_BUT_HAS_TO_BE_APPROVED');
                    $type = 'notice';
                }
            }
            else
            {
                $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
                $type = 'notice';
            }
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
    }

    function performMail($hashrequest)
    {
        // No empty hash value
        if(empty($hashrequest))
        {
            return false;
        }

        // Prepare request string
        $hash_data = explode('-', $hashrequest);

        // The hash_data array must have 2 entries - ID of entry and the hash itself
        if(count($hash_data) != 2)
        {
            return false;
        }

        // Check whether the ID is available and numeric - load the entry for the checks
        if(!empty($hash_data[0]) AND is_numeric($hash_data[0]))
        {
            $model = $this->getModel('entry');
            $gbrow = $model->getRow($hash_data[0]);
        }
        else
        {
            return false;
        }

        // Check whether the hash link is still valid
        $params = JComponentHelper::getParams('com_easybookreloaded');

        $app = JFactory::getApplication();
        $offset = $app->getCfg('offset');

        $date_entry = JFactory::getDate($gbrow->get('gbdate'));
        $date_entry->setOffset($offset);

        $date_now = JFactory::getDate();
        $date_now->setOffset($offset);

        $valid_time_emailnot = $params->get('valid_time_emailnot') * 60 * 60 * 24;

        if($date_entry->toUnix() + $valid_time_emailnot <= $date_now->toUnix())
        {
            return false;
        }

        // Create a second hash link from the same data and compare it with the transmitted hash value
        $hash = array();
        $hash['id'] = (int)$gbrow->get('id');
        $hash['gbmail'] = md5($gbrow->get('gbmail'));
        $hash['username'] = $gbrow->get('gbname');

        $secret_word = $params->get('secret_word');

        if(!empty($secret_word))
        {
            $hash['custom_secret'] = $params->get('secret_word');
        }
        else
        {
            // Get config object for the secret word and sitename
            $config = JFactory::getConfig();
            $hash['custom_secret'] = $config->getValue('secret');
        }

        $hash = substr(base64_encode(md5(serialize($hash))), 0, 16);

        if($hash != $hash_data[1])
        {
            return false;
        }

        return true;
    }

}

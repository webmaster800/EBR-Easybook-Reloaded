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

class EasybookReloadedControllerEntry extends JController
{
    protected $_access = null;
    protected $_input;

    function __construct()
    {
        parent::__construct();

        $this->_input = JFactory::getApplication()->input;
    }

    function _add_edit()
    {
        $params = JComponentHelper::getParams('com_easybookreloaded');
        $id = $this->_input->getInt('cid', 0);

        if((($id == 0 AND _EASYBOOK_CANADD) OR ($id != 0 AND _EASYBOOK_CANEDIT)) AND !$params->get('offline'))
        {
            $this->_input->set('view', 'entry');
            $this->_input->set('layout', 'form');
            parent::display();
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
            $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.JFactory::getSession()->get('gbid', false, 'easybookreloaded'), false), $msg, $type);
        }
    }

    function add()
    {
        $this->_add_edit();
    }

    function edit()
    {
        $this->_add_edit();
    }

    function save()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        // Clean page cache if System Cache plugin is enabled
        if(JPluginHelper::isEnabled('system', 'cache'))
        {
            $this->cleancache();
        }

        $params = JComponentHelper::getParams('com_easybookreloaded');
        $id = JRequest::getInt('id', 0);
        $gbid = JFactory::getSession()->get('gbid', false, 'easybookreloaded');

        if((($id == 0 AND _EASYBOOK_CANADD) OR ($id != 0 AND _EASYBOOK_CANEDIT)) AND !$params->get('offline'))
        {
            $model = $this->getModel('entry');

            // Store the entered data, create an output message and send the notification mail
            if($row = $model->store())
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

                $link = JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false);

                if($id == 0 AND $params->get('send_mail', true))
                {
                    // Reference needed objects and prepare the requested variables for the mail
                    $mail = JFactory::getMailer();
                    $uri = JFactory::getURI();
                    $db = JFactory::getDBO();
                    require_once(JPATH_SITE.'/components/com_easybookreloaded/helpers/route.php');

                    // Load all request variables - because JInput doesn't allow to load the whole data at once, a workaround
                    //is used. This was easily possible with the deprecated JRequest (e.g. JRequest::get('post');)
                    $temp_data = $_REQUEST;
                    array_walk($temp_data, create_function('&$temp_data', '$temp_data = htmlspecialchars(strip_tags(trim($temp_data)));'));

                    // Get unfiltered request variable is only with a trick with JInput possible, so direct access is used instead
                    // Possible solution: list($gbtext) = ($this->_input->get('gbtext', array(0), 'array') - use the filter array
                    // With JRequest one could use - JRequest::getVar('gbtext', NULL, 'post', 'none', JREQUEST_ALLOWRAW)
                    $temp_data['gbtext'] = htmlspecialchars($_REQUEST['gbtext'], ENT_QUOTES);

                    if(isset($temp_data['id']))
                    {
                        $id = $temp_data['id'];
                    }
                    else
                    {
                        $id = 0;
                    }

                    $name = $temp_data['gbname'];

                    if(!empty($temp_data['gbtitle']))
                    {
                        $title = $temp_data['gbtitle'];
                    }
                    else
                    {
                        $title = '';
                    }

                    $text = $temp_data['gbtext'];

                    if($params->get('enable_log', true))
                    {
                        $ip = getenv('REMOTE_ADDR');
                    }
                    else
                    {
                        $ip = '-';
                    }

                    // Get config object for the secret word and sitename
                    $config = JFactory::getConfig();

                    $hash = array();
                    $hash['id'] = (int)$row->get('id');
                    $hash['gbmail'] = md5($row->get('gbmail'));
                    $hash['username'] = $row->get('gbname');

                    // Get the custom secret word. If no word was set, take the Joomla! secret word from the configuration
                    $secret_word = $params->get('secret_word');

                    if(!empty($secret_word))
                    {
                        $hash['custom_secret'] = $params->get('secret_word');
                    }
                    else
                    {
                        $hash['custom_secret'] = $config->getValue('secret');
                    }

                    $hash = substr(base64_encode(md5(serialize($hash))), 0, 16);
                    $hash_id = $row->get('id').'-'.$hash;

                    $href = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRoute($row->get('id'), $gbid);
                    $hashmail_publish = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHash('publish_mail', $gbid).$hash_id;
                    $hashmail_comment = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHash('comment_mail', $gbid).$hash_id;
                    $hashmail_edit = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHash('edit_mail', $gbid).$hash_id;
                    $hashmail_delete = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHash('remove_mail', $gbid).$hash_id;

                    // Mail subject - get the name of the website and add it to the subject
                    $site_name = $config->getValue('sitename');

                    $mail->setsubject(JTEXT::sprintf('COM_EASYBOOKRELOADED_NEW_GUESTBOOKENTRY', $site_name));

                    if($params->get('send_mail_html'))
                    {
                        $mail->IsHTML(true);
                        $mail->setbody(JTEXT::sprintf('COM_EASYBOOKRELOADED_A_NEW_GUESTBOOKENTRY_HAS_BEEN_WRITTEN_HTML', $uri->base(), $name, $title, $text, $href, $hashmail_publish, $hashmail_comment, $hashmail_edit, $hashmail_delete, $ip));
                    }
                    else
                    {
                        $mail->setbody(JTEXT::sprintf('COM_EASYBOOKRELOADED_A_NEW_GUESTBOOKENTRY_HAS_BEEN_WRITTEN', $uri->base(), $name, $title, $text, $href, $hashmail_publish, $hashmail_comment, $hashmail_edit, $hashmail_delete, $ip));
                    }

                    // Get mail addresses for the notification mail
                    $admins = array();
                    $emailfornotification_usergroup_array = $params->get('emailfornotification_usergroup', array(8));

                    foreach($emailfornotification_usergroup_array as $emailfornotification_usergroup)
                    {
                        $query = "SELECT ".$db->nameQuote('email')." FROM ".$db->nameQuote('#__users')." AS A, ".$db->nameQuote('#__user_usergroup_map')." AS B WHERE ".$db->nameQuote('B.group_id')." = ".$db->quote($emailfornotification_usergroup)." AND ".$db->nameQuote('B.user_id')." = ".$db->nameQuote('A.id')." AND ".$db->nameQuote('A.sendEmail')." = 1";
                        $db->setQuery($query);
                        $result = $db->loadResultArray();

                        if(!empty($result))
                        {
                            foreach($result as $value)
                            {
                                $admins[] = $value;
                            }
                        }
                    }

                    if($params->get('emailfornotification'))
                    {
                        $emailfornotification = array_map('trim', explode(',', $params->get('emailfornotification')));

                        foreach($emailfornotification as $email)
                        {
                            $admins[] = $email;
                        }
                    }

                    // Set recipient and reply to
                    $mail->addrecipient($admins);
                    $replyto = array($row->get('gbmail'), $row->get('gbname'));
                    $mail->addReplyTo($replyto);
                    $mail->setSender(array($config->get('mailfrom'), $config->get('fromname')));

                    // Which mail type should be used? Default is mail - since 2.5-7
                    if($config->get('mailer') == 'sendmail')
                    {
                        $mail->useSendmail($config->get('sendmail'));
                    }
                    elseif($config->get('mailer') == 'smtp')
                    {
                        $mail->useSMTP($config->get('smtpauth'), $config->get('smtphost'), $config->get('smtpuser'), $config->get('smtppass'), $config->get('smtpsecure'), $config->get('smtpport'));
                    }

                    // Send the mail
                    $mail->send();
                }
            }
            else
            {
                $session = JFactory::getSession();
                $errors_output = array();
                $errors_array = array_keys($session->get('errors', null, 'easybookreloaded'));

                if((in_array('easycalccheck', $errors_array)) OR (in_array('easycalccheck_time', $errors_array)))
                {
                    if(in_array('easycalccheck_time', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_EASYCALCCHECK_TIME');
                    }
                    else
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_EASYCALCCHECK');
                    }
                }
                elseif(in_array('akismet', $errors_array))
                {
                    $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_AKISMET');
                }
                elseif(in_array('gbid', $errors_array))
                {
                    $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_GBID');
                }
                elseif(in_array('easycalccheck_question', $errors_array))
                {
                    $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_SPAMCHECKQUESTION');
                }
                else
                {
                    if(in_array('name', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_NAME');
                    }

                    if(in_array('mail', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_MAIL');
                    }

                    if(in_array('title', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_TITLE');
                    }

                    if(in_array('text', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_TEXT');
                    }

                    if(in_array('aim', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_AIM');
                    }

                    if(in_array('icq', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_ICQ');
                    }

                    if(in_array('yah', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_YAH');
                    }

                    if(in_array('skype', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_SKYPE');
                    }

                    if(in_array('msn', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_MSN');
                    }

                    if(in_array('toomanylinks', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_TOOMANYLINKS');
                    }

                    if(in_array('iptimelock', $errors_array))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_ERROR_TIMELOCK');
                    }

                    if(empty($errors_output))
                    {
                        $errors_output[] = JTEXT::_('COM_EASYBOOKRELOADED_UNKNOWNERROR');
                    }
                }

                $errors = implode(', ', $errors_output);

                $msg = JText::sprintf('COM_EASYBOOKRELOADED_PLEASE_VALIDATE_YOUR_INPUTS', $errors);
                $link = JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add&retry=true', false);
                $type = 'notice';

                $session->clear('errors', 'easybookreloaded');
            }

            $this->setRedirect($link, $msg, $type);
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
            $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
        }
    }

    function comment()
    {
        if(_EASYBOOK_CANEDIT)
        {
            $this->_input->set('view', 'entry');
            $this->_input->set('layout', 'commentform');
            $this->_input->set('hidemainmenu', 1);
            parent::display();
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
            $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.JFactory::getSession()->get('gbid', false, 'easybookreloaded'), false), $msg, $type);
        }
    }

    function remove()
    {
        // Clean page cache if System Cache plugin is enabled
        if(JPluginHelper::isEnabled('system', 'cache'))
        {
            $this->cleancache();
        }

        if(_EASYBOOK_CANEDIT)
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
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.JFactory::getSession()->get('gbid', false, 'easybookreloaded'), false), $msg, $type);
    }

    function publish()
    {
        // Clean page cache if System Cache plugin is enabled
        if(JPluginHelper::isEnabled('system', 'cache'))
        {
            $this->cleancache();
        }

        if(_EASYBOOK_CANEDIT)
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
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.JFactory::getSession()->get('gbid', false, 'easybookreloaded'), false), $msg, $type);
    }

    function savecomment()
    {
        // Clean page cache if System Cache plugin is enabled
        if(JPluginHelper::isEnabled('system', 'cache'))
        {
            $this->cleancache();
        }

        if(_EASYBOOK_CANEDIT)
        {
            JSession::checkToken() OR jexit('Invalid Token');
            $gbid = JFactory::getSession()->get('gbid', false, 'easybookreloaded');
            $model = $this->getModel('entry');

            if(!$row = $model->savecomment())
            {
                $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_SAVE_COMMENT');
                $type = 'error';
            }
            else
            {
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
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_RIGHTS');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false), $msg, $type);
    }

    function cleancache()
    {
        $gbid = JFactory::getSession()->get('gbid', false, 'easybookreloaded');
        $cache = JFactory::getCache();
        $id = md5(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded&gbid='.$gbid, false));
        $cache->remove($id, 'page');
        $id_entry = md5(JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add', false));
        $cache->remove($id_entry, 'page');
        $id_entry_retry = md5(JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add&retry=true', false));
        $cache->remove($id_entry_retry, 'page');

        return;
    }
}

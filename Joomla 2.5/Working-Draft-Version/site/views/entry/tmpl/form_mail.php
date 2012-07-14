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
$hashrequest = JRequest::getString('hash');
?>
<!-- Easybook Reloaded - Joomla! 2.5 Component by Kubik-Rubik.de - Viktor Vogel -->
<div id="easybook">
    <?php if($this->params->get('show_page_title', 1))
    {
        ?>
        <h2 class="componentheading"><?php echo $this->heading ?></h2>
<?php } ?>
    <div class="easy_entrylink">
        <strong><a class="view" href="<?php echo JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded'); ?>" style="text-decoration: none !important;"><?php echo JText::_('COM_EASYBOOKRELOADED_READ_GUESTBOOK'); ?><?php echo JHTML::_('image', 'components/com_easybookreloaded/images/book.png', JText::_('COM_EASYBOOKRELOADED_READ_GUESTBOOK').":", 'height="16" border="0" width="16" class="png" style="vertical-align: middle; padding-left: 3px;"'); ?></a></strong>
        <br />
        <br />
        <script type="text/javascript">
            function x()
            {
                return;
            }

            function insertprompt(insert, input, start, end, revisedMessage, currentMessage)
            {
                // Internet Explorer
                if (typeof document.selection != 'undefined')
                {
                    var range = document.selection.createRange();
                    range.text = insert;
                    var range = document.selection.createRange();
                    range.move('character', 0);
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != 'undefined')
                {
                    revisedMessage = currentMessage.substr(0, start) + insert + currentMessage.substr(end);
                    document.gbookForm.gbtext.value=revisedMessage;
                    document.gbookForm.gbtext.focus();
                    var pos;
                    pos = start + insert.length;
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
            }

            function insert(aTag, eTag)
            {
                var input = document.forms['gbookForm'].elements['gbtext'];
                input.focus();
                // Internet Explorer
                if(typeof document.selection != 'undefined')
                {
                    var range = document.selection.createRange();
                    var insText = range.text;
                    range.text = aTag + insText + eTag;
                    range = document.selection.createRange();
                    if (insText.length == 0)
                    {
                        range.move('character', -eTag.length);
                    }
                    else
                    {
                        range.moveStart('character', aTag.length + insText.length + eTag.length);
                    }
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != 'undefined')
                {
                    var start = input.selectionStart;
                    var end = input.selectionEnd;
                    var insText = input.value.substring(start, end);
                    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
                    var pos;
                    if (insText.length == 0)
                    {
                        pos = start + aTag.length;
                    }
                    else
                    {
                        pos = start + aTag.length + insText.length + eTag.length;
                    }
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
                else
                {
                    var pos;
                    var re = new RegExp('^[0-9]{0,3}$');
                    while (!re.test(pos))
                    {
                        pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
                    }
                    if (pos > input.value.length)
                    {
                        pos = input.value.length;
                    }
                    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
                    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
                }
            }

            function insertsmilie(thesmile)
            {
                var input = document.forms['gbookForm'].elements['gbtext'];
                input.focus();
                // Internet Explorer
                if(typeof document.selection != 'undefined')
                {
                    var range = document.selection.createRange();
                    var insText = range.text;
                    range.text = " "+thesmile+" ";
                    range = document.selection.createRange();
                    range.move('character', 0);
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != 'undefined')
                {
                    var start = input.selectionStart;
                    var end = input.selectionEnd;
                    var insText = input.value.substring(start, end);
                    input.value = input.value.substr(0, start) + " "+thesmile+" " + input.value.substr(end);
                    var pos;
                    pos = start + (thesmile.length + 2);
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
                else
                {
                    var pos;
                    var re = new RegExp('^[0-9]{0,3}$');
                    while (!re.test(pos))
                    {
                        pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
                    }
                    if (pos > input.value.length)
                    {
                        pos = input.value.length;
                    }
                    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
                    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
                }
            }

        <?php if($this->params->get('support_bbcode', false))
        {
            ?>
        function DoPrompt(action)
        {
            var input = document.forms['gbookForm'].elements['gbtext'];
            input.focus();

            var start = input.selectionStart;
            var end = input.selectionEnd;
            var revisedMessage;
            var currentMessage = document.gbookForm.gbtext.value;

            <?php if($this->params->get('support_link', false))
            {
                ?>
            if (action == "url")
            {
                var thisURL = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_URL_HERE'); ?>", "http://");
                var thisTitle = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_WEB_PAGE_TITLE'); ?>", "<?php echo JTEXT::_('COM_EASYBOOKRELOADED_WEB_PAGE_TITLE'); ?>");
                if (thisURL != undefined && thisTitle != undefined)
                {
                    if  (thisURL != "" && thisTitle != "")
                    {
                        var urlBBCode = "[URL="+thisURL+"]"+thisTitle+"[/URL]";
                        insertprompt(urlBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
            <?php } ?>
            <?php if($this->params->get('support_mail', true))
            {
                ?>
            if (action == "email")
            {
                var thisEmail = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_EMAIL_ADDRESS'); ?>", "");
                if (thisEmail != undefined)
                {
                    if  (thisEmail != "")
                    {
                        var emailBBCode = "[EMAIL]"+thisEmail+"[/EMAIL]";
                        insertprompt(emailBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
             <?php } ?>
            if (action == "code")
            {
                var thisLanguage = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_WHICH_LANGUAGE'); ?>", "");
                if (thisLanguage != undefined)
                {
                    if  (thisLanguage != "")
                    {
                        var codeBBCode = "[CODE="+thisLanguage+"]\n\n[/CODE]";
                        insertprompt(codeBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
            if (action == "youtube")
            {
                var thisYoutube = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_YOUTUBE_VIDEO_ID'); ?>", "");
                if (thisYoutube != undefined)
                {
                    if  (thisYoutube != "")
                    {
                        var codeBBCode = "[YOUTUBE]"+thisYoutube+"[/YOUTUBE]";
                        insertprompt(codeBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
            <?php if($this->params->get('support_pic', false))
            {
                ?>
            if (action == "image")
            {
                var thisImage = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_URL_OF_THE_PICTURE_YOU_WANT_TO_SHOW'); ?>", "http://");
                if (thisImage != undefined)
                {
                    if  (thisImage != "")
                    {
                        var imageBBCode = "[IMG]"+thisImage+"[/IMG]";
                        insertprompt(imageBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
            if (action == "image_link")
            {
                var thisImage = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_URL_OF_THE_PICTURE_YOU_WANT_TO_SHOW'); ?>", "http://");
                var thisURL = prompt("<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_THE_URL_HERE'); ?>", "http://");
                if (thisImage != undefined && thisURL != undefined)
                {
                    if  (thisImage != "" && thisURL != "")
                    {
                        var imageBBCode = "[IMGLINK="+thisURL+"]"+thisImage+"[/IMGLINK]";
                        insertprompt(imageBBCode, input, start, end, revisedMessage, currentMessage);
                    }
                }
                return;
            }
            <?php } ?>
            }
         <?php } ?>
        </script>
        <form name='gbookForm' action='<?php JRoute::_('index.php'); ?>' target='_top' method='post'>
            <input type='hidden' name='option' value='com_easybookreloaded' />
            <input type='hidden' name='task' value='save_mail' />
            <?php if($this->entry->id) : ?>
                <input type='hidden' name='id' value='<?php echo $this->entry->id; ?>' />
            <?php endif; ?>
            <input type='hidden' name='hash' value='<?php echo $hashrequest; ?>' />
            <?php if($this->params->get('enable_spam', true)) : ?>
                <input type='hidden' name='<?php echo $this->session->get('spamcheck_field_name', null, 'easybookreloaded'); ?>' value='<?php echo $this->session->get('spamcheckresult', null, 'easybookreloaded'); ?>' />
            <?php endif; ?>
            <?php if($this->params->get('spamcheck_question', true)) : ?>
                <input type='hidden' name='<?php echo $this->session->get('spamcheck_question_field_name', null, 'easybookreloaded'); ?>' value='<?php echo $this->params->get('spamcheck_question_answer'); ?>' />
            <?php endif; ?>

            <table align='center' width='90%' cellpadding='0' cellspacing='4' border='0' >

                <?php if($this->params->get('enable_log', true))
                {
                    ?>
                    <tr>
                        <td width='130'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_IP_ADDRESS'); ?><span class='small'>*</span></td>
                        <td><input type='text' name='gbiip' style='width:245px;' class='inputbox' value='<?php echo $this->entry->ip; ?>' disabled='disabled' /></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td width='130'><label for='gbname'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_NAME'); ?></label><span class='small'>*</span></td>
                    <td><input type='text' name='gbname' id='gbname' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbname; ?>' /></td>
                </tr>

                <?php if($this->params->get('show_mail', true) OR $this->params->get('require_mail', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbmail'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_EMAIL'); ?></label>
                        <?php
                        if($this->params->get('require_mail', true))
                        {
                            echo "<span class='small'>*</span>";
                        }
                        ?>
                    </td>
                        <td><input type='text' name='gbmail' id='gbmail' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbmail; ?>' /></td>
                    </tr>

                    <?php if(!$this->entry->id)
                    {
                        ?>
                        <tr>
                            <td width='130'><label for='gbmailshow'><?php echo JTEXT::_('SHOW_E-MAIL_IN_PUBLIC'); ?></label></td>
                            <td><input type='checkbox' name='gbmailshow' id='gbmailshow' class='inputbox' value='1' /></td>
                        </tr>
                    <?php } ?>
                    <?php
                }

                if($this->params->get('show_home', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbpage'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_HOMEPAGE'); ?></label></td>
                        <td><input type='text' name='gbpage' id='gbpage' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbpage; ?>' /></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_loca', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbloca'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_LOCATION'); ?></label></td>
                        <td><input type='text' name='gbloca' id='gbloca' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbloca; ?>' /></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_icq', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbicq'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_ICQ_NUMBER'); ?></label></td>
                        <td><input type='text' name='gbicq' id='gbicq' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbicq; ?>' /></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_aim', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbaim'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_AIM_NICKNAME'); ?></label></td>
                        <td><input type='text' name='gbaim' id='gbaim' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbaim; ?>' /></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_msn', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbmsn'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_MSN_MESSENGER'); ?></label></td>
                        <td><input type='text' name='gbmsn' id='gbmsn' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbmsn; ?>' /></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_yah', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbyah'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_YAHOO_MESSENGER'); ?></label></td>
                        <td><input type='text' name='gbyah' id='gbyah' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbyah; ?>'/></td>
                    </tr>
                    <?php
                }

                if($this->params->get('show_skype', true))
                {
                    ?>
                    <tr>
                        <td width='130'><label for='gbskype'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_SKYPE_NICKNAME'); ?></label></td>
                        <td><input type='text' name='gbskype' id='gbskype' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbskype ?>' /></td>
                    </tr>
                            <?php
                        }
                        if($this->params->get('show_rating', true))
                        {
                            echo '<script src="components/com_easybookreloaded/scripts/moostarrating.js" type="text/javascript"></script>';
                            ?>
                    <tr>
                        <td width='130'><label for='gbvote'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_WEBSITE_RATING'); ?></label></td>
                        <td>
                            <?php
                            echo "<input type='hidden' type='radio' name='gbvote' value='0' />";
                            for($i = 1; $i <= $this->params->get('rating_max', 5); $i++)
                            {
                                if((isset($this->entry->gbvote)) AND ($i == $this->entry->gbvote))
                                {
                                    echo '<input type="radio" name="gbvote" value="'.$i.'" checked="checked">';
                                }
                                else
                                {
                                    echo '<input type="radio" name="gbvote" value="'.$i.'">';
                                }
                            }
                            echo '<span id="easybookvotetip"></span>'
                            ?>
                        </td>
                    </tr>
                    <?php
                    }
                    else
                    {
                        echo "<input type='hidden' name='gbvote' value='0' />";
                    }
                    if($this->params->get('show_title', true))
                    {
                        ?>
                    <tr>
                        <td width='130'><label for='gbtitle'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_TITLE'); ?></label>
                        <?php
                        if($this->params->get('require_title', true))
                        {
                            echo "<span class='small'>*</span>";
                        }
                        ?>
                        </td>
                        <td><input type='text' name='gbtitle' id='gbtitle' style='width:245px;' class='inputbox' value='<?php echo $this->entry->gbtitle; ?>' /></td>
                    </tr>
                    <?php
                        }
                        // Switch for BB Code support
                    if($this->params->get('support_bbcode', false))
                    {
                        ?>
                    <tr>
                        <td width='130'></td>
                        <td>
                            <?php if($this->params->get('support_link', false))
                            {
                                ?>
                                <a href='javascript:x()' onclick='DoPrompt("url");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/world_link.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_WEB_ADDRESS'); ?>' height='16' width='16' /></a>
                                <?php
                            }
                            if($this->params->get('support_mail', true))
                            {
                                ?>
                                <a href='javascript:x()' onclick='DoPrompt("email");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/email_link.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_EMAIL_ADDRESS'); ?>' height='16' width='16' /></a>
                                <?php
                            }
                            if($this->params->get('support_pic', false))
                            {
                                ?>
                                <a href='javascript:x()' onclick='DoPrompt("image_link");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/picture_link.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_SHOW_IMAGE_WITH_A_LINK'); ?>' height='16' width='16' /></a>
                                <?php
                            }
                            if($this->params->get('support_pic', false))
                            {
                                ?>
                                <a href='javascript:x()' onclick='DoPrompt("image");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/picture.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_SHOWS_IMAGE_FROM_AN_URL'); ?>' height='16' width='16' /></a>
                                <?php
                            }
                            if($this->params->get('support_code', false))
                            {
                                ?>
                                <a href='javascript:x()' onclick='DoPrompt("code");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/code.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ENTER_CODE'); ?>' height='16' width='16' /></a>
        <?php
    }
    if($this->params->get('support_youtube', false))
    {
        ?>
                                <a href='javascript:x()' onclick='DoPrompt("youtube");'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/youtube.png' hspace='3' border='0' alt='' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_YOUTUBE'); ?>' height='16' width='16' /></a>
                            <?php } ?>
                            <a href='javascript:x()' onclick='insert("[B]", "[/B]")'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/text_bold.png' hspace='3' border='0' alt='Bold' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_BOLD'); ?>' height='16' width='16' /></a>
                            <a href='javascript:x()' onclick='insert("[I]", "[/I]")'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/text_italic.png' hspace='3' border='0' alt='Italic' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_ITALIC'); ?>' height='16' width='16' /></a>
                            <a href='javascript:x()' onclick='insert("[U]", "[/U]")'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/text_underline.png' hspace='3' border='0' alt='Underline' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_UNDERLINE'); ?>' height='16' width='16' /></a>
                            <a href='javascript:x()' onclick='insert("[CENTER]", "[/CENTER]")'><img src='<?php echo $this->baseurl ?>/components/com_easybookreloaded/images/text_align_center.png' hspace='3' border='0' alt='Center' title='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_CENTER'); ?>' height='16' width='16' /></a>
                        </td>
                    </tr>
                        <?php } ?>
                <tr>
                    <td width='130' valign='top'><label for='gbtext'><?php echo JTEXT::_('COM_EASYBOOKRELOADED_GUESTBOOK_ENTRY'); ?></label><span class='small'>*</span>
                        <br /><br />
                        <?php
                        // Switch for Smilie Support
                        if($this->params->get('support_smilie', true))
                        {
                            $count = 1;
                            $smiley = EasybookReloadedHelperSmilie::getSmilies();

                            foreach($smiley as $i => $sm)
                            {
                                if($this->params->get('smilie_set') == 0)
                                {
                                    echo "<a href=\"javascript:insertsmilie('$i')\" title='$i'>".JHTML::_('image', 'components/com_easybookreloaded/images/smilies/'.$sm, $sm, 'border="0"')."</a> ";
                                }
                                else
                                {
                                    echo "<a href=\"javascript:insertsmilie('$i')\" title='$i'>".JHTML::_('image', 'components/com_easybookreloaded/images/smilies2/'.$sm, $sm, 'border="0"')."</a> ";
                                }
                                if($count % 4 == 0)
                                {
                                    echo "<br />";
                                }
                                $count++;
                            }
                        }
                        ?>
                    </td>
                    <td valign='top'><textarea style='width:245px;' rows='8' cols='50' name='gbtext' id='gbtext' class='inputbox'><?php echo $this->entry->gbtext; ?></textarea></td>
                </tr>
                <tr>
                    <td width='130'></td>
                    <td style='padding-left: 130px;'><br /><input type='submit' name='send' value='<?php echo JTEXT::_('COM_EASYBOOKRELOADED_SUBMIT_ENTRY'); ?>' class='button' /></td>
                </tr>
            </table>
        </form>
        <p>
            <span class='small' style='padding-left:400px;'>* <?php echo JTEXT::_('COM_EASYBOOKRELOADED_REQUIRED_FIELD'); ?></span>
        </p>
        <?php if($this->params->get('show_logo', true) == 1)
        {
            ?>
            <p id="easyfooter">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank"><img src="<?php echo JURI::base(); ?>components/com_easybookreloaded/images/logo.png" class="png" alt="EasyBook Reloaded - Logo" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" border="0" width="80" height="36" /></a>
            </p>
        <?php
        }
        elseif($this->params->get('show_logo', true) == 2)
        {
            ?>
            <p id="easyfooter">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank">Easybook Reloaded by Kubik-Rubik.de</a>
            </p>
        <?php
        }
        elseif($this->params->get('show_logo', true) == 3)
        {
            ?>
            <p id="easyfooterinv">
                <a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Kubik-Rubik Joomla! Extension by Viktor Vogel" target="_blank">Easybook Reloaded by Kubik-Rubik.de</a>
            </p>
        <?php } ?>
    </div>
</div>
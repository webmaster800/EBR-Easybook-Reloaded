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
jimport('joomla.application.component.helper');

abstract class EasybookReloadedHelperContent
{
    /**
     * Parses the content and comment of the entries
     *
     * @param string $message
     *
     * @return string
     */
    static function parse($message)
    {
        $app = JFactory::getApplication();
        $params = $app->getParams('com_easybookreloaded');

        // Convert CR and LF to HTML BR command
        $message = preg_replace('@(\015\012)|(\015)|(\012)@', '<br />', $message);

        if($params->get('support_smilie', true))
        {
            EasybookReloadedHelperContent::replaceSmilies($message, $params);
        }

        if($params->get('support_bbcode', true))
        {
            EasybookReloadedHelperContent::convertBbCode($message, $params);
        }

        if($params->get('wordwrap', true))
        {
            EasybookReloadedHelperContent::wordwrap($message, $params);
        }

        return $message;
    }

    /**
     * Replaces all smilies with the corresponding images
     *
     * @param stinge $message
     * @param object $params
     */
    static function replaceSmilies(&$message, $params)
    {
        $smiley = EasybookReloadedHelperSmilie::getSmilies();

        // Save code blocks temporarily in an array to avoid replacement
        preg_match_all('@\[code=?(.*?)\](<br />)*(.*?)(<br />)*\[/code\]@si', $message, $matches);

        $i = 0;

        foreach($matches[0] as $match)
        {
            $message = str_replace($match, '[codetemp'.$i.']', $message);
            $i++;
        }

        foreach($smiley as $i => $sm)
        {
            if($params->get('smilie_set') == 0)
            {
                $message = str_replace($i, '<img src="'.JURI::base().'components/com_easybookreloaded/images/smilies/'.$sm.'" alt="'.$i.'" title="'.$i.'" />', $message);
            }
            else
            {
                $message = str_replace($i, '<img src="'.JURI::base().'components/com_easybookreloaded/images/smilies2/'.$sm.'" alt="'.$i.'" title="'.$i.'" />', $message);
            }
        }

        // Reset all code blocks
        $i = 0;

        foreach($matches[0] as $match)
        {
            $message = str_replace('[codetemp'.$i.']', $match, $message);
            $i++;
        }

        return;
    }

    /**
     * Converts all supported BBCode to correct HTML output
     *
     * @param string $message
     * @param object $params
     */
    static function convertBbCode(&$message, $params)
    {
        $message = preg_replace('@\[quote\](.*?)\[/quote]@si', '<strong>Quote:</strong><br /><blockquote>\\1</blockquote>', $message);
        $message = preg_replace('@\[b\](.*?)\[/b\]@si', '<strong>\\1</strong>', $message);
        $message = preg_replace('@\[i\](.*?)\[/i\]@si', '<i>\\1</i>', $message);
        $message = preg_replace('@\[u\](.*?)\[/u\]@si', '<u>\\1</u>', $message);
        $message = preg_replace('@\[center\](.*)\[/center\]@siU', '<p class="easy_center">\\1</p>', $message);

        if($params->get('support_link', false))
        {
            $message = preg_replace('@\[url=(http://)?(.*?)\](.*?)\[/url\]@si', '<a href="http://\\2" title="\\3" rel="nofollow" target="_blank">\\3</a>', $message);
        }

        if($params->get('support_code', false))
        {
            $message = preg_replace('@\[CODE=?(.*?)\](<br />)*(.*?)(<br />)*\[/code\]@si', '<pre xml:\\1>\\3</pre>', $message);
            $regex = '@<pre xml:s*(.*?)>(.*?)</pre>@s';
            $message = preg_replace_callback($regex, array('EasybookReloadedHelperContent', 'geshi_replacer'), $message);
        }

        if($params->get('support_mail', true))
        {
            if(preg_match_all('@\[email\](.*?)\[/email\]@si', $message, $matches))
            {
                foreach($matches[1] as $value)
                {
                    $message = preg_replace('@\[email\](.*?)\[/email\]@si', static::cloak($value), $message);
                }
            }
        }

        if($params->get('support_pic', false))
        {
            $message = preg_replace('@\[img\](.*)\[/img\]@siU', '<img src="\\1" alt="\\1"  title="\\1" />', $message);
            $message = preg_replace('@\[imglink=(http://)?(.*?)\](.*?)\[/imglink\]@si', '<a href="http://\\2" title="\\2" rel="nofollow" target="_blank"><img src="\\3" alt="\\3" /></a>', $message);
        }

        if($params->get('support_youtube', false))
        {
            preg_match_all('@\[youtube\](.*)\[/youtube\]@siU', $message, $matches);

            if(!empty($matches[1]))
            {
                $count = 0;

                foreach($matches[1] as $match)
                {
                    if(preg_match('@v=([^&]+)&?.*@', $match, $video_id))
                    {
                        $match = $video_id[1];
                    }

                    $message = str_replace($matches[0][$count], '[youtube]'.$match.'[/youtube]', $message);

                    $count++;
                }

                $message = preg_replace('@\[youtube\](.*)\[/youtube\]@siU', '<p class="easy_center"><iframe width="640" height="360" src="https://www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe></p>', $message);
            }
        }

        $matchCount = preg_match_all('@\[list\](.*?)\[/list\]@si', $message, $matches);

        for($i = 0; $i < $matchCount; $i++)
        {
            $currMatchTextBefore = preg_quote($matches[1][$i]);
            $currMatchTextAfter = preg_replace('@\[\*\]@si', '<li>', $matches[1][$i]);
            $message = preg_replace('@\[list\]'.$currMatchTextBefore.'\[/list\]@si', '<ul>'.$currMatchTextAfter.'</ul>', $message);
        }

        $matchCount = preg_match_all('@\[list=([a1])\](.*?)\[/list\]@si', $message, $matches);

        for($i = 0; $i < $matchCount; $i++)
        {
            $currMatchTextBefore = preg_quote($matches[2][$i]);
            $currMatchTextAfter = preg_replace('@\[\*\]@si', '<li>', $matches[2][$i]);
            $message = preg_replace('@\[list=([a1])\]'.$currMatchTextBefore.'\[/list\]@si', '<ol type=\\1>'.$currMatchTextAfter.'</ol>', $message);
        }

        return;
    }

    /**
     * Wraps the text to a given number of characters
     *
     * @param string $message
     * @param object $params
     */
    static function wordwrap(&$message, $params)
    {
        $size = (int)$params->get('maxlength', 75);
        $words = explode(' ', strip_tags($message));
        $count = count($words);

        for($i = 0; $i < $count; $i++)
        {
            if(strlen($words[$i]) > $size)
            {
                $message = str_replace($words[$i], wordwrap($words[$i], $size, ' ', true), $message);
            }
        }

        return;
    }

    /**
     * Replaces code blocks into formatted blocks with the help of GeSHi
     *
     * @param array $matches
     *
     * @return string
     */
    static function geshi_replacer(&$matches)
    {
        require_once(JPATH_BASE.'/plugins/content/geshi/geshi/geshi.php');

        $app = JFactory::getApplication();
        $params = $app->getParams('com_easybookreloaded');

        $text = $matches[2];
        $lang = $matches[1];

        if($lang == 'html')
        {
            $lang = 'html4strict';
        }
        elseif($lang == 'js')
        {
            $lang = 'javascript';
        }

        $html_entities_match = array("@<br />@", '@&amp;@', "@<@", "@>@", "@&#039;@", '@&quot;@', '@&nbsp;@');
        $html_entities_replace = array("\n", '&', '&lt;', '&gt;', "'", '"', ' ');
        $text = preg_replace($html_entities_match, $html_entities_replace, $text);

        $text = str_replace('&lt;', '<', $text);
        $text = str_replace('&gt;', '>', $text);
        $text = str_replace("\t", '  ', $text);

        $geshi = new GeSHi($text, $lang);
        $geshi->enable_keyword_links(false);

        if($params->get('geshi_lines') == 1)
        {
            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        }

        $text = $geshi->parse_code();

        return $text;
    }

    /**
     * Simple JavaScript email cloaker
     *
     * By default replaces an email with a mailto link with email cloaked
     *
     * @param   string  $mail   The -mail address to cloak.
     * @param   boolean $mailto True if text and mailing address differ
     * @param   string  $text   Text for the link
     * @param   boolean $email  True if text is an e-mail address
     *
     * @return  string  The cloaked email.
     *
     * @since   1.5
     */
    public static function cloak($mail, $mailto = true, $text = '', $email = true)
    {
        // Convert text
        $mail = static::convertEncoding($mail);

        // Split email by @ symbol
        $mail = explode('@', $mail);
        $mail_parts = explode('.', $mail[1]);

        // Random number
        $rand = rand(1, 100000);

        $replacement = "<script type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
        $replacement .= "\n var path = 'hr' + 'ef' + '=';";
        $replacement .= "\n var addy".$rand." = '".@$mail[0]."' + '&#64;';";
        $replacement .= "\n addy".$rand." = addy".$rand." + '".implode("' + '&#46;' + '", $mail_parts)."';";

        if($mailto)
        {
            // Special handling when mail text is different from mail address
            if($text)
            {
                if($email)
                {
                    // Convert text - here is the right place
                    $text = static::convertEncoding($text);

                    // Split email by @ symbol
                    $text = explode('@', $text);
                    $text_parts = explode('.', $text[1]);
                    $replacement .= "\n var addy_text".$rand." = '".@$text[0]."' + '&#64;' + '".implode("' + '&#46;' + '", @$text_parts)."';";
                }
                else
                {
                    $replacement .= "\n var addy_text".$rand." = '".$text."';";
                }

                $replacement .= "\n document.write('<a ' + path + '\'' + prefix + ':' + addy".$rand." + '\'>');";
                $replacement .= "\n document.write(addy_text".$rand.");";
                $replacement .= "\n document.write('<\/a>');";
            }
            else
            {
                $replacement .= "\n document.write('<a ' + path + '\'' + prefix + ':' + addy".$rand." + '\'>');";
                $replacement .= "\n document.write(addy".$rand.");";
                $replacement .= "\n document.write('<\/a>');";
            }
        }
        else
        {
            $replacement .= "\n document.write(addy".$rand.");";
        }

        $replacement .= "\n //-->";
        $replacement .= '\n </script>';

        // XHTML compliance no Javascript text handling
        $replacement .= "<script type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n document.write('<span style=\'display: none;\'>');";
        $replacement .= "\n //-->";
        $replacement .= "\n </script>";
        $replacement .= JText::_('JLIB_HTML_CLOAKING');
        $replacement .= "\n <script type='text/javascript'>";
        $replacement .= "\n <!--";
        $replacement .= "\n document.write('</');";
        $replacement .= "\n document.write('span>');";
        $replacement .= "\n //-->";
        $replacement .= "\n </script>";

        return $replacement;
    }

    /**
     * Convert encoded text
     *
     * @param   string $text Text to convert
     *
     * @return  string  The converted text.
     *
     * @since   1.5
     */
    protected static function convertEncoding($text)
    {
        // Replace vowels with character encoding
        $text = str_replace('a', '&#97;', $text);
        $text = str_replace('e', '&#101;', $text);
        $text = str_replace('i', '&#105;', $text);
        $text = str_replace('o', '&#111;', $text);
        $text = str_replace('u', '&#117;', $text);

        return $text;
    }
}

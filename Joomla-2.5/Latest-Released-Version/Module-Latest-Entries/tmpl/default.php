<?php
/**
 *  @Copyright
 *  @package    Easybook Reloaded - Latest Entries Module Joomla 2.5 - Module
 *  @author     Viktor Vogel {@link http://www.kubik-rubik.de}
 *  @version    2.5-1
 *  @date       Created on 27-Apr-2012
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
echo '<!-- Easybook Reloaded - Module Latest Entries - by Kubik-Rubik.de -->';

foreach($posts as $post) : ?>
    <div class="easylast_entry<?php echo $suffix; ?>">
        <?php if($showname) : ?>
            <div class="easylast_name<?php echo $suffix; ?>">
                <?php if($showname == 2) : ?>
                    <a href="<?php echo $post['entrylink']; ?>" title="<?php echo $post['gbname']; ?>">
                        <?php echo $post['gbname']; ?>
                    </a>
                <?php elseif($showname == 1) : ?>
                    <?php echo $post['gbname']; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($showtitle) : ?>
            <?php if(!empty($post['gbtitle'])) : ?>
                <div class="easylast_title<?php echo $suffix; ?>">
                    <?php echo $post['gbtitle']; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="easylast_text<?php echo $suffix; ?>">
            <?php echo $post['gbtext']; ?>
        </div>
        <?php if($showdate) : ?>
            <div class="easylast_small">
                <em>
                    <?php if($showdate == 1) :
                        echo JHTML::_('date', $post['gbdate'], JText::_('DATE_FORMAT_LC1'));
                    elseif($showdate == 2) :
                        echo JHTML::_('date', $post['gbdate'], JText::_('DATE_FORMAT_LC2'));
                    endif; ?>
                </em>
            </div>
        <?php endif; ?>
        <?php if($showentrylink) : ?>
            <div class="easylast_link<?php echo $suffix; ?>">
                <em>
                    <a href="<?php echo $post['entrylink']; ?>" title="<?php echo $post['text_clean']; ?>">
                        <?php echo JText::_('MOD_EBRLATESTENTRIES_TOENTRY'); ?>
                    </a>
                </em>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
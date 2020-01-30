<?php
/**
 * @version      4.2.0
 * @package      Simple Image Gallery Fork
 * @author       Andreas Kar (thex) <andreas.kar@gmx.at>
 * @copyright    Copyright © 2020 Andreas Kar. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$extraClass = 'fancybox-gallery';
$customLinkAttributes = 'data-fancybox="gallery'.$gal_id.'"';

$stylesheets = array(
    'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@'.$fancybox_version.'/dist/jquery.fancybox.min.css'
);
$stylesheetDeclarations = array();
$scripts = array(
    'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@'.$fancybox_version.'/dist/jquery.fancybox.min.js'
);

if(!defined('PE_FANCYBOX_LOADED')){
    define('PE_FANCYBOX_LOADED', true);
    $customLanguage = '';
    if($fancybox_language == 'xx') {
        $customLanguage = "$.fancybox.defaults.i18n.en = {
                    CLOSE: '".$fancybox_close."',
                    NEXT: '".$fancybox_next."',
                    PREV: '".$fancybox_prev."',
                    ERROR: '".$fancybox_error."',
                    PLAY_START: '".$fancybox_play_start."',
                    PLAY_STOP: '".$fancybox_play_stop."',
                    FULL_SCREEN: '".$fancybox_full_screen."',
                    THUMBS: '".$fancybox_thumbs."',
                    DOWNLOAD: '".$fancybox_download."',
                    SHARE: '".$fancybox_share."',
                    ZOOM: '".$fancybox_zoom."'
                };";
    }
    $scriptDeclarations = array("
        (function($) {
            $(document).ready(function() {
                $.fancybox.defaults.i18n.en = {
                    CLOSE: '".JText::_('JW_PLG_SIGF_FB_CLOSE')."',
                    NEXT: '".JText::_('JW_PLG_SIGF_FB_NEXT')."',
                    PREV: '".JText::_('JW_PLG_SIGF_FB_PREVIOUS')."',
                    ERROR: '".JText::_('JW_PLG_SIGF_FB_REQUEST_CANNOT_BE_LOADED')."',
                    PLAY_START: '".JText::_('JW_PLG_SIGF_FB_START_SLIDESHOW')."',
                    PLAY_STOP: '".JText::_('JW_PLG_SIGF_FB_PAUSE_SLIDESHOW')."',
                    FULL_SCREEN: '".JText::_('JW_PLG_SIGF_FB_FULL_SCREEN')."',
                    THUMBS: '".JText::_('JW_PLG_SIGF_FB_THUMBS')."',
                    DOWNLOAD: '".JText::_('JW_PLG_SIGF_FB_DOWNLOAD')."',
                    SHARE: '".JText::_('JW_PLG_SIGF_FB_SHARE')."',
                    ZOOM: '".JText::_('JW_PLG_SIGF_FB_ZOOM')."'
                };
                ".$customLanguage."
                $.fancybox.defaults.lang = '".$fancybox_language."';
                $('a.fancybox-gallery').fancybox({
                    buttons: [
                        'slideShow',
                        'fullScreen',
                        'thumbs',
                        'share',
                        'download',
                        //'zoom',
                        'close'
                    ],
                    beforeShow: function(instance, current) {
                        if (current.type === 'image') {
                            var title = current.opts.\$orig.attr('title');
                            current.opts.caption = (title.length ? '<b class=\"fancyboxCounter\">".JText::_('JW_PLG_SIGF_FB_IMAGE')." ' + (current.index + 1) + ' ".JText::_('JW_PLG_SIGF_FB_OF')." ' + instance.group.length + '</b>' + ' | ' + title : '');
                        }
                    }
                });
            });
        })(jQuery);
    ");
} else {
    $scriptDeclarations = array();
}

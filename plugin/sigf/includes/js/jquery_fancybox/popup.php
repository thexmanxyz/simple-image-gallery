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
$fancyBoxPath = ($fancybox_cdn == 'on') ? 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@' . $fancybox_version . '/dist/' : '';

$stylesheets = array(
    $fancyBoxPath . 'jquery.fancybox.min.css'
);
$stylesheetDeclarations = array();
$scripts = array(
    $fancyBoxPath . 'jquery.fancybox.min.js'
);

if(!defined('PE_FANCYBOX_LOADED')){
    define('PE_FANCYBOX_LOADED', true);

    $currentLanguage = JFactory::getLanguage();
    $langTag = substr($currentLanguage->getTag(), 0, 2);

    $buttons = '';
    if($fancybox_button_slideshow == 'on') {
        $buttons .= '\'slideShow\',';
    }
    if($fancybox_button_fullscreen == 'on') {
        $buttons .= '\'fullScreen\',';
    }
    if($fancybox_button_thumbs == 'on') {
        $buttons .= '\'thumbs\',';
    }
    if($fancybox_button_share == 'on') {
        $buttons .= '\'share\',';
    }
    if($fancybox_button_download == 'on') {
        $buttons .= '\'download\',';
    }
    if($fancybox_button_zoom == 'on') {
        $buttons .= '\'zoom\',';
    }
    if($fancybox_button_close == 'on') {
        $buttons .= '\'close\',';
    }
    if(strlen($buttons) > 0) {
        $buttons = rtrim($buttons, ',');
    }

    $captionCounter = '';
    if($fancybox_caption_image == 'on') {
        $captionCounter .= (" + '" . $fancybox_image . "'");
    }
    if(strlen($captionCounter) > 0) {
        $captionCounter .= " + ' '";
    }
    if($fancybox_caption_counter == 'on') {
        $captionCounter .= (" + (current.index + 1) + ' " . $fancybox_of . " ' + instance.group.length");
    }

    $captionSpacer = '';
    if(strlen($captionCounter) > 0 && ($fancybox_caption_text == 'on' || $fancybox_caption_image_name == 'on')) {
        $captionSpacer .= " + ' | '";
    }

    $loopGallery = '';
    if($fancybox_loop_gallery == 'on') {
        $loopGallery = ' loop: true,';
    }

    $keyboardNavi = '';
    if($fancybox_keyboard_navigation == 'off') {
        $keyboardNavi = ' keyboard: false,';
    }

    $arrowBtns = '';
    if($fancybox_button_arrows == 'off') {
        $arrowBtns = ' arrows: false,';
    }

    $infoBar = '';
    if($fancybox_counter == 'off') {
        $infoBar = ' infobar: false,';
    }

    $idleTime = '';
    if($fancybox_idle_time != '3') {
        $idleTime = ' idleTime: ' . $fancybox_idle_time . ',';
    }

    $imageProtect = '';
    if($fancybox_image_protect == 'on') {
        $imageProtect = ' protect: true,';
    }

    $imageAnimation = '';
    if($fancybox_animation_effect == 'false') {
        $imageAnimation = ' animationEffect: false,';
    } elseif ($fancybox_animation_effect != 'zoom') {
        $imageAnimation = ' animationEffect: \'' . $fancybox_animation_effect . '\',';
    }

    $imageAnimationDuration = '';
    if($fancybox_animation_duration != 366) {
        $imageAnimationDuration = ' animationDuration: ' . $fancybox_animation_duration . ',';
    }

    $imageTransition = '';
    if($fancybox_transition_effect == 'false') {
        $imageTransition = ' transitionEffect: false,';
    } elseif ($fancybox_transition_effect != 'fade') {
        $imageTransition = ' transitionEffect: \'' . $fancybox_transition_effect . '\',';
    }

    $imageTransitionDuration = '';
    if($fancybox_transition_duration != 366) {
        $imageTransitionDuration = ' transitionDuration: ' . $fancybox_transition_duration . ',';
    }

    $baseClasses = '';
    if($fancybox_base_class != '') {
        $baseClasses = ' baseClass: \'' . $fancybox_base_class . '\',';
    }

    $slideClasses = '';
    if($fancybox_slide_class != '') {
        $slideClasses = ' slideClass: \'' . $fancybox_slide_class . '\',';
    }

    $autoFullscreen = '';
    if($fancybox_auto_fullscreen == 'on') {
        $autoFullscreen = ' fullScreen: { autoStart: true },';
    }
    
    $touchMobile = '';
    if($fancybox_touch == 'on' ) {
        if($fancybox_touch_vertical == 'off' || $fancybox_touch_momentum == 'off') {
            $touchMobile .= 'vertical: ';
            if($fancybox_touch_vertical == 'off') {
                $touchMobile .= 'false';
            } else {
                $touchMobile .= 'true';
            }

            $touchMobile .= ', momentum: ';
            if($fancybox_touch_momentum == 'off') {
                $touchMobile .= 'false';
            } else {
                $touchMobile .= 'true';
            }
    
            $touchMobile = (' touch: {' . $touchMobile . '},');
        }
    } else {
        $touchMobile .= ' touch: false,';
    }
    
    $slideShow = '';
    if($fancybox_auto_slideshow == 'on' || $fancybox_slideshow_speed != 3000) {
        $slideShow .= 'autoStart: ';
        if($fancybox_auto_slideshow == 'on') {
            $slideShow .= 'true';
        } else {
            $slideShow .= 'false';
        }

        $slideShow .= ', speed: ' . $fancybox_slideshow_speed;
        $slideShow = ' slideShow: {' . $slideShow . '),';
    }
    
    $thumbnails = '';
    if($fancybox_thumbnail_autostart == 'on' || $fancybox_thumbnail_hide_close == 'off' || $fancybox_thumbnail_axis == 'x') {
        $thumbnails .= ' thumbs: {autoStart: ';
        if($fancybox_thumbnail_autostart == 'on') {
            $thumbnails .= 'true';
        } else {
            $thumbnails .= 'false';
        }

        $thumbnails .= ', hideOnClose: ';
        if($fancybox_thumbnail_hide_close == 'on') {
            $thumbnails .= 'true';
        } else {
            $thumbnails .= 'false';
        }

        $thumbnails .= ', axis: "' . $fancybox_thumbnail_axis . '"},';
    }

    $clickContent = '';
    if($fancybox_click_content != 'zoom') {
        $clickContent = ' clickContent: function(current, event) { return current.type === "image" ? ';
        if($fancybox_click_content == 'false') {
            $clickContent .= $fancybox_click_content;
        } else {
            $clickContent .= ('"' . $fancybox_click_content . '"');
        }
        $clickContent .= ' : false; },';
    }

    $clickSlide = '';
    if($fancybox_click_slide != 'close') {
        $clickSlide = ' clickSlide: ';
        if($fancybox_click_slide == 'false') {
            $clickSlide .= $fancybox_click_slide;
        } else {
            $clickSlide .= ('"' . $fancybox_click_slide . '"');
        }
        $clickSlide .= ',';
    }

    $clickOutside = '';
    if($fancybox_click_outside != 'close') {
        $clickOutside = ' clickOutside: ';
        if($fancybox_click_outside == 'false') {
            $clickOutside .= $fancybox_click_outside;
        } else {
            $clickOutside .= ('"' . $fancybox_click_outside . '"');
        }
        $clickOutside .= ',';
    }

    $dblClickContent = '';
    if($fancybox_dblclick_content != 'false') {
        $dblClickContent = ' dblclickContent: ';
        $dblClickContent .= ('"' . $fancybox_dblclick_content . '"');
        $dblClickContent .= ',';
    }

    $dblClickSlide = '';
    if($fancybox_dblclick_slide != 'false') {
        $dblClickSlide = ' dblclickSlide: ';
        $dblClickSlide .= ('"' . $fancybox_dblclick_slide . '"');
        $dblClickSlide .= ',';
    }

    $dblClickOutside = '';
    if($fancybox_dblclick_outside != 'false') {
        $dblClickOutside = ' dblclickOutside: ';
        $dblClickOutside .= ('"' . $fancybox_dblclick_outside . '"');
        $dblClickOutside .= ',';
    }

    $fancyBoxConfig = $loopGallery . $keyboardNavi . $arrowBtns . $infoBar . $idleTime . 
                        $imageProtect . $imageAnimation . $imageAnimationDuration . $imageTransition . 
                        $imageTransitionDuration . $baseClasses . $slideClasses . $autoFullscreen .
                        $touchMobile . $slideShow . $thumbnails . $clickContent . $clickSlide .
                        $clickOutside . $dblClickContent . $dblClickSlide . $dblClickOutside;

    $buttonLabeling = '';
    $buttonLabeling = "$.fancybox.defaults.i18n." . $langTag . " = {
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
    $scriptDeclarations = array("
        (function($) {
            $(document).ready(function() {
                ".$buttonLabeling."
                $.fancybox.defaults.lang = '" . $langTag . "';
                $('a.fancybox-gallery').fancybox({" . $fancyBoxConfig . "
                    buttons: [" . $buttons . "],
                    beforeShow: function(instance, current) {
                        if (current.type === 'image') {
                            var title = current.opts.\$orig.attr('title');
                            current.opts.caption = (title.length ? '<b class=\"fancyboxCounter\">'" . $captionCounter . $captionSpacer . " + '</b>' + title : '');
                        }
                    }
                });
            });
        })(jQuery);
    ");
} else {
    $scriptDeclarations = array();
}

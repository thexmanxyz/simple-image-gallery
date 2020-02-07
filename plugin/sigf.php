<?php
/**
 * @version      4.2.0
 * @package      Simple Image Gallery Fork
 * @author       Andreas Kar (thex) <andreas.kar@gmx.at>
 * @copyright    Copyright © 2020 Andreas Kar. All rights reserved.
 * @license      GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '2.5.0', 'ge')) {
    jimport('joomla.html.parameter');
}

class plgContentSigf extends JPlugin
{

    // Reference parameters
    public $plg_name             = "sigf";
    public $plg_tag              = "gallery";
    public $plg_version          = "4.2.0";
    public $plg_copyrights_start = "\n\n<!-- \"Simple Image Gallery Fork\" Plugin (v4.2.0) starts here -->\n";
    public $plg_copyrights_end   = "\n<!-- \"Simple Image Gallery Fork\" Plugin (v4.2.0) ends here -->\n\n";

    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        // Define the DS constant (b/c)
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }

    // Joomla 1.5
    public function onPrepareContent(&$row, &$params, $page = 0)
    {
        $this->renderSimpleImageGallery($row, $params, $page = 0);
    }

    // Joomla 2.5+
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        $this->renderSimpleImageGallery($row, $params, $page = 0);
    }

    // The main function
    public function renderSimpleImageGallery(&$row, &$params, $page = 0)
    {
        // API
        jimport('joomla.filesystem.file');
        $app = JFactory::getApplication();
        $document = JFactory::getDocument();

        if (version_compare(JVERSION, '4', 'ge')) {
            if ($app->isClient('administrator')) {
                return;
            }
            $jinput = $app->input;
            $tmpl = $jinput->getCmd('tmpl');
            $print = $jinput->getCmd('print');
            $format = $jinput->getCmd('format');
        } else {
            if ($app->isAdmin()) {
                return;
            }
            $tmpl = JRequest::getCmd('tmpl');
            $print = JRequest::getCmd('print');
            $format = JRequest::getCmd('format');
        }

        // Assign paths
        $sitePath = JPATH_SITE;
        $siteUrl  = JURI::root(true);

        if (version_compare(JVERSION, '2.5.0', 'ge')) {
            $pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name.'/'.$this->plg_name;
            $defaultImagePath = 'images';
        } else {
            $pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name;
            $defaultImagePath = 'images/stories';
        }

        // Check if plugin is enabled
        if (JPluginHelper::isEnabled('content', $this->plg_name) == false) {
            return;
        }

        // Bail out if the page format is not what we want
        $allowedFormats = array('', 'html', 'feed', 'json');
        if (!in_array($format, $allowedFormats)) {
            return;
        }

        // Simple performance check to determine whether plugin should process further
        if (strpos($row->text, $this->plg_tag) === false) {
            return;
        }

        // expression to search for
        $regex = "#{".$this->plg_tag."}(.*?){/".$this->plg_tag."}#is";

        // Find all instances of the plugin and put them in $matches
        preg_match_all($regex, $row->text, $matches);

        // Number of plugins
        $count = count($matches[0]);

        // Plugin only processes if there are any instances of the plugin in the text
        if (!$count) {
            return;
        }

        // Load the plugin language file the proper way
        JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);

        // Check for basic requirements
        if (!extension_loaded('gd') && !function_exists('gd_info')) {
            JError::raiseNotice('', JText::_('PLG_SIGF_NOTICE_01'));
            return;
        }
        if (!is_writable($sitePath.'/cache')) {
            JError::raiseNotice('', JText::_('PLG_SIGF_NOTICE_02'));
            return;
        }

        // ----------------------------------- Get plugin parameters -----------------------------------

        // Get plugin info
        $plugin = JPluginHelper::getPlugin('content', $this->plg_name);

        // Control external parameters and set variable for controlling plugin layout within modules
        if (!$params) {
            $params = class_exists('JParameter') ? new JParameter(null) : new JRegistry(null);
        }
        if (is_string($params)) {
            $params = class_exists('JParameter') ? new JParameter($params) : new JRegistry($params);
        }
        $parsedInModule = $params->get('parsedInModule');

        $pluginParams = class_exists('JParameter') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

        $show_copyright = $pluginParams->get('show_copyright', 'on');
        $galleries_rootfolder = ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder', $defaultImagePath);
        $popup_engine = 'jquery_fancybox';
        $mootools = $pluginParams->get('mootools', 'on');
        $jQueryHandling = $pluginParams->get('jQueryHandling', '1.12.4');
        $thb_template = 'Classic';
        $thb_width = (!is_null($params->get('thb_width', null))) ? $params->get('thb_width') : $pluginParams->get('thb_width', 200);
        $thb_height = (!is_null($params->get('thb_height', null))) ? $params->get('thb_height') : $pluginParams->get('thb_height', 160);
        $smartResize = 1;
        $jpg_quality = $pluginParams->get('jpg_quality', 80);
        $showcaptions = 0;
        $cache_expire_time = $pluginParams->get('cache_expire_time', 3600) * 60; // Cache expiration time in minutes
        $fancybox_cdn = $pluginParams->get('fancybox_cdn', 'on');
        $fancybox_version = $pluginParams->get('fancybox_version', '3.5.7');
        $fancybox_color_mode = $pluginParams->get('fancybox_color_mode', 'black-mode');
        $fancybox_icon_mode = $pluginParams->get('fancybox_icon_mode', 'default');
        $fancybox_text_mode = $pluginParams->get('fancybox_text_mode', 'default');
        $fancybox_idle_time = $pluginParams->get('fancybox_idle_time', '3');
        $fancybox_image_protect = $pluginParams->get('fancybox_image_protect', 'off');
        $fancybox_animation_effect = $pluginParams->get('fancybox_animation_effect', 'zoom');
        $fancybox_animation_duration = $pluginParams->get('fancybox_animation_duration', 366);
        $fancybox_transition_effect = $pluginParams->get('fancybox_transition_effect', 'fade');
        $fancybox_transition_duration = $pluginParams->get('fancybox_transition_duration', 366);
        $fancybox_language = $pluginParams->get('fancybox_language', 'default');
        $fancybox_image_target = $pluginParams->get('fancybox_image_target', '_self');
        $fancybox_loop_gallery = $pluginParams->get('fancybox_loop_gallery', 'off');
        $fancybox_keyboard_navigation = $pluginParams->get('fancybox_keyboard_navigation', 'on');
        $fancybox_mousewheel_navigation = $pluginParams->get('fancybox_mousewheel_navigation', 'on');
        $fancybox_auto_slideshow = $pluginParams->get('fancybox_auto_slideshow', 'off');
        $fancybox_slideshow_speed = $pluginParams->get('fancybox_slideshow_speed', 3000);
        $fancybox_thumbnail_autostart = $pluginParams->get('fancybox_thumbnail_autostart', 'off');
        $fancybox_thumbnail_hide_close = $pluginParams->get('fancybox_thumbnail_hide_close', 'on');
        $fancybox_thumbnail_axis = $pluginParams->get('fancybox_thumbnail_axis', 'y');
        $fancybox_thumbnail_border = $pluginParams->get('fancybox_thumbnail_border', 'default');
        $fancybox_click_content = $pluginParams->get('fancybox_click_content', 'zoom');
        $fancybox_click_slide = $pluginParams->get('fancybox_click_slide', 'close');
        $fancybox_click_outside = $pluginParams->get('fancybox_click_outside', 'close');
        $fancybox_dblclick_content = $pluginParams->get('fancybox_dblclick_content', 'false');
        $fancybox_dblclick_slide = $pluginParams->get('fancybox_dblclick_slide', 'false');
        $fancybox_dblclick_outside = $pluginParams->get('fancybox_dblclick_outside', 'false');
        $fancybox_counter = $pluginParams->get('fancybox_counter', 'on');
        $fancybox_auto_fullscreen = $pluginParams->get('fancybox_auto_fullscreen', 'off');
        $fancybox_gutter = $pluginParams->get('fancybox_gutter', 50);
        $fancybox_touch = $pluginParams->get('fancybox_touch', 'on');
        $fancybox_touch_vertical = $pluginParams->get('fancybox_touch_vertical', 'on');
        $fancybox_touch_momentum = $pluginParams->get('fancybox_touch_momentum', 'on');
        $fancybox_mobile_idle_time = $pluginParams->get('fancybox_mobile_idle_time', 'off');
        $fancybox_mobile_click_content = $pluginParams->get('fancybox_mobile_click_content', 'toggleControls');
        $fancybox_mobile_click_slide = $pluginParams->get('fancybox_mobile_click_slide', 'toggleControls');
        $fancybox_mobile_dblclick_content = $pluginParams->get('fancybox_mobile_dblclick_content', 'zoom');
        $fancybox_mobile_dblclick_slide = $pluginParams->get('fancybox_mobile_dblclick_slide', 'zoom');
        $fancybox_base_class = $pluginParams->get('fancybox_base_class', '');
        $fancybox_slide_class = $pluginParams->get('fancybox_slide_class', '');
        $fancybox_button_arrows = $pluginParams->get('fancybox_button_arrows', 'on');
        $fancybox_button_slideshow = $pluginParams->get('fancybox_button_slideshow', 'on');
        $fancybox_button_fullscreen = $pluginParams->get('fancybox_button_fullscreen', 'on');
        $fancybox_button_thumbs = $pluginParams->get('fancybox_button_thumbs', 'on');
        $fancybox_button_share = $pluginParams->get('fancybox_button_share', 'on');
        $fancybox_button_download = $pluginParams->get('fancybox_button_download', 'on');
        $fancybox_button_zoom = $pluginParams->get('fancybox_button_zoom', 'off');
        $fancybox_button_close = $pluginParams->get('fancybox_button_close', 'on');
        $fancybox_caption_image = $pluginParams->get('fancybox_caption_image', 'on');
        $fancybox_caption_counter = $pluginParams->get('fancybox_caption_counter', 'on');
        $fancybox_caption_text = $pluginParams->get('fancybox_caption_text', 'on');
        $fancybox_caption_image_name = $pluginParams->get('fancybox_caption_image_name', 'on');
        $fancybox_enlarge = $pluginParams->get('fancybox_enlarge', JText::_('PLG_SIGF_CLICK_TO_ENLARGE_IMAGE'));
        $fancybox_image = $pluginParams->get('fancybox_image', JText::_('PLG_SIGF_FB_IMAGE'));
        $fancybox_of = $pluginParams->get('fancybox_of', JText::_('PLG_SIGF_FB_OF'));
        $fancybox_viewing = $pluginParams->get('fancybox_viewing', JText::_('PLG_SIGF_YOU_ARE_VIEWING'));
        $fancybox_close = $pluginParams->get('fancybox_close', JText::_('PLG_SIGF_FB_CLOSE'));
        $fancybox_next = $pluginParams->get('fancybox_next', JText::_('PLG_SIGF_FB_NEXT'));
        $fancybox_prev = $pluginParams->get('fancybox_prev', JText::_('PLG_SIGF_FB_PREVIOUS'));
        $fancybox_error = $pluginParams->get('fancybox_error', JText::_('PLG_SIGF_FB_REQUEST_CANNOT_BE_LOADED'));
        $fancybox_play_start = $pluginParams->get('fancybox_play_start', JText::_('PLG_SIGF_FB_START_SLIDESHOW'));
        $fancybox_play_stop = $pluginParams->get('fancybox_play_stop', JText::_('PLG_SIGF_FB_PAUSE_SLIDESHOW'));
        $fancybox_full_screen = $pluginParams->get('fancybox_full_screen', JText::_('PLG_SIGF_FB_FULL_SCREEN'));
        $fancybox_thumbs = $pluginParams->get('fancybox_thumbs', JText::_('PLG_SIGF_FB_THUMBS'));
        $fancybox_download = $pluginParams->get('fancybox_download', JText::_('PLG_SIGF_FB_DOWNLOAD'));
        $fancybox_share = $pluginParams->get('fancybox_share', JText::_('PLG_SIGF_FB_SHARE'));
        $fancybox_zoom = $pluginParams->get('fancybox_zoom', JText::_('PLG_SIGF_FB_ZOOM'));
        $fancybox_hide_scrollbar = $pluginParams->get('fancybox_hide_scrollbar', 'on');

        // Advanced
        $memoryLimit = (int)$pluginParams->get('memoryLimit');
        if ($memoryLimit) {
            ini_set("memory_limit", $memoryLimit."M");
        }

        // Cleanups
        // Remove first and last slash if they exist
        if (substr($galleries_rootfolder, 0, 1) == '/') {
            $galleries_rootfolder = substr($galleries_rootfolder, 1);
        }
        if (substr($galleries_rootfolder, -1, 1) == '/') {
            $galleries_rootfolder = substr($galleries_rootfolder, 0, -1);
        }

        // Includes
        require_once dirname(__FILE__).'/'.$this->plg_name.'/includes/helper.php';

        // Other assignments
        $transparent = $pluginLivePath.'/includes/images/transparent.gif';

        // When used with K2 extra fields
        if (!isset($row->title)) {
            $row->title = '';
        }

        // Variable cleanups for K2
        if ($format == 'raw') {
            $this->plg_copyrights_start = '';
            $this->plg_copyrights_end = '';
        }

        // ----------------------------------- Prepare the output -----------------------------------

        // Process plugin tags
        if (preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER) > 0) {

            // start the replace loop
            foreach ($matches[0] as $key => $match) {
                $tagcontent = preg_replace("/{.+?}/", "", $match);
                $tagcontent = str_replace(array('"','\'','`'), array('&quot;','&apos;','&#x60;'), $tagcontent); // Address potential XSS attacks
                $tagcontent = trim(strip_tags($tagcontent));

                if (strpos($tagcontent, ':')!==false) {
                    $tagparams        = explode(':', $tagcontent);
                    $galleryFolder    = $tagparams[0];
                } else {
                    $galleryFolder    = $tagcontent;
                }

                // HTML & CSS assignments
                $srcimgfolder = $galleries_rootfolder.'/'.$galleryFolder;
                $gal_id = substr(md5($key.$srcimgfolder), 1, 10);

                // Render the gallery
                $SIGFHelper = new SimpleImageGalleryForkHelper();

                $SIGFHelper->srcimgfolder = $srcimgfolder;
                $SIGFHelper->thb_width = $thb_width;
                $SIGFHelper->thb_height = $thb_height;
                $SIGFHelper->smartResize = $smartResize;
                $SIGFHelper->jpg_quality = $jpg_quality;
                $SIGFHelper->cache_expire_time = $cache_expire_time;
                $SIGFHelper->gal_id = $gal_id;
                $SIGFHelper->format = $format;

                $gallery = $SIGFHelper->renderGallery();

                if (!$gallery) {
                    JError::raiseNotice('', JText::_('PLG_SIGF_NOTICE_03').' '.$srcimgfolder);
                    continue;
                }

                // CSS & JS includes: Append head includes, but not when we're outputing raw content (like in K2)
                if ($format == '' || $format == 'html') {

                    // Initialize variables
                    $relName = '';
                    $extraClass = '';
                    $extraWrapperClass = '';
                    $legacyHeadIncludes = '';
                    $customLinkAttributes = '';

                    $popupPath = "{$pluginLivePath}/includes/js/{$popup_engine}";
                    $popupRequire = dirname(__FILE__).'/'.$this->plg_name.'/includes/js/'.$popup_engine.'/popup.php';

                    if (file_exists($popupRequire) && is_readable($popupRequire)) {
                        require $popupRequire;
                    }

                    if($mootools == 'on') {
                        if (version_compare(JVERSION, '4', 'ge')) {
                            // Do nothing
                        } elseif (version_compare(JVERSION, '2.5.0', 'ge')) {
                            JHtml::_('behavior.framework');
                        } else {
                            JHtml::_('behavior.mootools');
                        }
                    }

                    if (count($stylesheets)) {
                        foreach ($stylesheets as $stylesheet) {
                            if (substr($stylesheet, 0, 4) == 'http' || substr($stylesheet, 0, 2) == '//') {
                                $document->addStyleSheet($stylesheet);
                            } else {
                                $document->addStyleSheet($popupPath.'/'.$stylesheet);
                            }
                        }
                    }
                    if (count($stylesheetDeclarations)) {
                        foreach ($stylesheetDeclarations as $stylesheetDeclaration) {
                            $document->addStyleDeclaration($stylesheetDeclaration);
                        }
                    }

                    if (strpos($popup_engine, 'jquery_') !== false && $jQueryHandling != 0) {
                        if (version_compare(JVERSION, '3.0', 'ge')) {
                            JHtml::_('jquery.framework');
                        } else {
                            $document->addScript('https://cdn.jsdelivr.net/npm/jquery@'.$jQueryHandling.'/dist/jquery.min.js');
                        }
                    }

                    if (count($scripts)) {
                        foreach ($scripts as $script) {
                            if (substr($script, 0, 4) == 'http' || substr($script, 0, 2) == '//') {
                                $document->addScript($script);
                            } else {
                                $document->addScript($popupPath.'/'.$script);
                            }
                        }
                    }
                    if (count($scriptDeclarations)) {
                        foreach ($scriptDeclarations as $scriptDeclaration) {
                            $document->addScriptDeclaration($scriptDeclaration);
                        }
                    }

                    if ($legacyHeadIncludes) {
                        if($show_copyright == 'on') {
                            $document->addCustomTag($this->plg_copyrights_start.$legacyHeadIncludes.$this->plg_copyrights_end);
                        } else {
                            $document->addCustomTag($legacyHeadIncludes);
                        }
                    }

                    if ($extraClass) {
                        $extraClass = ' '.$extraClass;
                    }

                    if ($extraWrapperClass) {
                        $extraWrapperClass = ' '.$extraWrapperClass;
                    }

                    if ($customLinkAttributes) {
                        $customLinkAttributes = ' '.$customLinkAttributes;
                    }

                    $pluginCSS = $SIGFHelper->getTemplatePath($this->plg_name, 'css/template.css', $thb_template);
                    $pluginCSS = $pluginCSS->http;
                    $document->addStyleSheet($pluginCSS.'?v='.$this->plg_version);
                }

                // Print output
                $isPrintPage = ($tmpl == "component" && $print !== false) ? true : false;

                // Fetch the template
                ob_start();
                $templatePath = $SIGFHelper->getTemplatePath($this->plg_name, 'default.php', $thb_template);
                $templatePath = $templatePath->file;
                include $templatePath;

                if($show_copyright == 'on') {
                    $getTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
                } else {
                    $getTemplate = ob_get_contents();
                }

                ob_end_clean();

                // Output
                $plg_html = $getTemplate;

                // Do the replace
                $row->text = preg_replace("#{".$this->plg_tag."}".preg_quote($tagcontent)."{/".$this->plg_tag."}#s", $plg_html, $row->text);
            }
        }
    }
}

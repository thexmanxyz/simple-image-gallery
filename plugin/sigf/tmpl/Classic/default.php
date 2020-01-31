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

$targetAttr = '';
if($fancybox_image_target != '_self') {
    $targetAttr = ' target="' . $fancybox_image_target . '"';
}
?>

<ul id="sigfId<?php echo $gal_id; ?>" class="sigfContainer sigfClassic<?php echo $extraWrapperClass; ?>">
    <?php foreach($gallery as $count=>$photo): ?>
    <li class="sigfThumb">
        <a href="<?php echo $photo->sourceImageFilePath; ?>" class="sigfLink<?php echo $extraClass; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;" title="<?php echo JText::_('PLG_SIGF_YOU_ARE_VIEWING').' '.$photo->filename; ?>" data-thumb="<?php echo $photo->thumbImageFilePath; ?>"<?php echo $targetAttr; ?><?php echo $customLinkAttributes; ?>>
            <img class="sigfImg" src="<?php echo $transparent; ?>" alt="<?php echo JText::_('PLG_SIGF_CLICK_TO_ENLARGE_IMAGE').' '.$photo->filename; ?>" title="<?php echo JText::_('PLG_SIGF_CLICK_TO_ENLARGE_IMAGE').' '.$photo->filename; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;background-image:url('<?php echo $photo->thumbImageFilePath; ?>');" />
        </a>
    </li>
    <?php endforeach; ?>
    <li class="sigfClear">&nbsp;</li>
</ul>

<?php if($isPrintPage): ?>
<!-- Print output -->
<div class="sigfPrintOutput">
    <?php foreach($gallery as $count => $photo): ?>
    <img src="<?php echo $photo->thumbImageFilePath; ?>" alt="<?php echo $photo->filename; ?>" />
    <?php if(($count+1)%3 == 0): ?><br /><br /><?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

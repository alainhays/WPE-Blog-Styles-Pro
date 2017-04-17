<?php
/**
 * BSP template: Main Admin Area
 *
 * This template is for the main admin area settings and controls
 *
 * @since  1.1.0
 * @package WPEXPANSE_Blog_Styles_Pro
 */
?>

<div id="top-actions-bar">
<div id="save-active-file" class="btn-elem"><i class="fa fa-check"></i> Save </div>
<div class="flex-padding"></div>
<div id="bsp-advanced-mode" class="btn-elem"><i class="fa fa-code"></i> Advanced </div>
<div id="bsp-easy-mode" class="btn-elem active"><i class="fa fa-magic"></i> Easy </div>
</div> <!-- Top Actions End -->
<div id="bsp-admin-content">
<!-- Easy options -->
<div id="bsp-easy-container">
<div class="col-100">
<label> Select a style: </label><div id="easy-folder-selector"></div>
</div>
</div>
<!-- Advanced code editor options -->
<div id="bsp-advanced-container">
<div id="wpe-bsp-main">
<div id="tab-container"></div>
<div id="BSP-main-editor"></div>
<div id="wpe-bsp-quick-Help" style="padding:20px;">
    <h3> Reference Guide to BSP Conventions </h3>
    <ul>
        <li>Wrap your classes like this ("<b>.bsp-</b>"example-class-name"<b>-bsp</b>") to add the class to quick insert for any post type.</li>
        <li>The main wrapper must have "<b>#BSP-init</b>" to render on post pages.</li>
        <li> Including "<b>#tinymce.wp-editor</b>" with the main wrapper class will allow your styles to render in the visual tab. (Tiny-Mce) </li>
    </ul>
     </div>
</div>
<div id="list-container">
</div>
</div>
</div> <!-- Admin Content End -->
<!-- initial loading screen -->
<div id="bsp-loading-container">
<div id="bsp-loader-icon">
<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
</div>
</div>
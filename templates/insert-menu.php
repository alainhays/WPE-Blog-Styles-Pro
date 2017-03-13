<?php
/**
 * BSP template: Posts Insert Menu
 *
 * This template is for the posts area settings and controls
 *
 * @since  1.1.3
 * @package WPEXPANSE_Blog_Styles_Pro
 */
?>

<div> Select one or more styles: </div>
<div style="padding-top:5px;">
    <select id="wpe-bsp-select-classes" multiple="multiple">
        <?php
        for($i = 0; $i < $class_total; $i++){
            echo '<option value="'.esc_html($data[$i]).'">'.esc_html(str_replace(array("-", "bsp"), " ", $data[$i])).'</option>';
        }
        ?>
    </select>
</div>
<div> Select a base HTML element: </div>
<div style="padding-top:5px;">
    <select id="wpe-bsp-select-directive">
    <option value="<div class=*xxx>***</div>" selected="selected">div - Basic block container </option>
    <option value="<span class=*xxx>***</span>">span - Basic inline container </option>
    <option value="<p class=*xxx>***</p>">p - Paragraph container </option>
    <option value="class=*yyy">class - Wrapped in class attribute </option>
    <option value="*zzz">INSERT STYLES ONLY</option>
    <option value="<h1 class=*xxx>***</h1>">h1 - Header tag largest </option>
    <option value="<h2 class=*xxx>***</h2>">h2 - Header tag larger </option>
    <option value="<h3 class=*xxx>***</h3>">h3 - Header tag medium </option>
    <option value="<h4 class=*xxx>***</h4>">h4 - Header tag small </option>
    <option value="<h5 class=*xxx>***</h5>">h5 - Header tag smallest </option>
    </select>
</div>

<div id="bsp-insert-code"> Insert </div>
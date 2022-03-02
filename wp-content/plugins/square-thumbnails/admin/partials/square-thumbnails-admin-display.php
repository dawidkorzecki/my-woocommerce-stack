<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ilmdesigns.com/
 * @since      1.0.0
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/admin/partials
 */

?>
<h1>Square Thumbnails Plugin v. 1.1.0</h1>

<p>Square Thumbails is a plugin for creating square thumbnails from images, without cropping them. It is like when you set background image in CSS to contain.</p>
<h2>Available options</h2>
<ul style="list-style-type: disc;padding-left:30px;">
    <li>Horizontal align of the image in the frame</li>
    <li>Vertical align of the image in the frame</li>
    <li>Creating all intermediary sizes, even if the size of the original image is smaller than some of the thumbnail sizes </li>
    <li>Option to select if the original image should be included in a square or to keep it in original</li>    
    <li>Set background color of the frame or check to automatically read the color from the image, point (0,0)</li>
</ul>


<p>If you want help, please click this <a href="">link</a></p>

<div style="color:navy;font-size:16px;margin-top:30px;">Thank you for using Square Thumbnails Plugin!</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<hr>
<h1>Settings</h1>
<hr>
<form action="upload.php?page=square-thumbnails-admin-page" method="post">
    
    <h2>Apply also to the original image 
    <input value="1" type="checkbox" id="sqt_tooriginal" <?php checked(get_option($this->option_name.'_tooriginal'), 1, true); ?>>
    </h2>
    <i>(If not checked, it will be applied only to thumbnails)</i>    
    <hr>
    <h2>Create missing sizes 
    <input value="1" type="checkbox" id="sqt_addallsizes" <?php checked(get_option($this->option_name.'_addallsizes'), 1, true); ?>>
    </h2>
    <i>(If checked, it will create thumbnails for all existing sizes, even if the image is smaller than some sizes. For example, it will create "large" thumbnail even if the original image is smaller than large.)</i>    
    <hr>
    <h2>Align</h2>
    <div class="sqt-align">
        <label>Horizontal 
        <select id="sqt_halign">
            <option value="left" <?php selected(get_option($this->option_name.'_halign'), 'left', true); ?>>Left</option>
            <option value="center" <?php selected(get_option($this->option_name.'_halign'), 'center', true); ?>>Center</option>
            <option value="right" <?php selected(get_option($this->option_name.'_halign'), 'right', true); ?>>Right</option>
        </select>
        </label
        <br>
        <label>`
        Vertical        
        <select id="sqt_valign">
            <option value="top" <?php selected(get_option($this->option_name.'_valign'), 'top', true); ?>>Top</option>
            <option value="middle" <?php selected(get_option($this->option_name.'_valign'), 'middle', true); ?>>Middle</option>
            <option value="bottom" <?php selected(get_option($this->option_name.'_valign'), 'bottom', true); ?>>Bottom</option>
        </select>
        </label>
    </div>
    <br>
    <br>
    <br>
    <h2>Background</h2>
    <label style="line-height: 40px;">Background color    
    <input id="sqt_bgcolor" value="<?php echo get_option($this->option_name.'_bgcolor');?>" class="cpa-color-picker" style="display:none;"></label> 
   or 
    Get the color from image <input value="1" type="checkbox" id="sqt_getimcolor" <?php checked(get_option($this->option_name.'_getimcolor'), 1, true); ?>>
    <br>

    <br>
    <br>
    <br>    
    
    <input  type="button" value="Save" id="sqt-save-settings">    
</form>


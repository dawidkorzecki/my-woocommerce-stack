<?php

namespace FSVendor;

/**
 *
 */
?><div class="alignleft actions">
    <select name="flexible_shipping_integration_filter">
        <option value=""><?php 
\_e('All manifests', 'flexible-shipping');
?></option>
        <optgroup label="<?php 
\_e('Integration', 'flexible-shipping');
?>">
            <?php 
foreach ($integrations as $key => $val) {
    ?>
                <option value="<?php 
    echo $key;
    ?>" <?php 
    echo $key == $integration ? 'selected' : '';
    ?>><?php 
    echo $val;
    ?></option>
            <?php 
}
?>
        </optgroup>
    </select>
</div>
<?php 

<?php

namespace FSVendor;

/**
 *
 */
?><table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>
            </th>
            <th>
                <?php 
\_e('Shipment', 'flexible-shipping');
?>
            </th>
            <th>
                <?php 
\_e('Order', 'flexible-shipping');
?>
            </th>
        </tr>
    </thead>

    <tbody id="the-list">
        <?php 
$count = 0;
?>
        <?php 
foreach ($shipments as $shipment) {
    ?>
            <?php 
    $count++;
    $order = $shipment->get_order();
    if (\version_compare(\WC_VERSION, '2.7', '<')) {
        $order_id = $order->id;
    } else {
        $order_id = $order->get_id();
    }
    ?>
            <tr>
                <td>
                    <?php 
    echo $count;
    ?>
                </td>
                <td>
                    <a href="<?php 
    echo $shipment->get_order_metabox_url();
    ?>"><?php 
    echo $shipment->get_tracking_number();
    ?></a>
                </td>
                <td>
                    <a href="<?php 
    echo \admin_url('post.php?action=edit&post=' . $order_id);
    ?>"><?php 
    echo $order->get_order_number();
    ?></a>
                </td>
            </tr>
        <?php 
}
?>
    </tbody>

    <tfoot>
        <tr>
            <th>
            </th>
            <th>
                <?php 
\_e('Shipment', 'flexible-shipping');
?>
            </th>
            <th>
                <?php 
\_e('Order', 'flexible-shipping');
?>
            </th>
        </tr>
    </tfoot>

</table><?php 

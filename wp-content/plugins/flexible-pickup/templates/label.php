<html>
<head>
    <title>
        <?php echo sprintf( __( 'Flexible Pickup Label for shipment: %s', 'flexible-pickup' ), $shipment->get_tracking_number() ); ?>
    </title>
    <style>
        @page {
        <?php if ( $pdf ) : ?>
            sheet-size: A4-P;
        <?php else : ?>
            size: A4 portrait;
        <?php endif; ?>
        }
        table {
            border-collapse: collapse;
            min-width: 350px;
        }
        td    {
            padding: 6px;
            border: 1px solid;
        }
        div.receiver {
            margin: 10px;
            font-size: 1.5em;
        }
        div.pickup_point {
            margin: 10px;
        }
        div.pickup_point .title {
            font-weight: bold;
        }
        div.cod {
            margin: 10px;
            font-size: 1.5em;
        }
    </style>
    <script type="text/javascript">
        window.print();
    </script>
</head>
<body>
    <table>
        <tr>
            <td>
                <div><?php _e( 'Receiver:', 'flexible-pickup' ); ?></div>
                <div class="receiver">
                    <div class="name">
                        <span class="first_name">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                            <?php echo $order->shipping_first_name; ?>
                            <?php else : ?>
                                <?php echo $order->get_shipping_first_name(); ?>
                            <?php endif; ?>
                        </span>
                        <span class="last_name">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                            <?php echo $order->shipping_last_name; ?>
                            <?php else : ?>
	                            <?php echo $order->get_shipping_last_name(); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="company">
                        <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
                            <?php echo $order->shipping_company; ?>
                        <?php else : ?>
                            <?php echo $order->get_shipping_company(); ?>
                        <?php endif; ?>
                    </div>
                    <div class="address">
                        <span class="address_1">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
                                <?php echo $order->shipping_address_1; ?>
                            <?php else : ?>
                                <?php echo $order->get_shipping_address_1(); ?>
                            <?php endif; ?>
                        </span>
                        <span class="address_2">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                            <?php echo $order->shipping_address_2; ?>
                            <?php else : ?>
	                            <?php echo $order->get_shipping_address_2(); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="postal_code_city">
                        <span class="postal_code">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                            <?php echo $order->shipping_postcode; ?>
                            <?php else : ?>
	                            <?php echo $order->get_shipping_postcode(); ?>
                            <?php endif; ?>
                        </span>
                        <span class="city">
                            <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                            <?php echo $order->shipping_city; ?>
                            <?php else : ?>
	                            <?php echo $order->get_shipping_city(); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="country">
                        <?php if ( version_compare( WC_VERSION, '2.7', '<' ) ) : ?>
	                        <?php echo WC()->countries->countries[$order->shipping_country]; ?>
                        <?php else : ?>
                            <?php echo WC()->countries->countries[$order->get_shipping_country()]; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div><?php _e( 'Pickup point:', 'flexible-pickup' ); ?></div>
                <div class="pickup_point">
                    <div class="title"><?php echo $pickup_point['title']; ?></div>
                    <div class="company"><?php echo $pickup_point['company']; ?></div>
                    <div class="address">
                        <span class="address">
                            <?php echo $pickup_point['address']; ?>
                        </span>
                        <span class="address_2">
                            <?php echo $pickup_point['address_2']; ?>
                        </span>
                    </div>
                    <div class="postal_code_city">
                        <span class="postal_code"><?php echo $pickup_point['postal_code']; ?></span>
                        <span class="city"><?php echo $pickup_point['city']; ?></span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="order">
                    <?php echo sprintf( __( 'Order: %s', 'flexible-pickup' ), $order->get_order_number() ); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="shipment">
	                <?php echo sprintf( __( 'Shipment: %s', 'flexible-pickup' ), $shipment->get_tracking_number() ); ?>
                </div>

            </td>
        </tr>
        <?php if ( $shipment->get_meta( '_cod', true ) == '1' ) : ?>
            <tr>
                <td>
                    <div class="cod">
				        <?php echo __( 'COD Amount:', 'flexible-pickup' ); ?>
                        <?php
                            $currency = '';
	                        if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
		                        $order->order_currency;
	                        }
	                        else {
		                        $order->get_currency();
                            }
                        ?>
                        <span class="amount"><?php echo wc_price( floatval( $shipment->get_meta( '_cod_amount' ) ), array( 'currency' => $currency ) ); ?></span>
                    </div>
                </td>
            </tr>
        <?php endif;?>
    </table>
</body>
</html>
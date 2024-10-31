<?php

$settings_link = 'admin.php?page=wc-settings&tab=shipping&section=wp_pargo';

?>

<!-- Page Header -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2 align="center"><img class="img img-responsive"
                                    src="<?php echo PARGOPLUGINURL.'assets/images/pargo_logo.png'; ?>"/></h2>
        </div>
    </div>
</div>
<!--/ end of Page Header -->

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 pargo-admin-ajax-notification">
            <!-- Notifications to display here -->
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-xs-9">

            <!-- The main content container -->
            <div class="container-fluid" style="background:#ddd;margin-bottom: 40px;">

                <!-- Content container header -->
                <div class="row">
                    <div class="col-xs-12" style="margin-bottom: 20px;">
                        <h1>Pargo Help</h1>
                        <strong>
                            The Pargo Wordpress plugin now makes use of WooCommerce Shipping Zones. <br/>
                            To make use of Pargo for your shipping, it must be added as a Shipping Method under at least
                            one existing Shipping Zone you have created or a new Shipping Zone you can create.
                        </strong>
                    </div>
                </div>

                <!-- Content Container Content-Steps -->
                <div class="row">
                    <div class="col-xs-12">

                        <h3 style="margin-bottom: 20px;">Setting up the Pargo Shipping Zone:</h3>

                        <ol>
                            <li style="margin-bottom: 30px;">
                                <p>Go to: <strong> Woocommerce </strong> in the Admin Menu and select <strong>
                                        Settings </strong></p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_menu.jpg'; ?>"
                                     style="max-height:250px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>On the Woocommerce Settings page, Click the <strong> Shipping </strong> tab.</p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_tabs.jpg'; ?>"
                                     style="max-height:50px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>Then click the <strong>Add Shipping Zone</strong> button to create a new <strong>
                                        Shipping Zone </strong>.</p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_add_shipping_zone.jpg'; ?>"
                                     style="max-height:50px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>Click the <strong> Add Shipping Method </strong> button to add a <strong> Shipping
                                        Method </strong>.</p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_add_shipping_method.jpg'; ?>"
                                     style="max-height:140px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>From the drop-down menu, select the <strong> Pargo </strong> option and click the
                                    <strong> Add Shipping Method </strong> button.</p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_pargo_shipping_method.jpg'; ?>"
                                     style="max-height:200px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>Pargo will now display as a <strong> Shipping Method </strong></p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_pargo_shipping_method_selected.jpg'; ?>"
                                     style="max-height:140px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>The Pargo Shipping method will now also be part of your <strong> Shipping
                                        Zone </strong></p>
                                <img class="img img-responsive"
                                     src="<?php echo PARGOPLUGINURL.'assets/images/help/woocommerce_pargo_shipping_zone.jpg'; ?>"
                                     style="max-height:140px;"/>
                            </li>
                            <li style="margin-bottom: 30px;">
                                <p>Please ensure that you click the <strong> Save changes </strong></p>
                            </li>
                        </ol>

                    </div>
                </div>


            </div>

            <div class="container-fluid" style="background:#ddd;padding-top: 20px;">
                <div class="row">
                    <div class="col-xs-12">
                        <p>For more info on howto use WooCommerce Shipping Zones, goto:</p>
                        <p><a href="https://docs.woocommerce.com/document/setting-up-shipping-zones/" target="_blank">https://docs.woocommerce.com/document/setting-up-shipping-zones/</a>
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- the right side pargo-portal box -->
        <div class="col-xs-3">

            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox ">
                    <div class="misc-pub-section">
                        <p><a href="https://mypargo.pargo.co.za/mypargo" target="_blank"
                              style="background: #ffeb3b;padding: 10px 10px;display: block;text-align: center;color: #333;text-decoration: none;"><b>Login</b>
                                to your myPargo account</a></p>
                        <p>Email Pargo: <a href="mailto:info@pargo.co.za">info@pargo.co.za</a></p>
                        <p>Call Pargo: 021 447 3636</p>
                        <p>About Pargo: <a href="https://pargo.co.za/faq/" target="_blank">FAQ</a></p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

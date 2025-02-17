<?php
include_once '../woocommerce/woocommerce.php';
global $wpdb;
$table = $wpdb->prefix.'postmeta';
$data = $wpdb->get_row("SELECT * FROM  $table WHERE  meta_key = 'pargo_consent'");
if (count((array) $data->meta_key) == 0) {
    header('Location: admin.php?page=pargo-shipping-optin');
}
$pargo_plugin_dir_path = dirname(__FILE__);
$settings_link = 'admin.php?page=wc-settings&tab=shipping&section=wp_pargo';

?>

    <div class="wrap">
    </div>
    <h2><?php echo '<img src="'.plugins_url('images/pargo_logo.png', __FILE__).'" >'; ?><br/></h2>
    <div class="wrap">
        <div id="poststuff">
            <div id="post-body" class=" columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div class="misc-pub-section" style="background:#ddd;">
                        <h1>Pargo Admin</h1>
                        <strong>The Pargo Wordpress plugin now makes use of WooCommerce Shipping Zones. To make use of
                            Pargo for your shipping, it must be added as a Shipping Method under at least one existing
                            Shipping Zone you have created or a new Shipping Zone you can create.</strong>
                        <h3>Plugin use steps:</h3>

                        <p>1. Go to: <strong>WooCommerce &gt; Settings &gt; Shipping &gt; Shipping Zones</strong>.</p>

                        <p>2. <strong> Click</strong> the <strong>Add shipping zone</strong> button at the top - or
                            click on an Existing Shipping Zone to edit it.</p>

                        <p>3. Click the <strong>Add shipping methods</strong> button and <strong>Select</strong> Pargo
                            in the drop-down menu.</p>

                        <p>4. <strong>Edit the Main Pargo Shipping Settings:</strong> Under Woocommerce settings &gt;
                            Shipping, select the <a href="<?php echo $settings_link; ?>">"Pargo"</a> tab. </p>

                        <p>5. <strong>Save changes</strong>.</p>

                        <p>For more info on howto use WooCommerce Shipping Zones, goto:</p>

                        <p><a href="https://docs.woocommerce.com/document/setting-up-shipping-zones/" target="_blank">https://docs.woocommerce.com/document/setting-up-shipping-zones/</a>
                        </p>

                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">

                    <div class="postbox ">

                        <div class="misc-pub-section">

                            <p><a href="https://pargo.co.za/mypargo" target="_blank"
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

<?php ?>
<?php

include_once PARGOPATH."includes/controllers/class-wp-pargo-shipping-method.php";
$pargo_shipping_method = new Wp_Pargo_Shipping_Method();
$pargo_url = $pargo_shipping_method->get_option('pargo_url');
$pargo_username = $pargo_shipping_method->get_option('pargo_username');
$pargo_password = $pargo_shipping_method->get_option('pargo_password');
$pargo_map_token = $pargo_shipping_method->get_option('pargo_map_token');
$pargo_map_is_production = $pargo_shipping_method->get_option('pargo_map_is_production');
$pargo_opt = $pargo_shipping_method->get_option('pargo_opt');

?>

<?php add_thickbox(); ?>

<!-- Page Header -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2 align="center"><img class="img-responsive"
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

        <div class="col-md-9">

            <!-- Pargo Opt-In Section -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-offset-3" style="padding-left:8px;">
                            <h2>PARGO OPT-IN</h2>
                        </div>

                        <form class="form-horizontal" id="pargo_optin"
                              action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                            <div class="form-group">
                                <label for="pargo_opt" class="control-label col-xs-3">Pargo Option</label>
                                <div class="col-xs-9">
                                    <label class="radio-inline">
                                        <input
                                                type="radio"
                                                name="pargo_opt"
                                                value="true"

                                            <?php
                                            if ($pargo_opt || $pargo_opt === "true") {
                                                echo " checked='checked'";
                                            }
                                            ?>
                                        >
                                        Opt-In
                                    </label>
                                    <label class="radio-inline">
                                        <input
                                                type="radio"
                                                name="pargo_opt"
                                                value="false"
                                            <?php
                                            if (!$pargo_opt || $pargo_opt === "false") {
                                                echo " checked='checked'";
                                            }
                                            ?>
                                        >
                                        Opt-Out
                                    </label>
                                    <span id="pargo_optHelpBlock" class="help-block">Opt-in to help Pargo better understand our clients. This is not a required field.</span>
                                </div>
                            </div>

                            <!-- Wordpress hidden fields -->
                            <input type="hidden" name="action" id="pargo_optin_action" value="pargo_optin"/>

                            <div class="form-group row">
                                <div class="col-xs-offset-3 col-xs-9">
                                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
            <!--/ End OF Pargo Opt-In Section -->

            <!-- Pargo Credentials Section -->
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">

                        <div class="col-md-offset-3" style="padding-left:8px;">
                            <h2>PARGO CREDENTIALS</h2>
                        </div>

                        <form class="form-horizontal" id="pargo_account_credentials"
                              action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                            <div class="form-group">
                                <label for="pargo_username" class="control-label col-xs-3">Pargo Username</label>
                                <div class="col-xs-6">
                                    <input
                                            id="pargo_username"
                                            name="pargo_username"
                                            placeholder="Pargo Username"
                                            type="text"
                                            aria-describedby="pargo_usernameHelpBlock"
                                            required="required"
                                            class="form-control"
                                            value="<?php echo $pargo_username; ?>"
                                    >
                                    <span id="pargo_usernameHelpBlock" class="help-block">Please enter your Pargo Account Username</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pargo_password" class="control-label col-xs-3">Pargo Password</label>
                                <div class="col-xs-6">
                                    <input
                                            id="pargo_password"
                                            name="pargo_password"
                                            placeholder="Pargo Password"
                                            type="password"
                                            aria-describedby="pargo_passwordHelpBlock"
                                            required="required"
                                            class="form-control"
                                            value="<?php echo $pargo_password; ?>"
                                    >
                                    <span id="pargo_passwordHelpBlock" class="help-block">Please enter your Pargo Account Password</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pargo_api_url" class="control-label col-xs-3">Pargo API Url</label>
                                <div class="col-xs-6">
                                    <input
                                            id="pargo_url"
                                            name="pargo_url"
                                            placeholder="Pargo API Url"
                                            type="text"
                                            aria-describedby="pargo_api_urlHelpBlock"
                                            required="required"
                                            class="form-control"
                                            value="<?php echo $pargo_url; ?>"
                                    >
                                    <span id="pargo_api_urlHelpBlock" class="help-block">Please enter your Pargo Account API Url</span>
                                </div>
                                <div class="col-xs-3">
                                    <a class="button button-secondary" id="pargo_verify_account_button" href="#">Verify
                                        Credentials</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pargo_map_token" class="control-label col-xs-3">Pargo Map Token</label>
                                <div class="col-xs-6">
                                    <input
                                            id="pargo_map_token_field"
                                            name="pargo_map_token"
                                            placeholder="Pargo Map Token"
                                            type="text"
                                            class="form-control"
                                            aria-describedby="pargo_map_tokenHelpBlock"
                                            value="<?php echo $pargo_map_token; ?>"
                                    >
                                    <span id="select_pargo_location_button" class="help-block">Please enter your Pargo Account Map Token</span>
                                </div>
                                <div class="col-xs-3">
                                    <a id="pargo_map_test_button" class="button button-secondary thickbox">
                                        Test Pargo Map
                                    </a>
                                </div>
                            </div>
                            <input type="hidden" name="action" id="pargo_account_credentials_action"
                                   value="pargo_account_credentials">
                            <div class="form-group row">
                                <div class="col-xs-offset-3 col-xs-9">
                                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
            <!--/ End OF Pargo Credentials Section -->
        </div>
        <!-- Right Side-bar -->
        <div class="col-md-3">
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
        <!--/ End OF - Right Side-bar -->
    </div>
</div>

<script>
    jQuery(document).ready(function () {

        jQuery("#pargo_account_credentials").on("submit", function (e) {
            e.preventDefault();
            jQuery.ajax({
                url: jQuery(this).attr("action"),
                method: "POST",
                data: jQuery(this).serialize(),
                success: function (data) {
                    jQuery(".pargo-admin-ajax-notification").html(data);
                }
            });

        });


        jQuery("#pargo_verify_account_button").on("click", function (e) {
            e.preventDefault();

            let url = "<?php echo admin_url('admin-ajax.php'); ?>";

            let credentials = {
                "username": jQuery('#pargo_username').val(),
                "password": jQuery('#pargo_password').val(),
                "api_url": jQuery('#pargo_url').val(),
                "action": 'pargo_verify_credentials'
            };

            jQuery.ajax({
                url: url,
                method: "POST",
                data: credentials,
                success: function (data) {
                    jQuery(".pargo-admin-ajax-notification").html(data);
                }
            });

        });


        jQuery("#pargo_optin").on("submit", function (e) {
            e.preventDefault();
            // alert("Submitting form");

            jQuery.ajax({
                url: jQuery(this).attr("action"),
                method: "POST",
                data: jQuery(this).serialize(),
                success: function (data) {
                    jQuery(".pargo-admin-ajax-notification").html(data);
                }
            });

        });

        jQuery("#pargo_map_test_button").on('click', function (e) {
            e.preventDefault();

            let map_token = jQuery("#pargo_map_token_field").val();
            let staging = <?php echo '"' . $pargo_map_is_production . '"'; ?> === "no"? ".staging" : "";

            let url = "https://map" + staging +".pargo.co.za/?token=" + map_token + "&TB_iframe=true&width=1000&height=800";
            jQuery(this).attr("href", url);

        });

    });
</script>

/** PARGO 2.4.0 Wordpress Plugin Javascript File **/
const PARGO_PROCESS_TYPE_W2P = "W2P";
const PARGO_PROCESS_TYPE_W2D = "W2D";
const PARGO_FADE_SPEED = 300;


function setupProcessTypeSelect($) {
    // if the dropdown does not exist, default to w2p, this means w2d has been disabled
    if ($('#pargo-select-delivery-type').length == 0) {
        sessionStorage.setItem('pargo-delivery-type', PARGO_PROCESS_TYPE_W2P);
        return;
    }

    //Set the correct state on page load
    const pargoSelectedPickupLocation = $('#pargo-selected-pickup-location');
    const pargoDeliveryType = $('#pargo-select-delivery-type');

    if (sessionStorage.getItem('pargo-delivery-type') !== null) {
        pargoDeliveryType.val(sessionStorage.getItem('pargo-delivery-type')).change();
        if (sessionStorage.getItem('pargo-delivery-type') === PARGO_PROCESS_TYPE_W2D) {
            pargoSelectedPickupLocation.hide();
        }
    } else {
        pargoDeliveryType.val(PARGO_PROCESS_TYPE_W2P).change();
        sessionStorage.setItem('pargo-delivery-type', PARGO_PROCESS_TYPE_W2P);
    }

    pargoDeliveryType.on('change', e => {
        sessionStorage.removeItem('pargo-delivery-type');

        if ($(e.currentTarget).val() === PARGO_PROCESS_TYPE_W2P) {
            sessionStorage.setItem('pargo-delivery-type', PARGO_PROCESS_TYPE_W2P);
            pargoSelectedPickupLocation.show('slow');
        } else {
            sessionStorage.setItem('pargo-delivery-type', PARGO_PROCESS_TYPE_W2D);
            pargoSelectedPickupLocation.hide('slow');
        }

        $.post(ajax_object.adminurl, {
                'action': 'set_delivery_type',
                'delivery_type': sessionStorage.getItem('pargo-delivery-type')
            },
            function(response) {
                if (!response.success) {
                    console.log("Failed to set delivery type.");
                }
                //triggers the shipping method to reload on change. This makes the pargo shipping method act like a normal method change in woocom
                jQuery('select.shipping_method, :input[name^=shipping_method]').change(() => {
                    $('body').trigger('update_checkout', { update_shipping_method: true });
                });
            }
        );
    });
}

jQuery(document).ready(function($) {

    if (window.addEventListener) {
        window.addEventListener("message", filterNonPargoEvents, false);
    } else {
        window.attachEvent("onmessage", filterNonPargoEvents);
    }

    localStorage.removeItem("pargo-select");
    var shippingMethod = $("input:radio.shipping_method:checked").val();

    if (!shippingMethod) {
        shippingMethod = $(".shipping_method").val();
    }

    $(document.body).on("updated_checkout", () => {
        var shipping_method_radios = $(".shipping_method");
        shipping_method_radios.change(() => {
            if ($(this).val() !== "wp_pargo") {
                localStorage.removeItem("pargo-select");
            }
        });

        if (!shippingMethod) {
            shippingMethod = shipping_method_radios.val();
        }
        if (shippingMethod === "wp_pargo") {
            $("#ship-to-different-address").hide();
            $("#parcel_test_field").show();
            $(".insur-info").show();
        } else {
            $("#ship-to-different-address").show();
            $("#parcel_test_field").hide();
            $(".insur-info").hide();
            const parcel_test = $("#parcel_test");
            if (parcel_test.prop("checked")) {
                parcel_test.prop("checked", false);
                $(document.body).trigger("update_checkout");
            }
        }

        if (shippingMethod === "wp_pargo" && localStorage.getItem("pargo-select") !== null) {
            $(".pargo-info-box").show();
            setupProcessTypeSelect($);
        } else if (
            shippingMethod === "wp_pargo" &&
            localStorage.getItem("pargo-select") === null
        ) {
            $(".pargo-info-box").hide();
        } else {
            $(".pargo-info-box").hide();
        }
    });
    $(".opening-hours-pargo-btn").click(() => {
        $(".open-hours-pargo-info").toggle("slow");
    });
    $(".pargo-toggle-map").click(() => {
        $(".pargo-info-box").hide("slow");
        $("#pargo_map_block").show("slow");
        $("body").trigger("update_checkout", { update_shipping_method: true });
        placeOrderBtn(false);
        localStorage.removeItem("pargo-select");
    });

    function placeOrderBtn(value) {
        $("#place_order").removeAttr("disabled");
    }

    function openPargoModal() {
        //get value of map token hidden input
        let pargoMapToken = $("#pargomerchantusermaptoken").val();
        // add staging if not in production
        let staging = $("#pargoismapproduction").val() === '1' ? '' : '.staging';
        $(".pargo-cart").append(
            `<div id='pargo_map'>
                <div id='pargo_map_center'>
                    <div id='pargo_map_inner'>
                        <div class='pargo_map_close'>x</div>
                            <iframe
                                id='thePargoPageFrameID'
                                src='https://map${staging}.pargo.co.za/?token=${pargoMapToken}'
                                width='100%'
                                height='100%'
                                allow='geolocation'
                                name='thePargoPageFrame'>
                            </iframe>
                        </div>
                    </div>
                </div>`
        );
        $("#pargo_map").fadeIn(PARGO_FADE_SPEED);
        $(".pargo_map_close").on("click", () => {
            $("#pargo_map").fadeOut(PARGO_FADE_SPEED);
            $('.pargo-cart').empty();
        });
    }
    var body = $("body");
    body.on("click", "#select_pargo_location_button", () => {
        openPargoModal();
    });
    $("#parcel_test_field").after('<span class="insur-info">insurance terms</span>');

    body.on("click", ".insur-info", () => {
        if ($("#parcel_test").is(":checked")) {
            $("#modal-trigger-insurance").attr("checked", "checked");
        } else {
            $("#modal-trigger-insurance").attr("checked", "");
        }
    });

    /**
     *  filterNonPargoEvents()
     *  @param {Object} event
     *  @return {Boolean}
     *
     *
     *  @description
     *   In Javascript Events, more than more emmiter and subscriber can use the same event handler
     *   Due to this we have been triggering selectPargoPoint() which in turn fires off an API call
     *   to the wordpress /admin-api.php endpoint uneccessarily since any event
     *   fired on the message event will trigger this function.
     */
    function filterNonPargoEvents(item) {
        if (typeof item.data.pargoPointCode == 'undefined') {
            return true
        }
        item.stopPropagation()

        selectPargoPoint(item)
    }

    /** After Pargo Pickup Point is Selected **/
    function selectPargoPoint(item) {
        var data = null;
        if (item.data.pargoPointCode) {
            localStorage.setItem(
                "pargo-shipping-settings",
                JSON.stringify(item.data)
            );

            data = JSON.parse(localStorage.getItem("pargo-shipping-settings"));
            let pargoButtonCaptionAfter = $("#pargobuttoncaptionafter").val();
            $("#select_pargo_location_button").html(pargoButtonCaptionAfter);
        } else {
            data = JSON.parse(localStorage.getItem("pargo-shipping-settings"));
        }

        $.post(ajax_object.adminurl, {
            pargoshipping: data,
            action: 'set_pick_up_point',
        }, () => {
            if (item.data.photo !== "") {
                $("#pick-up-point-img").attr("src", item.data.photo);
                $("#pargoStoreName").text(item.data.storeName);
                $("#pargoStoreAddress").text(item.data.address1);
                $("#pargoBusinessHours").text(item.data.businessHours);
            }
            if (item.data.pargoPointCode) {
                $("#pargo_map").hide("slow", () => {});
                $('.pargo-cart').empty();
            }
        });
    }
    // Click checkout - make sure point is selected
    body.on("click", "a.checkout-button.button.alt.wc-forward, #place_order",
        function(e) {
            const wp_pargo_input = $('input[value="wp_pargo"]');
            if (wp_pargo_input.is(":checked") || wp_pargo_input.is(":hidden")) {
                if ($("#pargoStoreName").is(":empty")) {
                    var delivery_type = $('select#pargo-select-delivery-type').val()
                    if (delivery_type == undefined || delivery_type == PARGO_PROCESS_TYPE_W2P) {
                        $("#pargo-not-selected").addClass("show-warn");
                    }
                    e.preventDefault();
                }
            }
        }
    );
    $("#pargo-not-selected").on("click", function() {
        $(this).removeClass("show-warn");
    });
});
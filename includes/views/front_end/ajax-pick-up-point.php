<?php

if (!session_id()) {
    session_start();
}

if(isset($_POST['pargoshipping']) && !empty($_POST['pargoshipping'])){

    $_SESSION['pargo_shipping_address'] = $_POST['pargoshipping'];

}


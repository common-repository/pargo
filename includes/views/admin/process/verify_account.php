<?php

$username = sanitize_text_field($_POST['pargo_username']);
$password = sanitize_text_field($_POST['pargo_password']);

echo json_encode($_POST);

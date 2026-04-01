<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('ClientId');
$client->setClientSecret('ClientSecret');
$client->setRedirectUri('RedirectUri'); //http://localhost:3000/callback.php

$client->addScope("email");
$client->addScope("profile");
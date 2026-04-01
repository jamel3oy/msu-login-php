<?php
require 'config.php';
session_start();

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $accessToken = $token['access_token'];

    $oauth = new Google_Service_Oauth2($client);
    $user_info = $oauth->userinfo->get();

    // เรียก API MSU
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://erp.msu.ac.th/service/api/staffinfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $accessToken
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $staffData = json_decode($response, true);

    // เก็บข้อมูลลง session
    $_SESSION['user'] = [
        'id' => $user_info->id,
        'name' => $user_info->name,
        'email' => $user_info->email,
        'picture' => $user_info->picture
    ];

    // เก็บข้อมูล staff เพิ่ม
    if ($staffData['status'] === true) {
        $_SESSION['staff'] = $staffData['data'];
    }

    header('Location: dashboard.php');
    exit;
}
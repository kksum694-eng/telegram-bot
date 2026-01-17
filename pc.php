<?php
// --- Configuration ---
const BOT_TOKEN     = '7998704927:AAHoLSwfnN4023NZHhfx29a3eJfXRs6SHRc';
const ADMIN_CHAT_ID = '';
const WEBHOOK_URL   = '';
const CHANNEL_IDS   = ['@sklootmachao'];

function httpCallAdvanced($url, $data = null, $headers = [], $method = "GET", $returnHeaders = false) {
    $ch = curl_init();
    
    // Anti-Akamai: Use HTTP/2 and modern TLS
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, br");
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Anti-bot: Realistic connection behavior
    curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 45);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($method === "POST") {
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($returnHeaders) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);

    curl_close($ch);

    if ($err) {
        error_log("cURL Error: " . $err);
        return ['success' => false, 'error' => $err, 'http_code' => $httpCode, 'body' => ''];
    }

    if ($httpCode == 403) {
        return ['success' => false, 'error' => 'Akamai blocked (403)', 'http_code' => 403, 'body' => $response];

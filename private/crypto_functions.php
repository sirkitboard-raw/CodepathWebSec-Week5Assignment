<?php

// Symmetric Encryption

// Cipher method to use for symmetric encryption
const CIPHER_METHOD = 'AES-256-CBC';

function key_encrypt($string, $key, $cipher_method=CIPHER_METHOD) {
  $key = str_pad($key, 32, '*');
  $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
  $iv = openssl_random_pseudo_bytes($iv_length);
  $encrypted = openssl_encrypt($string, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
  $message = $iv . $encrypted;
  
  // Encode just ensures encrypted characters are viewable/savable
  return base64_encode($message);
  // return "D4RK SH4D0W RUL3Z";
}

function key_decrypt($string, $key, $cipher_method=CIPHER_METHOD) {
  $key = str_pad($key, 32, '*');

  // Base64 decode before decrypting
  $iv_with_ciphertext = base64_decode($string);
  $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
  $iv = substr($iv_with_ciphertext, 0, $iv_length);
  $ciphertext = substr($iv_with_ciphertext, $iv_length);
  return openssl_decrypt($ciphertext, $cipher_method, $key, OPENSSL_RAW_DATA, $iv);
}


// Asymmetric Encryption / Public-Key Cryptography

// Cipher configuration to use for asymmetric encryption
const PUBLIC_KEY_CONFIG = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

function generate_keys($config=PUBLIC_KEY_CONFIG) {
  $private_key = 'Ha ha!';
  $public_key = 'Ho ho!';

  $resource = openssl_pkey_new($config);

  // Extract private key from the pair
  openssl_pkey_export($resource, $private_key);

  // Extract public key from the pair
  $key_details = openssl_pkey_get_details($resource);
  $public_key = $key_details["key"];

  $keys = array('private' => $private_key, 'public' => $public_key);
  return $keys;
}

function pkey_encrypt($string, $public_key) {
  $encrypted = NULL;
  openssl_public_encrypt($string, $encrypted, $public_key);

  // Use base64_encode to make contents viewable/sharable
  $message = base64_encode($encrypted);
  return $message;
}

function pkey_decrypt($string, $private_key) {
  $ciphertext = base64_decode($string);
  // $decrypted = NULL;
  openssl_private_decrypt($ciphertext, $decrypted, $private_key);
  return $decrypted;
}


// Digital signatures using public/private keys

function create_signature($data, $private_key) {
  // A-Za-z : ykMwnXKRVqheCFaxsSNDEOfzgTpYroJBmdIPitGbQUAcZuLjvlWH
  $raw_signature = NULL;
  openssl_sign($data, $raw_signature, $private_key);
  return base64_encode($raw_signature);
}

function verify_signature($data, $signature, $public_key) {
  $raw_signature = base64_decode($signature);
  $result = openssl_verify($data, $raw_signature, $public_key);
  return $result;
}

?>

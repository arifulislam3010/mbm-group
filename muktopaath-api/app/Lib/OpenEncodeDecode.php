<?php
namespace App\Lib;

class OpenEncodeDecode
{
    // function base64url_encode( $data ){
    //     return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
    // }

    // function base64url_decode( $data ){
    //     return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
    // }

    public function encode($val){

        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '9234567891061321';

        // Store the encryption key
        $encryption_key = "muktopaath003";

        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($val, $ciphering,
        $encryption_key, $options, $encryption_iv);

        // Display the encrypted string
        return $encryption;
    }

    public function decode($encryption){

        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '9234567891061321';

        // Store the encryption key
        $encryption_key = "muktopaath003";

        //Descrypt the string
        $decryption = openssl_decrypt($encryption, $ciphering,
        $encryption_key, $options, $encryption_iv);

        // Display the encrypted string
        return $decryption;
    }

}
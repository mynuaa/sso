<?php

require_once 'config.php';

class SSO {
    protected static $ssodb = null;
    protected static $ucdb = null;
    protected static $cert = null;
    protected static $pbkey = null;
    protected static $prkey = null;
    protected static $uid = -1;
    protected static function ssoInit($args) {
        self::$ssodb = new mysqli($args['host'], $args['user'], $args['pass'], $args['dbnm'], $args['port']);
        self::$cert = $args['cert'];
        self::$pbkey = openssl_pkey_get_public(file_get_contents(self::$cert . "/public_key.pem"));
        self::$prkey = openssl_pkey_get_private(file_get_contents(self::$cert . "/private_key.pem"));
        if (isset($_COOKIE['myauth_uid'])) {
            if (!($uid = self::ssoDecrypt($_COOKIE['myauth_uid']))) {
                setcookie('myauth_uid', '', time() - 3600);
            } else {
                $uid = intval(json_decode($uid, true)['uid']);
                self::$uid = $uid;
            }
        }
    }
    protected static function ucInit($args) {
        self::$ucdb = new mysqli($args['host'], $args['user'], $args['pass'], $args['dbnm'], $args['port']);
    }
    public static function init() {
        self::ssoInit($GLOBALS['__CONFIG']['sso']);
        self::ucInit($GLOBALS['__CONFIG']['uc']);
    }
    public static function ssoEncrypt($str) {
        if (!openssl_public_encrypt($str, $encrypted, self::$pbkey)) return false;
        return base64_encode($encrypted);
    }
    public static function ssoDecrypt($str) {
        $encrypted = base64_decode($str);
        if (!openssl_private_decrypt($encrypted, $str, self::$prkey)) return false;
        return $str;
    }
    public static function getPubkeyForJs() {
        return trim(file_get_contents(self::$cert . "/js_public_key.dat"));
    }
    public static function generateLoginUrl() {
        if (self::$uid != -1) {
            return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        $uri = base64_encode($_SERVER['REQUEST_URI']);
        return "http://{$_SERVER['HTTP_HOST']}/sso/?page=login&redirect_uri={$uri}";
    }
    public static function gotoLogin() {
        if (self::$uid != -1) {
            return;
        }
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/sso/?page=login&redirect_uri=' . base64_encode($_SERVER['REQUEST_URI']));
        die();
    }
    public static function getUser() {
        $id = intval(self::$uid);
        $ucRow = $ssoRow = null;
        $ucResult = self::$ucdb->query("SELECT `username`, `email` FROM `members` WHERE `uid` = {$id}");
        $ssoResult = self::$ssodb->query("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$id}");
        if ($ucResult) $ucRow = $ucResult->fetch_array();
        if ($ssoResult) $ssoRow = $ssoResult->fetch_array();
        if (!$ucRow || !$ssoRow) return null;
        $row = [
            'uid' => self::$uid,
            'username' => $ucRow['username'],
            'email' => $ucRow['email'],
            'auth_ded' => $ssoRow['auth_ded'],
        ];
        if (!$ssoRow) return null;
        return $row;
    }
}

SSO::init();

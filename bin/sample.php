<?php
require(__DIR__."/../vendor/autoload.php");

$key = "your key for api";
$secret = "your secret for api";

$bitbankcc = new \PhpBitbankcc\PhpBitbankcc\Bitbankcc();
$bitbankcc->initialize($key, $secret);

$body = $bitbankcc->readTicker("xrp_jpy");
//$body = $bitbankcc->readBalance();

var_dump($body);
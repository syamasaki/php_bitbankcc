# Overview
* This is php client implementation for Bitbankcc API

# How to use

```php
$key = "your key for api";
$secret = "your secret for api";

$bitbankcc = new \PhpBitbankcc\PhpBitbankcc\Bitbankcc();
$bitbankcc->initialize($key, $secret);

// json string
$body = $bitbankcc->readTicker("xrp_jpy");

echo $body;
// "{"success":1,"data":{"sell":"74.008","buy":"74.007","high":"78.861","low":"66.000","last":"74.008","vol":"95814140.0602","timestamp":1521528248647}}"
```
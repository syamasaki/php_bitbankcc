<?php
namespace PhpBitbankcc\PhpBitbankcc;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Bitbankcc
 * @package PhpBitbankcc\PhpBitbankcc
 *
 * when you encounter error about
 * https://github.com/google/google-api-php-client/issues/843
 *
 * Sean Yamasaki <sho.yamasaki@gmail.com>
 * http://seany.jp
 */
class Bitbankcc
{
    /**
     * @var string
     */
    private static $baseUrl = "https://api.bitbank.cc";

    /**
     * @var string
     */
    private static $basePublicUrl = "https://public.bitbank.cc";

    /**
     * @var bool
     */
    private static $ssl = true;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;


    /**
     * @param string $key
     * @param string $secret
     * @param array $params
     */
    public function initialize(string $key, string $secret, array $params = [])
    {
        $this->key = $key;
        $this->secret = $secret;

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            "base_url" => null,
            "ssl" => null
        ]);
        $params = $optionsResolver->resolve($params);

        if ($params["base_url"] !== null) {
            self::$baseUrl = $params["base_url"];
        }
        if ($params["ssl"] !== null) {
            self::$ssl = $params["ssl"];
        }
    }

    /**
     * @return string
     * TODO: implement
     */
    public function readBalance()
    {
//        $path = "/v1/user/assets";
//        $nonce = (string) (new \DateTime())->getTimestamp();
//        return $this->requestForGet($path, $nonce);

        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws \PhpBitbankcc\PhpBitbankcc\Exception
     */
    public function readActiveOrders()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws \PhpBitbankcc\PhpBitbankcc\Exception
     */
    public function createOrder()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws \PhpBitbankcc\PhpBitbankcc\Exception
     */
    public function cancelOrder()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function readTradeHistory()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function readWithdrawalAccount()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function requestWithdrawal()
    {
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @param string $pair
     * @return string
     */
    public function readTicker(string $pair)
    {
        $path = self::$basePublicUrl."/{$pair}/ticker";

        $client = new \GuzzleHttp\Client([
            "base_url" => self::$baseUrl
        ]);
        $response = $client->request("GET", $path, [
            "http_errors" => false
        ]);
        return (string) $response->getBody();
    }

    /**
     * @param string $pair
     * @return string
     */
    public function readOrderBooks(string $pair)
    {
//        $path = self::$basePublicUrl."/{$pair}/depth";
//
//        $client = new \GuzzleHttp\Client([
//            "base_url" => self::$baseUrl
//        ]);
//        $response = $client->request("GET", $path, [
//            "http_errors" => false
//        ]);
//        return (string) $response->getBody();

        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @param string $pair
     * @param string $date
     * @throws Exception
     */
    public function readTransactions(string $pair, string $date = "")
    {
//        $path = self::$basePublicUrl."/{$pair}/transactions".($date === "") ? "" : "/".$date;
//        $client = new \GuzzleHttp\Client([
//            "base_url" => self::$baseUrl
//        ]);
//        $response = $client->request("GET", $path, [
//            "http_errors" => false
//        ]);
//        return (string) $response->getBody();
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /******* private methods ********/

    /**
     * @param string $path
     * @param string $nonce
     * @param array $query
     * @return string
     */
    private function requestForGet(string $path, string $nonce, array $query = [])
    {
        $purl = new \Purl\Url(self::$baseUrl.$path);
        $signature = $this->getGetSignature($path, $this->secret, $nonce, $query);

        $headers = [
            "Content-Type" => "application/json",
            "ACCESS-KEY" => $this->key,
            "ACCESS-NONCE" => $nonce,
            "ACCESS-SIGNATURE" => $signature
        ];

        $urlQuery = $purl->getQuery()["query"];
        $urlQuery = ($urlQuery !== null) ? $urlQuery : [];

        // Todo: care about ssl
        // Todo: error handling
        $client = new \GuzzleHttp\Client([
            "base_url" => self::$baseUrl
        ]);
        $response = $client->request("GET", $path, [
            "query" => $urlQuery,
            "allow_redirects" => true,
            "headers" => $headers,
            "http_errors" => false
        ]);
        return (string) $response->getBody();
    }

    /**
     * @param string $path
     * @param string $nonce
     * @param string $body
     * @return string
     */
    private function requestForPost(string $path, string $nonce, string $body)
    {
        $purl = new \Purl\Url(self::$baseUrl.$path);
        $signature = $this->getPostSignature($this->secret, $nonce, $body);

        $headers = [
            "Content-Type" => "application/json",
            "ACCESS-KEY" => $this->key,
            "ACCESS-NONCE" => $nonce,
            "ACCESS-SIGNATURE" => $signature,
            "ACCEPT" => "application/json"
        ];

        $urlQuery = $purl->getQuery()["query"];
        $urlQuery = ($urlQuery !== null) ? $urlQuery : [];

        // Todo: care about ssl
        // Todo: error handling
        $client = new \GuzzleHttp\Client([
            "base_url" => self::$baseUrl
        ]);
        $response = $client->request("POST", $path, [
            "query" => $urlQuery,
            "allow_redirects" => true,
            "headers" => $headers,
            "body" => $body,
            "http_errors" => false
        ]);
        return (string) $response->getBody();
    }

    /**
     * @param string $path
     * @param string $secretKey
     * @param string $nonce
     * @param array $query
     * @return string
     */
    private function getGetSignature(string $path, string $secretKey, string $nonce, array $query = [])
    {
        $queryString = (!empty($query)) ? "?" . implode("&", $query) : "";
        $message = $nonce . $path . $queryString;
        return hash_hmac("sha256", $message, $secretKey);
    }

    /**
     * @param string $secretKey
     * @param string $nonce
     * @param string $body
     * @return string
     */
    private function getPostSignature(string $secretKey, string $nonce, string $body = "")
    {
        $message = $nonce . $body;
        return hash_hmac("sha256", $message, $secretKey);
    }
}
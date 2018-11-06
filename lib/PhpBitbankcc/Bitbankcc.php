<?php
namespace PhpBitbankcc\PhpBitbankcc;

/**
 * Class Bitbankcc
 * @package PhpBitbankcc\PhpBitbankcc
 *
 * Shohei Yamasaki <sho.yamasaki@gmail.com>
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
     */
    public function initialize(string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * @return array
     */
    public function readBalance()
    {
        $path = "/v1/user/assets";
        $nonce = $this->createNonce();
        return $this->requestForGet($path, $nonce);
    }

    /**
     * @return array
     */
    public function readActiveOrders($pair, $count = null, $fromId = null, $endId = null, $since = null, $end = null)
    {
        $path = "/v1/user/spot/active_orders";
        $nonce = $this->createNonce();
        $params = [
            "pair" => $pair,
            "count" => $count,
            "from_id" => $fromId,
            "end_id" => $endId,
            "since" => $since,
            "end" => $end
        ];
        return $this->requestForGet($path, $nonce, $params);
    }

    /**
     * @throws \PhpBitbankcc\PhpBitbankcc\Exception
     */
    public function createOrder()
    {
        $path = "/v1/user/spot/order";
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws \PhpBitbankcc\PhpBitbankcc\Exception
     */
    public function cancelOrder()
    {
        $path = "/v1/user/spot/cancel_order";
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function readTradeHistory()
    {
        $path = "/v1/user/spot/trade_history";
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function readWithdrawalAccount()
    {
        $path = "/v1/user/withdrawal_account";
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @throws Exception
     */
    public function requestWithdrawal()
    {
        $path = "/v1/user/request_withdrawal";
        throw new \PhpBitbankcc\PhpBitbankcc\Exception("Todo: implement");
    }

    /**
     * @param string $pair
     * @return string
     */
    public function readTicker(string $pair)
    {
        $path = self::$basePublicUrl."/{$pair}/ticker";

        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET", $path, [
            "http_errors" => false
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $pair
     * @return string
     */
    public function readOrderBooks(string $pair)
    {
        $path = self::$basePublicUrl."/{$pair}/depth";

        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET", $path, [
            "http_errors" => false
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $pair
     * @param string $date
     * @throws Exception
     */
    public function readTransactions(string $pair, string $date = "")
    {
        $path = self::$basePublicUrl."/{$pair}/transactions".($date === "") ? "" : "/".$date;
        $client = new \GuzzleHttp\Client();
        $response = $client->request("GET", $path, [
            "http_errors" => false
        ]);
        return (string) $response->getBody();
    }


    /******* private methods ********/

    /**
     * @param string $path
     * @param string $nonce
     * @param array $query
     * @return array
     */
    private function requestForGet(string $path, string $nonce, array $query = [])
    {
        $purl = new \Purl\Url(self::$baseUrl.$path);
        $signature = $this->getGetSignature($path, $this->secret, $nonce, $query);

        $headers = [
            "ACCEPT" => "application/json",
            "ACCESS-KEY" => $this->key,
            "ACCESS-NONCE" => $nonce,
            "ACCESS-SIGNATURE" => $signature
        ];

        $urlQuery = $purl->getQuery()["query"];
        $urlQuery = ($urlQuery !== null) ? $urlQuery : [];

        $client = new \GuzzleHttp\Client();
        $response = $client->get(self::$baseUrl.$path, [
            "query" => $urlQuery,
            "allow_redirects" => false,
            "headers" => $headers,
            "http_errors" => false
        ]);

        return json_decode($response->getBody(), true);
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
            "ACCEPT" => "application/json",
            "ACCESS-KEY" => $this->key,
            "ACCESS-NONCE" => $nonce,
            "ACCESS-SIGNATURE" => $signature
        ];

        $urlQuery = $purl->getQuery()["query"];
        $urlQuery = ($urlQuery !== null) ? $urlQuery : [];

        $client = new \GuzzleHttp\Client();
        $response = $client->request("POST", $path, [
            "query" => $urlQuery,
            "allow_redirects" => false,
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

    /**
     * @return mixed
     */
    private function createNonce()
    {
        return microtime(true) * 1000;
    }
}
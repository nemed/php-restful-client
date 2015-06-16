<?php
namespace ctur\rest;

use Exception;

/**
 * Rest client.
 * Example:
 * Client::call(Client::GET, 'http://myapi.com/api.php', ['param1' => 1, 'param1' => 2]);
 * Generate link and create request to http://myapi.com/api.php?param1=1&param2=2
 * Supported methods: POST, PUT, GET etc
 *
 * @package kfosoft\rest
 * @author Cyril Turkevich
 */
class Client
{
    const GET = 1;
    const POST = 2;
    const PUT = 3;

    /**
     * @param int $method use class const POST, PUT, GET etc
     * @param string $url url to api
     * @param array|null $data array("param" => "value") ==> index.php?param=value
     *
     * @return mixed
     *
     * @throws Exception
     *
     * @author Cyril Turkevich
     */
    public static function call($method, $url, $data = null)
    {
        $curl = curl_init();

        switch ($method) {
            case static::POST :
                curl_setopt($curl, CURLOPT_POST, 1);
                $data && curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case static::PUT:
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case static::GET:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
            default:
                throw new Exception('Undefined rest method!');
        }

        /* Optional Authentication: */
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}

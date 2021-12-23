<?php

namespace Huid\PhpcDep\Support;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * class Requests.
 * @method string getBaseUri()
 */
class Requests
{
    /**
     * Make a get request.
     *
     * @param string $endpoint
     * @param array  $query
     * @param array  $headers
     *
     * @return array | string
     */
    public function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }
    /**
     * Make a post request.
     *
     * @param string $endpoint
     * @param array  $params
     * @param array  $headers
     *
     * @return array
     */
    public function post($endpoint, $params = [], $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
    }
    /**
     * Make a http request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $options  http://docs.guzzlephp.org/en/latest/request-options.html
     *
     * @return string | array
     */
    public function request($method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options));
    }
    /**
     * Return base Guzzle options.
     *
     * @return array
     */
    public function getBaseOptions()
    {
        $options = [
            'base_uri' => method_exists($this, 'getBaseUri') ? $this->getBaseUri() : '',
            'timeout' => property_exists($this, 'timeout') ? $this->timeout : 15.0,
        ];
        return $options;
    }
    /**
     * Return http client.
     *
     * @param array $options
     *
     * @return \GuzzleHttp\Client
     *
     * @codeCoverageIgnore
     */
    public function getHttpClient(array $options = [])
    {
        return new Client($options);
    }
    /**
     * Convert response contents to json.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array | string
     */
    public function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        } elseif (false !== stripos($contentType, 'html')) { // 有些情况，响应头是为application/json，但文本内容为html
            return json_decode($contents, true);
        }
        return $contents;
    }
}

<?php

if (!function_exists('curl')) {
    /**
     * @param string $uri
     * @param array $data  FORM:['query'=>[]];JSON:['json'=>[],'headers'=>[]],其它 key 还有：form_params,multipart,decode_content,json,query,headers
     * @param string $method
     * @return array|bool|mixed
     */
    function curl(string $uri, array $data = [], string $method = 'GET')
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 20,
            'verify' => config('app.debug') ? false : true, // 本地debug时，请求https关闭证书认证
            'allow_redirects' => false
        ]);

        $response = null;
        try {
            switch (strtoupper($method)) {
                case 'GET':
                    $response = $client->get($uri, $data);
                    break;
                case 'POST':
                    $response = $client->post($uri, $data);
                    break;
                case 'PUT':
                    $response = $client->put($uri, $data);
                    break;
                case 'DELETE':
                    $response = $client->delete($uri, $data);
                    break;
                case 'HEAD':
                    $response = $client->head($uri, $data);
                    break;
                case 'PATCH':
                    $response = $client->patch($uri, $data);
                    break;
                default:
                    return ['code'=>-1,'message'=>'Method Not Allowed'];
            }
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }
}

if (!function_exists('is_ip')) {
    function is_ip(string $string)
    {
        $ip=explode('.',$string);

        foreach ($ip as $iValue) {
            if($iValue > 255) {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $string);
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return is_ip($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $ip;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return is_ip($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
        }

        return is_ip($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $ip;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: luoxulx
 * Date: 2019/6/5
 * Time: 下午8:18
 */

namespace App\Support;

/**
 * 谨慎使用
 * ### 通用翻译API每月提供200万字符免费额度，超出后需要按照当月全部使用字符数收费,任何一个标点、符号都算一个字符
 * ！！！超过200万字符，需按照49元人民币/百万字符支付当月全部翻译字符数费用（包括之前免费的200万字符）！！！
 * ###字符数（Byte）以翻译的源语言字符长度为标准计算，空格、html标签等均计入字符。###
 * ###一般来说，一个汉字占3个字符，一个英文字母占1个字符，一个半角标点符号占1个字符，一个全角标点符号占2个字符。###
 * ###例如：百度=6个字符，Baidu=5个字符。###
 * 可翻译长文本,通用翻译API支持28种语言互译，覆盖中、英、日、韩、西、法、泰、阿、俄、葡、德、意、荷、芬、丹等；支持28种语言的语种检测。
 * from 默认auto,可自动检测  more info=>http://api.fanyi.baidu.com/api/trans/product/apidoc
 * Class BaiduTranslate
 * @package App\Tools\BaiduTranslate
 */
class BaiduTranslate
{
    protected $uri;
    protected $appId;
    protected $secKey;

    public function __construct()
    {
        $this->uri = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
        $this->appId = config('app.14k.bai_du_fy_id');
        $this->secKey = config('app.14k.bai_du_fy_key');
    }

    /**
     * @param $query
     * @param string $from
     * @param string $to
     * @return mixed
     * @throws \Exception
     */
    public function translate($query, $from='auto', $to='en')
    {
        $salt = random_int(10000, 99999);
        $args = array(
            'q' => $query,
            'appid' => $this->appId,
            'salt' => $salt,
            'from' => $from,
            'to' => $to
        );

        $args['sign'] = md5($this->appId . $query . $salt . $this->secKey);
        $ret = $this->call($this->uri, $args);
        $result = json_decode($ret, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg() ?? 'unknown error', 400);
        }

        if (isset($result['error_code']) && $result['error_code'] != 52000) {
            throw new \Exception($result['error_msg'] ?? 'unknown error', 400);
        }

        if (isset($result['trans_result'][0]['dst'])) {
            return $result['trans_result'][0]['dst'];
        }

        throw new \Exception('translate unknown error', 400);
    }

    //发起网络请求
    private function call($url, $args=null, $method='POST', $timeout = 10, $headers=array())
    {
        $ret = false;
        $i = 0;
        while($ret === false)
        {
            if($i > 1) {
                break;
            }
            if($i > 0) {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }

    private function callOnce($url, $args=null, $method='post', $withCookie = false, $timeout = 10, $headers=array())
    {
        $ch = curl_init();
        if($method === 'post')
        {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else
        {
            $data = $this->convert($args);
            if($data)
            {
                if(stripos($url, '?') > 0)
                {
                    $url .= "&$data";
                }
                else
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    private function convert(&$args): string
    {
        $data = '';
        if (\is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (\is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val).'&';
                }
            }
            return trim($data, '&');
        }
        return $args;
    }
}

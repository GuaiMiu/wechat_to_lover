<?php
$timezone = date_default_timezone_get();           // 获取默认时区
if ($timezone !== 'Asia/Shanghai') {
    date_default_timezone_set('Asia/Shanghai');    // 设置默认时区
}
/*
 * @Author: 贺和平
 * @Date: 2022-08-22 21:27:52
 * @Mail: 1297685880@qq.com
 * @LastEditTime: 2022-08-23 01:06:40
 * @FilePath: \\wechat-msg\\tools\\Duck.php
 */
class Duck
{
    var $hefengkey;
    var $hefengcity;

    var $appid;
    var $appsecret;


    var $togetherdays;
    var $birthday;

    function __construct($config)
    {
        $this->appid = $config['appid'];
        $this->appsecret = $config['appsecret'];
        $this->hefengkey = $config['hefengkey'];
        //$this->hefengcity = $config['hefengcity'];
        $this->togetherdays = $config['togetherdays'];
        $this->birthday = $config['birthday'];


        $params = [
            'location' => $config['hefengcity'],//
            'key' => $this->hefengkey
        ];
        $url = 'https://geoapi.qweather.com/v2/city/lookup';
        $CityID = $this->getUrl($url, $params);
        //var_dump($CityID);
        $this->hefengcity = $CityID['location'][0]['id'];
        //var_dump($this->hefengcity);
    }

    
    public static function getUrl ($url, $data=[])
    {
        if($data == !NULL) {
            $data = http_build_query($data);
            //echo $uri;
            $url = $url . '?' . $data;
            //echo $url;
        }
        $headerArray =array("Content-type:application/json;","Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_ENCODING, '');//请求时自动加上请求头Accept-Encoding，并且返回内容会自动解压
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($curl);
        curl_close($curl);
        $output_data = json_decode($output,true);
        if ($output_data ==NULL)
        {
            return $output;
        }
        return $output_data;

    }

    public static function postUrl ($url, $data)
    {
        //$data  = json_encode($data);    
        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_ENCODING, '');//请求时自动加上请求头Accept-Encoding，并且返回内容会自动解压
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
    }


    public function getQingHua ()
    {
        $url = 'https://api.vvhan.com/api/love?type=json';
        $qinghua = $this->getUrl($url);
        if(!isset($qinghua['ishan']))
        {
            $qinghua = $this->getUrl('https://api.lovelive.tools/api/SweetNothings');
            return $qinghua;
        }
        return $qinghua['ishan'];
    }

    /**
     * 和风天气
     */
    public function getWeather ()
    {
        $params = [
            'location' => $this->hefengcity,
            'key' => $this->hefengkey
        ];
        $url = 'https://devapi.qweather.com/v7/weather/now';
        $weather = $this->getUrl($url, $params);
        return $weather;
    }

    public function getIndices ()
    {
        $params = [
            'type' => '3',//穿衣指数3 洗车指数2 运动指数1 ... 和风天气自查
            'location' => $this->hefengcity,
            'key' => $this->hefengkey
        ];
        $url = 'https://devapi.qweather.com/v7/indices/1d';
        return $Indices = $this->getUrl($url, $params);
    }

    public function getCity ()
    {
        $params = [
            'location' => $this->hefengcity,//
            'key' => $this->hefengkey
        ];
        $url = 'https://geoapi.qweather.com/v2/city/lookup';
        return $City = $this->getUrl($url, $params)['location'][0]['name'];
    }



    /**
     * 微信
     */
    const URL_LIST = [
        'getAccessToken' => 'https://api.weixin.qq.com/cgi-bin/token',
        'getUserList'   => 'https://api.weixin.qq.com/cgi-bin/user/get',
        'sendTemplateMessage' => 'https://api.weixin.qq.com/cgi-bin/message/template/send',
        'getTemplateList' => 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template'
];
    public function getAccessToken()
    {
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $this->appid,
            'secret' => $this->appsecret
        ];
        //echo self::URL_LIST['getAccessToken'];
        
        $accessTokenInfo = $this->getUrl(self::URL_LIST["getAccessToken"],$params);
        //var_dump($accessTokenInfo);
        return $accessTokenInfo['access_token'];
    }
    
    public function getUserList($nextOpenId = '')
    {
        $params = [
            'access_token' => $this->getAccessToken(),
        ];
        return $userList = $this->getUrl(self::URL_LIST['getUserList'] , $params);

    }

    public function getTemplateList()
    {
        $params = [
            'access_token' => $this->getAccessToken(),
        ];
        return $templateList = $this->getUrl(self::URL_LIST['getTemplateList'] , $params);
    }

    public function sendTemplateMessage($content)
    {
        //echo $this->getAccessToken();
        return $this->postUrl(self::URL_LIST['sendTemplateMessage'] . '?access_token=' . $this->getAccessToken(), $content);
    }

    /**
     *
     */
    public function getTogetherDays()
    {
        $now = strtotime(date("Y-m-d H:i:s"));
        //计算两个日期之间的时间差
        $diff = abs($now - strtotime($this->togetherdays));
        //转换时间差的格式
        // $years = floor($diff / (365*60*60*24));
        // $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24));
        $days = floor(($diff)/ (60*60*24));
        $hours = floor(($diff - $days*60*60*24)  / (60*60));
        $minutes = floor(($diff - $days*60*60*24  - $hours*60*60)/ 60);
        $seconds = floor(($diff - $days*60*60*24  - $hours*60*60 - $minutes*60));
        return $days.'天'.$hours.'小时'.$minutes.'分钟'.$seconds.'秒';
    }

    public function getBirthday($birthday ='')
    {
        if ($birthday == NULL)
        {
            $birthday = $this->birthday;
        }
        list($birthYear, $birthMonth, $birthDay) = explode('-', $birthday);
        //echo $birthDay;
        $birthday = date("Y") .'-'.$birthMonth.'-'.$birthDay;
        if(!(strtotime($birthday) > strtotime(date("Y-m-d"))))
        {
            $birthday = date("Y")+1 .'-'.$birthMonth.'-'.$birthDay;
        }

        //echo $this->birthday;
        $now = strtotime(date("Y-m-d"));
        $diff = abs($now - strtotime($birthday));
        $days = floor(($diff)/ (60*60*24));
        return $days;
    }

    /**
     * 新冠疫情
     */
    public function getFeiYan()
    {
        $data = $this->getUrl('https://cdn.mdeer.com/data/yqstaticdata.js');
        $data = ltrim($data,'callbackstaticdata(');
        $data = rtrim($data,')');
        $data = json_decode($data,true);

        foreach ($data['cityLists'] as $city)
        {
            if($city['city'] == "成都")
            {
                $feiYan = [
                    "新增确诊" => $city['lastLocalSureNew'],
                    "新增无症状" => $city['lastIncrHideNew'],
                    "现有确诊" => $city['currentConfirm']
                ];
                return $feiYan;
            }

        }


    }

    
}





?>
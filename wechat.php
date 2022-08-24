<?php
/*
 * @Author: 贺和平
 * @Date: 2022-08-22 20:58:39
 * @Mail: 1297685880@qq.com
 * @LastEditTime: 2022-08-23 19:02:21
 * @FilePath: \\undefinedc:\\Users\\ai014\\Desktop\\wechat-msg\\wechat.php
 */
echo @$_GET['echostr'];
require_once 'Duck.php';
//var_dump($_ENV);
$config = getConfig(__DIR__.'/config.ini');
//echo $config['appid'];
$start = new Duck($config);
    $data = [
        'touser' => '',
        'template_id' => $start->getTemplateList()['template_list'][0]['template_id'],//默认只给第一个模板发消息
        'url' => 'https://ozxc.cn',
        'topcolor' => '#fdb3b0',
        'data' => [
            'date' => [
                'value' => date('Y年n月j日 H时i分s秒'),
                'color' => '#5ecf3b'
            ],
            'week' => [
                'value' => date('Y年n月j日 H时i分s秒'),
                'color' => '#5ecf3b'
            ],

            'city' => [//城市
                'value' => $start->getCity(),
                'color' => '#fda76f'
            ],
            'weather' => [ //天气现象
                'value' => $start->getWeather()['now']['text'],
                'color' => '#6ee5f6'
            ],
            'temp' => [ //温度
                'value' => $start->getWeather()['now']['temp'],
                'color' => '#5ecf3b'
            ],
            'humidity' => [//相对湿度
                'value' => $start->getWeather()['now']['humidity'].'%',
                'color' => '#949bd6'
            ],
            'indices' => [//指数
                'value' => $start->getIndices()['daily'][0]['text'],
                'color' => '#40d6bf'
            ],
            'qinghua' => [//情话
                'value' => $start->getQingHua(),
                'color' => '#eb5f76'
            ],
            'birthday' => [//生日
                'value' => $start->getBirthday(),
                'color' => '#fdb3b0'
            ],
            'birthday2' => [//第二个人的生日
                'value' => $start->getBirthday($config['birthday2']),
                'color' => '#a594de'
            ],
            'togetherdays' => [//在一起多久了
                'value' => $start->getTogetherDays(),
                'color' => '#8218e7'
            ]
        ],
    ];

/**
 * 启动点
 */
foreach ($start->getUserList()['data']['openid'] as $user)
{
    $data['touser'] = $user;
    $start->sendTemplateMessage(json_encode($data));
}




function getConfig($path)
{
    $file = fopen($path,"r");
    while(!feof($file))
    {
        $data =  fgets($file);
        $data = trim($data);
        //$data = preg_replace('# #','',$data);
        //parse_str($configdata,$arrconfig);
        $datalist = explode('=',$data);
        $datalist[0] = trim($datalist[0]);
        $datalist[1] = trim($datalist[1]);

        $configdata[$datalist[0]] =$datalist[1];
    }
    return $configdata;
}
 ?>
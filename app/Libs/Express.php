<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2017/7/25
 * Time: 14:08
 */

namespace App\Libs;


use Illuminate\Support\Facades\Log;

class Express
{
    protected static $EBusinessID='1298211';
    protected static $AppKey='f1d73c40-2c6f-4fee-a624-7a01ad952380';
    protected static $ReqURL='http://api.kdniao.com/api/dist';
    protected static $ShipperCode='STO';

    protected static $Url100='https://poll.kuaidi100.com/poll/query.do';

    //订阅物流信息
    public static function orderTraces($code,$ShipperCode)
    {
        $requestData="{\"CallBack\":\"\",\"IsNotice\":\"0\",\"LogisticCode\":\"".$code."\",\"MemberID\":\"\",\"OrderCode\":\"\",\"Receiver\":{\"Address\":\"\",\"CityName\":\"\",\"ExpAreaName\":\"\",\"Mobile\":\"\",\"Name\":\"\",\"ProvinceName\":\"\"},\"Sender\":{\"Address\":\"\",\"CityName\":\"\",\"ExpAreaName\":\"\",\"Mobile\":\"\",\"Name\":\"\",\"ProvinceName\":\"\"},\"ShipperCode\":\"".$ShipperCode."\"}";


        $datas = array(
            'EBusinessID' => self::$EBusinessID,
            'RequestType' => '1008',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = self::encrypt($requestData, self::$AppKey);
        $result=self::sendPost(self::$ReqURL, $datas);
        Log::info(json_encode($result,true));
        //根据公司业务处理返回的信息......

        return $result;
    }
    //实时获得物流信息
    public static function getOrderTraces($code,$shipper_code){
        $url='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
        $requestData= "{'OrderCode':'','ShipperCode':'".$shipper_code."','LogisticCode':'".$code."'}";

        $datas = array(
            'EBusinessID' => self::$EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = self::encrypt($requestData, self::$AppKey);
        $result=self::sendPost($url, $datas);

        return $result;
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    public static function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    public static function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    /**
     * 顺丰查询订单
     * @param $order
     * @author yss
     * @date  2018/12/12 10:55
     */
    public static function sfTraces($order)
    {
        try{
            $post_data = array();
            $post_data["customer"] = '915096A470D8DF628DD23298F6422F2E';
            $key= 'cyMvMbue9926' ;
            $post_data["param"] = '{"com":"shunfeng","num":"'.$order->logistic_code.'"}';

            $post_data["sign"] = strtoupper(md5($post_data["param"].$key.$post_data["customer"]));

            $o='';
            foreach ($post_data as $k=>$v)
            {
                $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
            }

            $post_data=substr($o,0,-1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,self::$Url100);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_POST, true);

            $result = curl_exec($ch);
            curl_close($ch);
            $data = str_replace("\"",'"',$result );
            $data = json_decode($data,true);

            return $data;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
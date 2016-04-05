<?php

/**
 *  picture check with ali sdk on composer lib
 */
include_once 'aliyun-php-sdk-core/Config.php';
use Green\Request\V20160308 as Green;

class CheckPicture
{
    private $client;

    private $request;

    public function __construct($accessKey, $secretKey)
    {
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $accessKey, $secretKey);

        $this->client = new DefaultAcsClient($iClientProfile);

        // 图片检测
        $this->request = new Green\ImageDetectionRequest ();

    }

    public function check($url)
    {
        //设置参数

        //设置为同步调用
        $this->request->setAsync("false");

        //设置图片链接
        //同步只支持单张图片
        $this->request->setImageUrl(json_encode(array($url)));

        //设置检测的场景
        //porn: 黄图检测
        //ocr:  图文识别
        $this->request->setScene(json_encode(array("porn")));

        try {
            $response = $this->client->getAcsResponse($this->request);
            //print_r($response);

            //返回状态值成功时进行处理
            if ("Success" == $response->Code) {
                $imageResults = $response->ImageResults->ImageResult;
                foreach ($imageResults as $imageResult) {
                    //黄图结果处理
                    $pornResult = $imageResult->PornResult;

                    return [
                        'rate' => $pornResult->Rate,
                        'label' => $pornResult->Label,
                    ];
                }
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }
}
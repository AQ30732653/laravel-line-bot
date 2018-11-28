<?php

namespace App\Repositories;

class LineBotRepository
{
    /**
        * 測試資料
        * 模板格式-分享好友推播廣告
        *
        * @return array
        */
    public function getShareData()
    {
        $content = "邀請「RO仙境傳說(手機版)」Bot好友\n";
        $content .= "活動大獎在等你\n";
        $content .= "點選以下連結加入Bot好友:\n";
        $content .= "https://line.me/R/ti/p/%40scp5298e\n";
        $content .= "QR Code連結:\n";
        $content .= "http://qr-official.line.me/L/9xblb85Lo9.png";

        $data = [
            "type" => "template",
            "altText" => "this is a buttons template",
            "template" => [
                "type" => "buttons",
                "actions" => [
                    [
                        "type" => "uri",
                        "label" => "分享好友加入",
                        "uri" => "https://line.me/R/msg/text/?" . urlencode($content)
                    ]
                ],
                "thumbnailImageUrl" => "https://i.imgur.com/ghS96TY.jpg",
                "title" => "RO仙境傳說(手機版)",
                "text" => "Bot 指令「分享」「網址」「活動」"
            ]
        ];

        return $data;
    }

    /**
        * 測試資料
        * 模板格式-網站連結
        *
        * @return array
        */
    public function getWebsiteData()
    {
        $data = [
            "type" => "template",
            "altText" => "this is a buttons template",
            "template" => [
                "type" => "buttons",
                "actions" => [
                    [
                        "type" => "uri",
                        "label" => "RO官方網站",
                        "uri" => "https://rom.gnjoy.com.tw/"
                    ], [
                        "type" => "uri",
                        "label" => "巴哈姆特",
                        "uri" => "https://forum.gamer.com.tw/A.php?bsn=28924"
                    ], [
                        "type" => "uri",
                        "label" => "FaceBook粉絲頁",
                        "uri" => "https://www.facebook.com/RO.ForeverLove/"
                    ]
                ],
                "thumbnailImageUrl" => "https://i.imgur.com/ghS96TY.jpg",
                "title" => "RO仙境傳說(手機版)",
                "text" => "遊戲熱門上線中"
            ]
        ];

        return $data;
    }

    public function getActivityText()
    {
        $text = "「RO仙境傳說(手機版)」\n";
        $text .= "最新活動，邀請您(主人)參與\n";
        $text .= "將以下邀請碼分享給好友\n";
        $text .= "好友拿著專屬於您的邀請碼，到活動頁登錄\n";
        $text .= "雙方各自可領到一組序號獎勵\n";
        $text .= "邀請碼:\n";
        $text .= "「abcd123546」\n";
        $text .= "活動網址:\n";
        $text .= "https://rom.gnjoy.com.tw/\n";
        $text .= "分享好友連結:\n";
        $text .= url('api/lineBot/shareActivity');
        $text .= "目前您已有x位好友登錄邀請碼";

        return $text;
    }
}

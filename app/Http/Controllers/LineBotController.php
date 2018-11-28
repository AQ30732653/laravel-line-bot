<?php

namespace App\Http\Controllers;

use App\Services\LineBotService;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotController extends Controller
{
    /**
        * channel access token
        * @var string
        */
    private $channelToken = 'your channel access token';
    /**
        * channel secret
        * @var string
        */
    private $channelSecret = 'your channel secret';
    protected $bot;
    protected $lineBotService;

    /**
        * LineBotController constructor.
        */
    public function __construct()
    {
        $httpClient = new CurlHTTPClient($this->channelToken);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channelSecret]);
        $this->lineBotService = new LineBotService();
    }

    /**
        * 推送文字訊息給 Line
        *
        * @param Request $request
        */
    public function push($userId, $content)
    {
        $content = new TextMessageBuilder($content);
        $this->bot->pushMessage($userId, $content);
    }

    /**
        * Line 推送資料給我方，我方即時回傳資料給 Line
        * Webhook URL
        *
        * @param Request $request
        * @throws LINEBot\Exception\InvalidEventRequestException
        * @throws LINEBot\Exception\InvalidSignatureException
        * @throws \ReflectionException
        */
    public function callback(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        $events = $this->bot->parseEventRequest($request->getContent(), $signature);

        foreach ($events as $event) {
            $multiMessageBuilder = new MultiMessageBuilder();

            // 加入好友
            if ($event->getType() === 'follow') {
                // 加入好友時，取得 user id
                $userId = $event->getUserId();
                $messageBuilder = $this->lineBotService->getMessageBuilder('分享');
                $multiMessageBuilder->add($messageBuilder);
            }

            // 發出訊息
            if($event->getType() === 'message') {
                // 文字訊息
                if ($event->getMessageType() === 'text') {
                    $command = $event->getText();
                    $messageBuilder = $this->lineBotService->getMessageBuilder($command);
                    if ($messageBuilder) $multiMessageBuilder->add($messageBuilder);
                }

                // 貼圖訊息
                if ($event->getMessageType() === 'sticker') {
                    $stickMsg = new StickerMessageBuilder(1, 2);
                    $multiMessageBuilder->add($stickMsg);
                    $textMsg = new TextMessageBuilder('你好!');
                    $multiMessageBuilder->add($textMsg);
                }
            }

            $this->bot->replyMessage($event->getReplyToken(), $multiMessageBuilder);
        }
    }

    /**
        * 分享活動內容給好友
        *
        * @return \Illuminate\Http\RedirectResponse
        */
    public function shareActivity()
    {
        $content = "「RO仙境傳說(手機版)」\n";
        $content .= "我(主人)邀請您參與活動\n";
        $content .= "到活動頁登錄以下邀請碼\n";
        $content .= "雙方各自可領到一組序號獎勵\n";
        $content .= "提醒您，該邀請碼只能登錄一次\n";
        $content .= "僅限「您」與「於分享給您的好友」使用\n";
        $content .= "如果您也想以主人的身分參與活動\n";
        $content .= "相關訊息請下指定「活動」\n";
        $content .= "邀請碼:\n";
        $content .= "「abcd123546」\n";
        $content .= "活動網址:\n";
        $content .= "https://rom.gnjoy.com.tw/";

        $url = "https://line.me/R/msg/text/?" . urlencode($content);
        return response()->redirectTo($url);
    }
}

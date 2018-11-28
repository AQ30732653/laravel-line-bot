<?php

namespace App\Services;

use App\Repositories\LineBotRepository;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineBotService
{
    protected $lineBotRepos;

    /**
        * LineBotService constructor.
        */
    public function __construct()
    {
        $this->lineBotRepos = new LineBotRepository();
    }

    /**
        *  依照指令，取得回傳資料
        *
        * @param $command
        * @return TemplateMessageBuilder
        */
    public function getMessageBuilder($command)
    {
        $messageBuilder = null;
        switch ($command) {
            case '分享':
                $data = $this->lineBotRepos->getShareData();
                $messageBuilder = $this->setTemplateMessageBuilder($data);
                break;
            case '網址':
                $data = $this->lineBotRepos->getWebsiteData();
                $messageBuilder = $this->setTemplateMessageBuilder($data);
                break;
            case '活動':
                $text = $this->lineBotRepos->getActivityText();
                $messageBuilder = new TextMessageBuilder($text);
                break;
        }

        return $messageBuilder;
    }

    /**
        * 取得模板格式資料
        *
        * @param $data
        * @return TemplateMessageBuilder
        */
    public function setTemplateMessageBuilder($data)
    {
        $uriTemplateActionBuilder = [];
        foreach ($data['template']['actions'] as $action) {
            $uriTemplateActionBuilder[] = new UriTemplateActionBuilder(
                $action['label'],
                $action['uri']
            );
        }

        $buttonTemplateBuilder = new ButtonTemplateBuilder(
            $data['template']['title'],
            $data['template']['text'],
            $data['template']['thumbnailImageUrl'],
            $uriTemplateActionBuilder
        );

        return new TemplateMessageBuilder($data['altText'], $buttonTemplateBuilder);
    }
}

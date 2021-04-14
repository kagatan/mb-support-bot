<?php


namespace App\Services\Telegram\Commands;


use App\Services\MikBill\API\API;
use Illuminate\Support\Facades\Cache;
use WeStacks\TeleBot\Handlers\CommandHandler;
use WeStacks\TeleBot\Objects\Update;
use WeStacks\TeleBot\TeleBot;

abstract class Command extends CommandHandler
{
    private $isAuth = false;
    private $user_id = -1;

    public function __construct(TeleBot $bot, Update $update)
    {
        parent::__construct($bot, $update);

        $this->check_access();

        var_dump($this->getMenu());
    }


    public function setMenu($menuName)
    {
        Cache::put($this->user_id . '_prev_menu', $menuName);
    }

    public function getMenu()
    {
        return Cache::get($this->user_id . '_prev_menu');
    }


    public function isAuth()
    {
        return $this->isAuth;
    }

    public function check_access()
    {
        $allowed_id = config('telebot.bots.bot.allowed_id');

        if (isset($this->update->message->from->id)) {
            $this->user_id = $this->update->message->from->id;
        } elseif (isset($this->update->callback_query->from->id)) {
            $this->user_id = $this->update->callback_query->from->id;
        }

        $this->isAuth = in_array($this->user_id, $allowed_id);
    }


    public function menuMain()
    {
        $this->setMenu('menuMain');

        $this->sendMessage([
            'text'         => '<b>Главное меню:</b>',
            'parse_mode'   => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [
                            "text"          => "🔍 Поиск",
                            "callback_data" => "menuSearch"
                        ],
                        [
                            "text"          => "⚡️ONU",
                            "callback_data" => "onuInfo"
                        ],
                    ],
                    [
                        [
                            "text"          => "🆘 Помощь",
                            "callback_data" => "menuHelp"
                        ]
                    ]
                ]
            ]
        ]);
    }
}

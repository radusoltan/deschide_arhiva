<?php

namespace App\Console\Commands;

use App\Models\ArticleTelegramPost;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class SendToTG extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trimite stirile publicate pe Telegram';

    protected $telegram;

    public function __construct(Api $telegram){
        parent::__construct();
        $this->telegram = $telegram;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $xml = simplexml_load_file('https://www.deschide.md/articole/rss.xml');
        $namespaces = $xml->getNamespaces(true);

        $items = [];
        foreach ($xml->channel->item as $item) {
            $tmp = new \stdClass();
            $tmp->title = trim((string) $item->title);
            $tmp->link  = trim((string) $item->link);
            $tmp->description = trim((string) $item->description);

            // Verifică dacă există un thumbnail în spațiul de nume media
            $media = $item->children($namespaces['media']);
            $tmp->media_url = isset($media->thumbnail) ? trim((string) $media->thumbnail->attributes()->url) : null;

            $items[] = $tmp;
        }

        $newscount = 5;

        while ($newscount > 0){

            $object = array_slice($items, 0, 5)[$newscount];

            $tgPost = ArticleTelegramPost::where('article_title', $object->title)->first();

            if (!$tgPost) {

                if ($object->media_url){
                    $response = $this->telegram->sendPhoto([
                        'chat_id' => '@deschide_test',
                        'caption' => "<b>{$object->title}</b>\n\n{$object->description}\n\n<a href=\"{$object->link}\">Citește mai mult</a>",
                        'photo' => InputFile::create($object->media_url),
                        'parse_mode' => 'HTML'
                    ]);
                } else {

                    $response = $this->telegram->sendMessage([
                        'chat_id' => '@deschide_test',
                        'text' =>  "<b>{$object->title}</b>\n\n{$object->description}\n\n<a href=\"{$object->link}\">Citește mai mult</a>",
                        'parse_mode' => 'HTML'
                    ]);
                }
                ArticleTelegramPost::create([
                    'article_title' => $object->title,
                    'telegram_message_id' => $response->messageId,
                ]);
            }
            $newscount--;
        }
    }
}

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

        // Trimite primele 5 articole
        $newscount = 10;

        while ($newscount > 0) {
            $object = $items[$newscount - 1]; // Ajustează indexul pentru a accesa corect elementele

            // Verifică dacă articolul a fost deja postat
            $existsOnTelegram = ArticleTelegramPost::where('article_title', $object->title)->exists();

            if (!$existsOnTelegram) {
                // Trimite mesajul pe Telegram
                $response = $this->telegram->sendPhoto([
                    'chat_id' => env('TELEGRAM_CHAT_ID'),
                    'caption' => "<b>{$object->title}</b>\n\n{$object->description}\n\n<a href=\"{$object->link}\">Citește mai mult</a>",
                    'photo' => InputFile::create($object->media_url),
                    'parse_mode' => 'HTML',
                ]);

                // Salvează ID-ul mesajului în baza de date
                ArticleTelegramPost::create([
                    'article_title' => $object->title,
                    'telegram_message_id' => $response->getMessageId()
                ]);
            }

            $newscount--;
        }
    }
}

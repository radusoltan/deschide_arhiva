<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleOserver;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Facebook\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // elastic bind
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts($app['config']->get('services.elastic.hosts'))
                ->setApiKey(env('ELASTIC_API_TOKEN'))
                ->setSSLVerification(false)
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        \Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('facebook', Provider::class);
        });
//        Article::observe(ArticleOserver::class);
    }
}

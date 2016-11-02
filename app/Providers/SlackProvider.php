<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maknz\Slack\Client;
use GuzzleHttp\Client as Guzzle;
class SlackProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['maknz.slack'] = $this->app->share(function($app)
        {
            return new Client(
                $app['config']->get('slack.endpoint'),
                [
                    'channel' => $app['config']->get('slack.channel'),
                    'username' => $app['config']->get('slack.username'),
                    'icon' => $app['config']->get('slack.icon'),
                    'link_names' => $app['config']->get('slack.link_names'),
                    'unfurl_links' => $app['config']->get('slack.unfurl_links'),
                    'unfurl_media' => $app['config']->get('slack.unfurl_media'),
                    'allow_markdown' => $app['config']->get('slack.allow_markdown'),
                    'markdown_in_attachments' => $app['config']->get('slack.markdown_in_attachments')
                ],
                new Guzzle
            );
        });

        $this->app['maknz.slack.level'] = $this->app->share(function($app)
        {
            return new Client(
                $app['config']->get('slack.endpointLevel'),
                [
                    'channel' => $app['config']->get('slack.channel'),
                    'username' => $app['config']->get('slack.username'),
                    'icon' => $app['config']->get('slack.icon'),
                    'link_names' => $app['config']->get('slack.link_names'),
                    'unfurl_links' => $app['config']->get('slack.unfurl_links'),
                    'unfurl_media' => $app['config']->get('slack.unfurl_media'),
                    'allow_markdown' => $app['config']->get('slack.allow_markdown'),
                    'markdown_in_attachments' => $app['config']->get('slack.markdown_in_attachments')
                ],
                new Guzzle
            );
        });

        $this->app->bind('Maknz\Slack\Client', 'maknz.slack');
        $this->app->bind('Maknz\Slack\Client', 'maknz.slack.level');
    }
}

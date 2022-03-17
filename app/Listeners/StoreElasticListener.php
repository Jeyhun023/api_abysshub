<?php

namespace App\Listeners;

use Elasticsearch\ClientBuilder;
use App\Events\StoreElasticEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreElasticListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $hosts;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->hosts = [
            [
                'host' => env("ELASTICSEARCH_HOST"),
                'port' => env("ELASTICSEARCH_PORT"),
                'scheme' => env("ELASTICSEARCH_SCHEME"),
                'user' => env("ELASTICSEARCH_USER"),
                'pass' => env("ELASTICSEARCH_PASS")
            ]
        ];
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(StoreElasticEvent $event)
    {
        $response = Http::acceptJson()->asForm()->post('https://django.abysshub.com/api/vectorize', [
            'data' => $event->data->title.' '.$event->data->content
        ]);
       
        $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build();
        $params['index'] = 'products';
        $params['id'] = $event->data->id;
        $params['body']['name'] = $event->data->name;
        $params['body']['slug'] = $event->data->slug;
        $params['body']['description'] = $event->data->description;
        $params['body']['tags'] = $event->data->tags;
        $params['body']['user'] = $event->data->user;
        $params['body']['shop'] = $event->data->shop;
        $params['body']['price'] = $event->data->price;
        $params['body']['rate'] = $event->data->rate;
        $params['body']['vector'] = json_decode($response->body());
        $params['body']['download_count'] = $event->data->download_count;
        $params['body']['created_at'] = $event->data->created_at;
        $params['body']['updated_at'] = $event->data->updated_at;
        $params['body']['deleted_at'] = $event->data->deleted_at;

        $client->index($params);
    }
}

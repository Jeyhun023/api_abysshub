<?php

namespace App\Listeners;

use Elasticsearch\ClientBuilder;
use App\Events\ThreadElasticEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThreadElasticListener implements ShouldQueue
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
    public function handle(ThreadElasticEvent $event)
    {
        $response = Http::acceptJson()->asForm()->post('https://django.abysshub.com/api/vectorize', [
            'data' => $event->data->title.' '.$event->data->content
        ]);
       
        $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build();
        $params['index'] = 'threads';
        $params['id'] = $event->data->id;
        $params['body']['title'] = $event->data->title;
        $params['body']['slug'] = $event->data->slug;
        $params['body']['description'] = $event->data->description;
        $params['body']['tags'] = $event->data->tags;
        $params['body']['type'] = $event->data->type;
        $params['body']['user'] = $event->data->user;
        $params['body']['product'] = $event->data->product;
        $params['body']['accepted_answer_id'] = $event->data->accepted_answer_id;
        $params['body']['upvote'] = $event->data->upvote_count;
        $params['body']['downvote'] = $event->data->upvote_count;
        $params['body']['comment_count'] = $event->data->comment_count;
        $params['body']['view_count'] = $event->data->view_count;
        $params['body']['answer_count'] = $event->data->answer_count;
        $params['body']['vector'] = json_decode($response->body());
        $params['body']['last_active_at'] = $event->data->last_active_at;
        $params['body']['created_at'] = $event->data->created_at;
        $params['body']['updated_at'] = $event->data->updated_at;
        $params['body']['closed_at'] = $event->data->closed_at;
        $params['body']['deleted_at'] = $event->data->deleted_at;

        $client->index($params);
    }
}

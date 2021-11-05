<?php

namespace App\Listeners;

use App\Events\NewSearchEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Elasticsearch\ClientBuilder;

class NewSearchListener implements ShouldQueue
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
    public function handle(NewSearchEvent $event)
    {
        $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 
   
        $params = [
            'index' => 'searchings',
            'body'  => [
                'query' => [
                    'term' => [
                        'query' => $event->query
                    ]
                ]
            ]
        ];
        $response = $client->search($params);

        $params['index'] = 'searchings';
        $params['body']['query'] = $event->query;

        if($response['hits']['total']['value'] != 0){
            $params['id'] = $response['hits']['hits'][0]['_id'];
            $params['body']['time'] = $response['hits']['hits'][0]['_source']['time'] + 1;
            $params['body']['created_at'] = $response['hits']['hits'][0]['_source']['created_at'];
        }else{
            $params['body']['time'] = 1;
            $params['body']['created_at'] = now();
        }

        $client->index($params);
    }
}


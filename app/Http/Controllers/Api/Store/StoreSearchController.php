<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Elasticsearch\ClientBuilder;
use App\Http\Resources\Store\StoreSearchCollection;
use App\Events\NewSearchEvent;

use App\Models\Product;

class StoreSearchController extends Controller
{
    use ApiResponser;
    public $hosts;
    public $user;
    
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
        $this->user = auth('api')->user();
    }

    public function index($query)
    {
        try {
            $from = (request()->input('from') !=null ) ? request()->input('from') : 0;
            $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 

            $params = [
                'index' => 'products',
                'size'  => 10,
                'from'  => $from,
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                [ 'multi_match' => [ 'query' => $query,
                                        'fields' => ['name^3', 'tags','description']
                                    ] 
                                ],
                            ]
                        ]
                    ]
                ]
            ];
            
            $response = $client->search($params);
            // event(new NewSearchEvent($query));

            activity('store')
                ->event('search')
                ->causedBy($this->user)
                ->withProperties(['query' => $query ])
                ->log( request()->ip() );

            return $this->successResponse([
                'total' => $response['hits']['total']['value'], 
                'from'  => $from,
                'max_score' => $response['hits']['max_score'], 
                'results' => new StoreSearchCollection($response['hits']['hits'])
            ], null);

        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

}

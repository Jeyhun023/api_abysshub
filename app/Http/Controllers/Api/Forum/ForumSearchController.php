<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Elasticsearch\ClientBuilder;
use App\Http\Resources\Forum\ForumSearchCollection;
use App\Events\NewSearchEvent;

use App\Models\User;

class ForumSearchController extends Controller
{
    use ApiResponser;
    public $hosts;
    
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

    public function index($query)
    {
        try {
            $from = (request()->input('from') !=null ) ? request()->input('from') : 0;
            $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 

            $params = [
                'index' => 'threads',
                'size'  => 10,
                'from'  => $from,
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                [ 'multi_match' => [ 'query' => $query,
                                        'fields' => ['title^3', 'tags','content']
                                    ] 
                                ],
                            ]
                        ]
                    ]
                ]
            ];
            
            $response = $client->search($params);

            event(new NewSearchEvent($query));

            return $this->successResponse([
                'total' => $response['hits']['total']['value'], 
                'from'  => $from,
                'max_score' => $response['hits']['max_score'], 
                'results' => new ForumSearchCollection($response['hits']['hits'])
            ], null);

        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    
    public function user($query)
    {
        try {
            $users = User::where('name', 'LIKE', '%' . $query . '%')->get();
            
            return $this->successResponse($users, null);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}

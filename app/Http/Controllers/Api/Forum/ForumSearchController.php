<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Elasticsearch\ClientBuilder;
use App\Http\Resources\Forum\ForumSearchCollection;

class ForumSearchController extends Controller
{
    use ApiResponser;

    public function index($query)
    {
        try {
            $hosts = [
                [
                    'host' => '13d6a30482e344d9b88a034ea728adc2.us-central1.gcp.cloud.es.io',
                    'port' => '9243',
                    'scheme' => 'https',
                    // 'path' => '/threads',
                    'user' => 'elastic',
                    'pass' => 'Q2Wt03kuyuNxgqqeJcSzAeAj'
                ]
            ];
    
            $client = ClientBuilder::create()->setRetries(2)->setHosts($hosts)->build(); 

            $params = [
                'index' => 'threads',
                'body'  => [
                    'query' => [
                        'match' => [
                            'title' => $query
                        ]
                    ]
                ]
            ];
            
            $response = $client->search($params);
        
            return $this->successResponse(new ForumSearchCollection($response['hits']['hits']), null);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}

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

    public function index()
    {
        try {
            $query = (request()->input('query') !=null ) ? request()->input('query') : 0;
            $from = (request()->input('from') !=null ) ? request()->input('from') : 0;
            $tags = (request()->input('tags') !=null ) ? explode(',',request()->input('tags')) : null;
            $must_not = (request()->input('must_not') !=null ) ? explode(',',request()->input('must_not')) : null;

            $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 

            $params['index'] = 'products';
            $params['size'] = 10;
            $params['from'] = $from;
            $params['body']['query']['bool']['should'][] = [ "multi_match" => ["query" => $query, "fields" => ['name^3', 'description']]];
            
            if($tags != null){
                foreach($tags as $tag){
                    $params['body']['query']['bool']['should'][] = [ "term" => ["tags" => $tag] ] ;
                }
                $params['body']['query']['bool']['minimum_should_match'] = 2;
                $params['body']['query']['bool']['boost'] = 1.0;
            }

            if($must_not != null){
                foreach($must_not as $tag){
                    $params['body']['query']['bool']['must_not'][] = [ "term" => ["tags" => $tag] ] ;
                }
            }

            $response = $client->search($params);
            event(new NewSearchEvent($query));

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

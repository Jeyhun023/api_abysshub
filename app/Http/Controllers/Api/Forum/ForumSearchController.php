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
use App\Models\Product;

use App\Models\Thread;

class ForumSearchController extends Controller
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

    public function change()
    {
        $threads = Thread::with(['answers' => function($query) {
            $query->with('linked');
            $query->with('comments');
        }])->get();
        foreach($threads as $thread) {
            // if($thread->answers->isNotEmpty()){
            //     if($thread->answers->first()->linked->isNotEmpty()){
            //         foreach($thread->answers->first()->linked as $linked){
            //             $linked->delete();
            //         }
            //     }
            //     if($thread->answers->first()->comments->isNotEmpty()){
            //         foreach($thread->answers->first()->comments as $comment){
            //             $comment->delete();
            //         }
            //     }
            //     $thread->answers->first()->delete();   
            //     $thread->decrement('answer_count');
            // }
            $tags = $thread->tags;
            return $tags;

        }

        return "yes";
        try {
            $query = request()->input('query');
            $from = (request()->input('from') !=null ) ? request()->input('from') : 0;
            $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 

            $params = [
                'index' => 'threads',
                'size'  => 10,
                'from'  => $from,
                'body' => [
                    'query' => [
                        'bool' => [
                            "should" => [
                                [ "term" => [ "tags" => "java" ] ],
                                [ "term" => [ "tags" => "php" ] ],
                                [ "multi_match" => [
                                        "query" => $query, 
                                        "fields" => ['title^3', 'tags','content']
                                    ]
                                ],
                            ],
                            "minimum_should_match" => 2,
                            "boost" => 1.0
                        ],
                    
                    ]
                ]
            ];
            
            $response = $client->search($params);
            return $response;
            event(new NewSearchEvent($query));

            activity('thread')
                ->event('search')
                ->causedBy($this->user)
                ->withProperties(['query' => $query ])
                ->log( request()->ip() );

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

    public function index()
    {
        try {
            $query = request()->input('query');
            $from = (request()->input('from') !=null ) ? request()->input('from') : 0;
            $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build(); 

            // 'bool' => [
            //     "should" => [
            //         [ 'query' => $query,
            //           'multi_match' => [ 'fields' => ['title^3', 'tags','content']] 
            //         ],
            //         [ "term" => [ "tags" => "important" ] ],
            //         [ "term" => [ "tags" => "revisit" ] ] 
            //     ],
            //     "minimum_should_match" => 1,
            //     "boost" => 1.0,
            // ]

            $params = [
                'index' => 'threads',
                'size'  => 10,
                'from'  => $from,
                'body' => [
                    'query' => [
                        'bool' => [
                            "should" => [
                                [ "term" => [ "tags" => "java" ] ],
                                [ "term" => [ "tags" => "php" ] ],
                                [ "multi_match" => [
                                        "query" => $query, 
                                        "fields" => ['title^3', 'tags','content']
                                    ]
                                ],
                            ],
                            "minimum_should_match" => 2,
                            "boost" => 1.0
                        ],
                    
                    ]
                ]
            ];
            
            $response = $client->search($params);
            return $response;
            event(new NewSearchEvent($query));

            activity('thread')
                ->event('search')
                ->causedBy($this->user)
                ->withProperties(['query' => $query ])
                ->log( request()->ip() );

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
      
    public function product($query)
    {
        try {
            $products = Product::where('name', 'LIKE', '%' . $query . '%')->get();
            
            return $this->successResponse($products, null);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}

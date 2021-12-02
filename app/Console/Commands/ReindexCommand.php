<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Thread;
use Elasticsearch\ClientBuilder;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all articles to Elasticsearch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $hosts;

    public function __construct()
    {
        parent::__construct();

        $this->hosts = [
            [
                'host' => "asd",
                'port' => env("ELASTICSEARCH_PORT"),
                'scheme' => env("ELASTICSEARCH_SCHEME"),
                'user' => env("ELASTICSEARCH_USER"),
                'pass' => env("ELASTICSEARCH_PASS")
            ]
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threads = Thread::with(['answers' => function($query) {
            $query->with('linked');
            $query->with('comments');
        },'user', 'category', 'product'])->where('id', '<=', 3500)->where('id', '>', 3000)->get();
        $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build();
        foreach($threads as $thread) {
            if($thread->answers->isNotEmpty()){
                if($thread->answers->first()->linked->isNotEmpty()){
                    foreach($thread->answers->first()->linked as $linked){
                        $linked->delete();
                    }
                }
                if($thread->answers->first()->comments->isNotEmpty()){
                    foreach($thread->answers->first()->comments as $comment){
                        $comment->delete();
                    }
                }
                $thread->answers->first()->delete();   
                $thread->decrement('answer_count');
            }

            $tags = collect(json_decode($thread->getRawOriginal('tags')));
            $thread->tags = $tags;
            $thread->save();

            $params['index'] = 'threads';
            $params['id'] = $thread->id;
            $params['body']['title'] = $thread->title;
            $params['body']['slug'] = $thread->slug;
            $params['body']['content'] = $thread->content;
            $params['body']['tags'] = $thread->tags;
            $params['body']['user'] = $thread->user;
            $params['body']['category'] = $thread->category;
            $params['body']['product'] = $thread->product;
            $params['body']['accepted_answer_id'] = $thread->accepted_answer_id;
            $params['body']['upvote'] = $thread->upvote;
            $params['body']['comment_count'] = $thread->comment_count;
            $params['body']['view_count'] = $thread->view_count;
            $params['body']['answer_count'] = $thread->answer_count;
            $params['body']['last_active_at'] = $thread->last_active_at;
            $params['body']['created_at'] = $thread->created_at;
            $params['body']['updated_at'] = $thread->updated_at;
            $params['body']['closed_at'] = $thread->closed_at;
            $params['body']['deleted_at'] = $thread->deleted_at;
    
            $client->index($params);

            echo $thread->id.'\r\n';
        }

    }
}

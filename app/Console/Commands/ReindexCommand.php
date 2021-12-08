<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;
use App\Models\Thread;
use App\Models\Answer;
use App\Http\Resources\Forum\ThreadResource;
use Illuminate\Support\Str;
use App\Events\ThreadElasticEvent;

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
                'host' => env("ELASTICSEARCH_HOST"),
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
        $time = time() + 353;
        $get_thread = Thread::find(1);
        $x = $get_thread->upvote;
        while ( true ) {
            $html = file_get_contents("https://stackoverflow.com/questions?tab=votes&page=".$x);
            preg_match_all('@<div class="question-summary" id="question-summary-(.*?)">(.*?)<div class="summary">(.*?)<h3>(.*?)<a href="(.*?)" class="question-hyperlink">(.*?)</a>(.*?)</h3>(.*?)</div>(.*?)</div>@si', $html, $threads);
            foreach($threads[5] as $thread){
                if(time() > $time){
                    break;
                }
                $thread_single = file_get_contents('https://stackoverflow.com'.$thread);
                preg_match_all('@<h1 itemprop="name" class="fs-headline1 ow-break-word mb8 flex--item fl1"><a href="(.*?)" class="question-hyperlink">(.*?)</a></h1>@si', $thread_single, $title);
                preg_match_all('@<div class="postcell post-layout--right">(.*?)<div class="s-prose js-post-body" itemprop="text">(.*?)</div>(.*?)</div>@si', $thread_single, $content);
                preg_match_all('@<a href="(.*?)" class="(.*?)" title="(.*?)" rel="tag">(.*?)</a>@si', $thread_single, $tags_array);
                preg_match_all('@<div class="answercell post-layout--right">(.*?)<div class="s-prose js-post-body" itemprop="text">(.*?)</div>(.*?)</div>@si', $thread_single, $answers_array);
                $title = $title[2][0];
                $content = $content[2][0];
                $tags = [];
                $answers = [];
                foreach($tags_array[4] as $tag){
                    if(!in_array($tag, $tags)){
                        array_push($tags, $tag);
                    }
                }
                for($n=0; $n < rand(0, count($answers_array[2]) ); $n++){
                    array_push($answers, $answers_array[2][$n]);
                }

                $thread = new Thread();
                $thread->user_id = 2;
                $thread->title = $title;
                $thread->slug = Str::slug($title);
                $thread->content = $content;
                $thread->tags = collect( $tags );
                $thread->last_active_at = now();
                $thread->type = 1;
                $thread->answer_count = $n;
                $thread->save();
                $thread = new ThreadResource($thread);
                event(new ThreadElasticEvent($thread));
                foreach($answers as $answer){
                    Answer::create([
                        'thread_id' => $thread->id, 
                        'user_id' => 2, 
                        'content' => $answer
                    ]);
                }
                sleep(2);
            }
            $x++;
            $get_thread->update(['upvote' => $x]);
         
            if(time() > $time){
                break;
            }
        }

        // for($x = 31033; $x <= 35000; $x += 1000){
        //     $threads = Thread::with(['answers' => function($query) {
        //         $query->with('linked');
        //         $query->with('comments');
        //     },'user', 'category', 'product'])->where('id', '<=', $x + 1000)->where('id', '>', $x)->get();
        //     $client = ClientBuilder::create()->setRetries(2)->setHosts($this->hosts)->build();
        //     foreach($threads as $thread) {
        //         if($thread->answers->isNotEmpty()){
        //             if($thread->answers->first()->linked->isNotEmpty()){
        //                 foreach($thread->answers->first()->linked as $linked){
        //                     $linked->delete();
        //                 }
        //             }
        //             if($thread->answers->first()->comments->isNotEmpty()){
        //                 foreach($thread->answers->first()->comments as $comment){
        //                     $comment->delete();
        //                 }
        //             }
        //             $thread->answers->first()->delete();   
        //             $thread->decrement('answer_count');
        //         }

        //         $tags = collect(json_decode($thread->getRawOriginal('tags')));
        //         $thread->tags = $tags;
        //         $thread->save();

        //         $params['index'] = 'threads';
        //         $params['id'] = $thread->id;
        //         $params['body']['title'] = $thread->title;
        //         $params['body']['slug'] = $thread->slug;
        //         $params['body']['content'] = $thread->content;
        //         $params['body']['tags'] = $thread->tags;
        //         $params['body']['user'] = $thread->user;
        //         $params['body']['category'] = $thread->category;
        //         $params['body']['product'] = $thread->product;
        //         $params['body']['accepted_answer_id'] = $thread->accepted_answer_id;
        //         $params['body']['upvote'] = $thread->upvote;
        //         $params['body']['comment_count'] = $thread->comment_count;
        //         $params['body']['view_count'] = $thread->view_count;
        //         $params['body']['answer_count'] = $thread->answer_count;
        //         $params['body']['last_active_at'] = $thread->last_active_at;
        //         $params['body']['created_at'] = $thread->created_at;
        //         $params['body']['updated_at'] = $thread->updated_at;
        //         $params['body']['closed_at'] = $thread->closed_at;
        //         $params['body']['deleted_at'] = $thread->deleted_at;
        
        //         $client->index($params);

        //         echo $thread->id. PHP_EOL;
        //     }
        // }
    }
}

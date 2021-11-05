<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSearchEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $query;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

}

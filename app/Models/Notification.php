<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $table = 'notifications';
    protected $guarded =['id'];

    protected $appends = ['created_at_human'];

    public function getCreatedAtHumanAttribute()
    {
        $diff = Carbon::parse($this->created_at)->diffForHumans(null, true); // true = short format
        return $diff . ' ago';
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public static function getPaginateData(){
        $notifications = Notification::where('notifiable_id', auth()->id())
            ->where('notifiable_type', get_class(auth()->user())) // important if multiple notifiable types
            ->latest()
            ->paginate(10);

        return $notifications;
    }

    public static function get($id){
        return Notification::where('notifiable_id', auth()->id())
            ->where('notifiable_type', get_class(auth()->user()))
            ->findOrFail($id);
    }

}

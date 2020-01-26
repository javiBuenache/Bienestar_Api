<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    protected $table = 'usages';
    protected $fillable = ['day','useTime','location','user_id','application_id'];
    
    public function register_usage($day,$useTime,$location,$user_id,$application_id)
    {
        $usage = new usage;
        $usage->day = $day;
        $usage->useTime = $useTime;
        $usage->location = $location;
        $usage->user_id = $user_id;
        $usage->application_id = $application_id;
        $usage->save();
    }

    public function get_usage($user_id)
    {
        $usages = DB::table('usages')->select('user_id','application_id','day',DB::raw("SUM(useTime) as totalTime"))
                                        ->from('usages')
                                        ->groupBy('application_id','user_id','day')
                                        ->get();
        return $usages;
    }
}

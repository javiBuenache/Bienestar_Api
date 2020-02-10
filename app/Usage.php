<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Usage extends Model
{
    protected $table = 'usages';
    protected $fillable = ['date', 'event', 'latitude', 'longitude'];
    

    public function get_usage($user_id)
    {
        $usages = DB::table('usages')->select('user_id','application_id','date',DB::raw("SUM(use_time) as total_time"))
                                        ->from('usages')
                                        ->where('user_id', $user_id)
                                        ->groupBy('application_id','user_id','date')
                                        ->get();
        return $usages;
    }

}

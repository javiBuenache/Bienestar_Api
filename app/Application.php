<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Application extends Model
{
    protected $table = 'applications';
    protected $fillable = ['icon','name'];

       
    public function register_app(Request $request)
    {
        $application = new Application();
        $application->icon = $request->name;
        $application->name = $request->icon;
        $application->save();
    }
    
    Public function in_use_app($name)
    {
        $applications = self::where('name',$name)->get();
        
        foreach ($applications as $key => $value)
         {
            if($value->name == $name){
                return true;
            }
        }
        return false;
    }
    Public function get_App()
    {
        $applications = self::all();
        return $applications;
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_have_applications')
                    ->withPivot('date', 'event', 'latitude', 'longitude') 
                    ->withTimestamps();                    
    }
    
    public function users_usages()
    {
        return $this->belongsToMany('App\Application', 'user_usage_applications')
                    ->withPivot('max_time', 'start_time', 'finish_time') 
                    ->withTimestamps();
    }
}

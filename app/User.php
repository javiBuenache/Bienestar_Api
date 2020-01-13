<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Helpers\Token;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name','email','password'];

    public function register(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = encrypt($request->password);
        $user->save();
    }
    public static function is_email_in_use($email)
    {  
        $users = User::where('email', $email)->get();

        foreach ($users as $key => $value) 
        {
            if ($value->email == $email) 
            {
                return true; 
            }
            return false;
        }
    }

    public static function by_field($key, $value)
    {
        $users = self::where($key, $value)->get();
        foreach ($users as $key => $user) {
            return $user;
        }
    }
    
    public function is_authorized(Request $request)
    {
        $token = new Token();
        $header = $request->header("Authorization");    
        if (!isset($header)) {
            return false;
        }
        $data = $token->decode($header);
        return !empty(self::by_field('email', $data->email));
    }


    public function apps()
    {
        return $this->belongsToMany('App\Application', 'user_have_applications')
                    ->withPivot('date', 'event', 'latitude', 'longitude')                    
                    ->withTimestamps();
    }
    public function usages_apps()
    {
        return $this->belongsToMany('App\Application', 'user_usage_applications')
                    ->withPivot('max_time', 'start_time', 'finish_time') 
                    ->withTimestamps();
    }
}

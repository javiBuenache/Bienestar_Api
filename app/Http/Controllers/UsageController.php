<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Usage;
use App\User;
use App\Application;

class UsageController extends Controller
{
    public function import_CSV(Request $request)
    {
        $user_email = $request->email;

        $user = User::where('email', $user_email)->first();

        $csv = $request->csv;

        $array_number = count($csv);

        $date_opens= new DateTime();
        $date_close = new DateTime();

        for ($i = 1; $i < $array_number; ++$i)
        {
            $usage = new Usage();
            $app_name = $csv[$i][1];
            $currentapp = Application::where('name', '=', $app_name)->first();

            if (isset($currentapp)) {
                $usage->date = $csv[$i][0];
                $usage->event = $csv[$i][2];
                $usage->latitude = $csv[$i][3];
                $usage->longitude = $csv[$i][4];

                $date = new DateTime($usage->date);

                if ($usage->event == "opens")
                 {
                    $date_opens = $date;
                    $time_used = 0;
                }
                else
                {
                    $date_close = $date;
                    $time_used = $date_close->getTimestamp() - $date_opens->getTimestamp();
                    $usage->use_time = $time_used;

                    $usage->user_id= $user->id;
                    $usage->application_id = $currentapp->id;


                    $usage->save();
                }
            }
        }
        return response()->json(["Message"=> "Datos importados"], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    public function show_locations(Request $request)
    {
        $user_email = $request->data->email;

        $user = User::where('email', $user_email)->first();

        $usages = Usage::where('user_id',$user->id)->get();

        $array_latitude = array();
        $array_longitude = array();

        for ($i=0; $i < count($usages); $i++) 
        { 
                array_push($array_latitude, $usages[$i]->latitude);
                array_push($array_longitude, $usages[$i]->longitude);
        }

        return response()->json(["latitude"=> $array_latitude, "longitude"=> $array_longitude], 200);
    }

    public function get_use_week($user)
    {
        $requestedDate = New DateTime();
        //Expresión en días del año
        $requestedDate = $requestedDate->format('W')-1;

        $apps = Application::all();

        $usages = Usage::whereRaw("WEEK(date) = $requestedDate")
            ->select('user_id', 'application_id',DB::raw("SUM(use_time) as total_time"))
            ->where('user_id', $user->id)
            ->groupBy('user_id','application_id','use_time')
            ->get();

        $time_help = 0; 
        $total_time = array();

        foreach ($apps as $key => $app) {
            foreach ($usages as $key => $usage) {
                if ($app->id == $usage->application_id) {
                    $time_help += $usage->total_time;
                }
            }
            array_push($total_time, $time_help);
            $time_help = 0;
        }
        return $total_time;
    }
    public function get_use_month($user)
    {
        $apps = Application::all();

        $requestedDate = New DateTime();
        //Expresión en días del año
        $requestedDate = $requestedDate->format('m');

        $usages = Usage::whereRaw("MONTH(date) = $requestedDate")
            ->select('user_id','application_id',DB::raw("SUM(use_time) as total_time"))
            ->where('user_id', $user->id)
            ->groupBy('user_id','application_id','use_time')
            ->get();
            
            $time_help = 0; 
            $total_time = array();
    
            foreach ($apps as $key => $app) 
            {
                foreach ($usages as $key => $usage) 
                {
                    if ($app->id == $usage->application_id) 
                    {
                        $time_help += $usage->total_time;
                    }
                }
                array_push($total_time, $time_help);
                $time_help = 0;
            }
        return $total_time;
    }

    public function get_use_all ($user)
    {
        $apps = Application::all();

        $requestedDate = New DateTime();
        //Expresión en días del año
        $requestedDate = $requestedDate->format('Y');

        $usages = Usage::whereRaw("YEAR(date) = $requestedDate")
            ->select('user_id','application_id',DB::raw("SUM(use_time) as total_time"))
            ->where('user_id', $user->id)
            ->groupBy('user_id','application_id','use_time')
            ->get();
            
            $time_help = 0; 
            $total_time = array();
    
            foreach ($apps as $key => $app) 
            {
                foreach ($usages as $key => $usage) 
                {
                    if ($app->id == $usage->application_id)
                     {
                        $time_help += $usage->total_time;
                    }
                }
                array_push($total_time, $time_help);
                $time_help = 0;
            }
        return $total_time;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user_email = $request->data->email;
        $user = User::where('email',$user_email)->first();  
        
        $usage = new Usage();
        $usages = $usage->get_usage($user->id);

        $date = new DateTime();
        $date = $date->format('Y-m-d');

        $apps = Application::all();
        $today_time = array();

        $names = array();
        $icons = array();

        $time_help = 0;

        foreach ($usages as $key => $usage) 
        {
            if ($usage->date == $date) 
            {   
                $app = Application::where('id', $usage->application_id)->first();
                array_push($names, $app->name);
                array_push($icons, $app->icon);   
                $time_help += $usage->total_time;
                array_push($today_time, $time_help); 
                $time_help = 0;
            }
        }
        if (count($names) < count($apps)) 
        {
            foreach ($apps as $key => $app)
             {
                if (isset($names[$key])) 
                {
                    if ($app->name != $names[$key]) 
                    {
                        array_push($names, $app->name);
                        array_push($icons, $app->icon);   
                        array_push($today_time, $time_help); 
                    }
                }else
                {
                        array_push($names, $app->name);
                        array_push($icons, $app->icon);   
                        array_push($today_time, $time_help); 
                }
                
            }
    }
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

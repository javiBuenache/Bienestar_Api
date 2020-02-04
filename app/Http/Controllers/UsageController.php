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

    public function show_locations(Request $request)
    {
        
        $user_email = $request->data->email;
        
        $user = User::where('email', '=', $user_email)->first(); 

        $user_id = $user->id;

        $location= DB::table('usages')
        ->select('latitude', 'longitude')
        ->distinct()
        ->get();

        return response()->json(
            $location
        ,201);
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

   
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
       
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

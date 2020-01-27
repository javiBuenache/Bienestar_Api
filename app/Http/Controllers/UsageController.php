<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DateTime;
use App\Usage;
use App\User;
use App\Application;

class UsageController extends Controller
{
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
        // recivir CSV y añadirlo a la base de datos
        /*
            Day -> date (dia de hoy)
            useTime -> Time ((Hora de apertura - Hora de cierre) + useTime anterior)
            location -> string
            user_id (lo sacamos del token)
            application_id (lo sacamos con el nombre de la app)
        */

        $csv = array_map('str_getcsv' ,file('/Applications/MAMP/htdocs/usage.csv'));
        $countArray = count($csv);

        $user_email = $request->data->email;
        $request_user = User::where('email', $user_email)->first();
        
        for ($i=1; $i < $countArray ; $i++)
        { 

            $openDate = new DateTime ($csv[$i][0]);
            $application = $csv[$i][1];
            $openLocation = $csv[$i][3] . "," . $csv[$i][4];

            $i++;

            $closeDate =  new DateTime ($csv[$i][0]);
            // $timeUsed se guarda en segundos 
            $timeUsed = $closeDate->getTimestamp() - $openDate->getTimestamp();

            $application = Application::where('name',$application)->first();

            if (isset($application)) 
            {

                $newUsage = new usage();
                $newUsage->register_usage($openDate,$timeUsed,$openLocation,$request_user->id,$application->id);   
            }               
        }
        return response()->json(["Success" => "Se ha añadido el uso de todas las aplicaciones"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user_email = $request->data->email;
        $request_user = User::where('email', $user_email)->first();

        $usage = new Usage();        
        $usages = $usage->get_usage($request_user->id);

        //var_dump($usages);exit();
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

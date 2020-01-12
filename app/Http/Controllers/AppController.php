<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Application;


class AppController extends Controller
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
        $application = new Application();

        if(!$application->in_use_app($request->name))
        {
            $application->register_app($request);

            return response()->json(["Success" => "Se ha añadido la aplicacion."]);
        }else
        {
            return response()->json(["Error" => "La aplicacion ya existe"]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $application = new Application();
        $applications = $application->get_App();

        if(isset($applications))
        {
           
            return response()->json(["Success" => $applications]);
        }else{
            return response()->json(["Error" => "No hay aplicaciones"]);
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
        $application = Application::where('name',$request->name)->first();

        if (isset($application)) 
        {
            $application->icon = $request->icon;
            $application->name = $request->name;
            $application->update();
        
            return response()->json(["Success" =>  "Modificado la aplicacion."]);
        }else
        {
            return response()->json(["Error" => "La aplicacion no existe"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $application = Application::where('name',$request->name)->first();

        if (isset($application)) 
        {
            $application->delete();
        
            return response()->json(["Success" => "Se ha borrado la aplicacion."]);
        }else
        {

            return response()->json(["Error" => "La aplicacion no existe"]);
        }
    }
}

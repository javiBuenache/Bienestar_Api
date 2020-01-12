<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Application;
use App\Helpers\Token;

class UserController extends Controller
{

    public function login(Request $request)
    {
        if ($request->email == null || $request->password == null) 
        {
            return response()->json([
                'alert' => 'Error: Inserte un email y un password'],
                400
            );
        }
        $data_token = ['email'=>$request->email];
        
        $user = User::where($data_token)->first();  
       
        if ($user!=null) 
        {       
            if($request->password == $user->password)
            {       
                $token = new Token($data_token);
                $token_coded = $token->encode();
                return response()->json(["token" => "usuario correcto", $token_coded], 201);
            }   
        }     
        return response()->json(["Error" => "No se ha encontrado"], 401);
    }

    public function recuperate_password(Request $request)
    {
        $user = User::where('email',$request->email)->first();  

        if (isset($user)) 
        {   
            $newPassword = self::randomPassword();
            self::sendEmail($user->email,$newPassword);
            $user->password = $newPassword;
            $user->update();
            return response()->json(["Success" => "contraseña nueva"]);
        }else
        {
            return response()->json(["Error" => "no existe email"]);
        }
    }
    public function sendEmail($email,$newPassword)
    {
        $to     =  $email;
        $subjet    = 'Recuperar contraseña';
        $message   = 'Su nueva contraseña es: "'.$newPassword.'"';
        //print($mensaje);exit();
        mail($to, $subjet, $message);
    }
    
    public function randomPassword() 
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwz1234567890';
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1; 
        for ($i = 0; $i < 10; $i++) 
        {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public function import_CSV(Request $request)
    {
        $request_user = $request->user; 
        $csv = array_map('str_getcsv', file('/Applications/MAMP/htdocs/Bienestar-CSV/usage.csv'));   

        foreach ($sv as $key => $column) 
        {                 
            if($key != 0)
            {
                $name = $column[1];             
                $app = Application::where('name', '=', $name)->first();
                
                $request_user->apps()->attach($app->id, 
                [
                    'date' => $column[0], 
                    'event' => $column[2],                      
                    'latitude' => $column[3],
                    'longitude' => $column[4],
                ]); 
            }
    
        }
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
        
        if ($request->name == null || $request->email == null || $request->password == null) 
        {
            return response()->json([
                'alert' => 'Error: Inserte un nonbre, un email y un password'],
                400
         );
        }

        $user = new User();

        if (!$user->is_email_in_use($request->email)){
            $user->register($request);
            $data_token = [
                "email" => $user->email,
            ];
            $token = new Token($data_token);
            
            $token_coded = $token->encode();
            return response()->json([
                "token" => $token_coded
            ], 201);
        }else
        {
            return response()->json(["Error" => "El email ya existe"]);
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
        //
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
        $user = user::where('email',$request->data_token->email)->first();

        if (isset($user)) 
        {
            
            $user->name = $request->name;
            $user->password = $request->password;
            $user->update();
        
            return response()->json(["Success" => "Se ha modificado el usuario."]);
        }else
        {
            return response()->json(["Error" => "El usuario no existe"]);
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
        $email = $request->data_token->email;
        $user = User::where('email',$email)->first();

        if (isset($user))
         {            
            if ($request->password == $user->password) 
            {
               $user->delete();            
                return response()->json(["Success" => "Se ha borrado el usuario."]);
            }else{
                return response()->json(["Error" => "la contraseña no coincide"]);
            }
        }else{
            return response()->json(["Error" => "El ususario no existe"]);
        }
    }
}

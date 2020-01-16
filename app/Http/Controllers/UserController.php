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
                401
            );
        }
       
        $user = User::where('email', '=', $request->email)->first();

        if ($user!=null) 
        {      
            $decrypted_user_password = decrypt($user->password);
            //var_dump($decrypted_user_password);exit;
        }else
        {
            return response()->json([
                "message" => "datos incorrectos",
            ], 401);
        }
        if($decrypted_user_password == $request->password)
        {
            $token = new Token(["email" => $user->email]);
            $coded_token = $token->encode();
            return response()->json([
                
                "token" => 'Usuario logueado: "'.$coded_token.'"',
            ], 200);
        }else
        {
            return response()->json([
                "message" => "datos incorrectos",
            ], 400);
        }
    }

    public function recuperate_password(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first(); 
        
        if (isset($user)) 
        {  
            $newPassword = self::randomPassword();
            self::sendEmail($user->email,$newPassword);
            $user->password = encrypt($newPassword);
            $user->update();
            return response()->json(["Success" => "contrasena nueva"],200);
        }else
        {
            return response()->json(["Error" => "no existe email"],400);
        }
    }
    public function sendEmail($email,$newPassword)
    {
        $to     =  $email;
        $subjet    = 'Recuperar contraseña';
        $message   = 'Su nueva contrasena es: "'.$newPassword.'"';
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
        $user_email = $request->data->email;
        
        $request_user = User::where('email', $user_email)->first();
       
        $csv = array_map('str_getcsv', file('/Applications/MAMP/htdocs/CSV-Bienestar/usage.csv'));   
        $array_number= count($csv);
        //var_dump($csv); exit;
        
        foreach ($csv as $array_number => $column) 
        {                 
            if($array_number != 0)
            {
                $name = $column[1];          
                $app = Application::where('name', '=', $name)->first();
                  //var_dump($column[1]); exit;     
                $request_user->apps()->attach(
                    $app->id, 
                [
                    'date' => $column[0], 
                    'event' => $column[2],                      
                    'latitude' => $column[3],
                    'longitude' => $column[4]
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
            return response()->json(["Error" => "El email ya existe"],400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $email = $request->data_token->email;

        $user = User::where('email',$email)->first();

        if(isset($user))
        {
            $user->password = encrypt($request->password);

            return response()->json(["Success" => $user],200);
        }else
        {
            return response()->json(["Error" => "El ususario no existe"],401);
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
        $user_email = $request->data->email;
        
        $request_user = User::where('email', $user_email)->first();

        $user_id = $request_user->id;

        if($user_id!=$id)
        {
            return response()->json([
                "message" => 'Error, solo puedes editar tu usuario'
            ],401);
        }

        if($request->name==NULL || $request->email==NULL || $request->password==NULL)
        {
            return response()->json([
                "message" => 'Debes rellenar todos los campos'
            ],401);
        }

        $request_user->name = $request->name;
        $request_user->email = $request->email;
        $request_user->password = encrypt($request->password);
        $request_user->save();

        return response()->json([
            "message" => 'Actualizados los nuevos datos'
        ],200);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
       $user_email = $request->data->email;
        
       $request_user = User::where('email', $user_email)->first();

        if (isset($request_user))
         {            
            if ($request->password == encrypt($request->password)) 
            {
               $request_user->delete();    

                return response()->json(["Success" => "Se ha borrado el usuario."]);
            }else
            {
                return response()->json(["Error" => "la contraseña no coincide"]);
            }
        }else
        {
            return response()->json(["Error" => "El ususario no existe"]);
        }
    }
}

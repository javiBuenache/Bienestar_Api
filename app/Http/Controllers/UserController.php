<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Application;
use App\Helpers\Token;
use DateTime;


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
                
                "token" => $coded_token
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
            $newPassword = self::random_password();
            self::send_email($user->email,$newPassword);
            $user->password = encrypt($newPassword);
            $user->update();
            return response()->json(["Success" => "contrasena nueva"],200);
        }else
        {
            return response()->json(["Error" => "no existe email"],400);
        }
    }
    public function send_email($email,$new_password)
    {
        $to     =  $email;
        $subjet    = 'Recuperar contraseña';
        $message   = 'Su nueva contrasena es: "'.$new_password.'"';
        //print($mensaje);exit();
        mail($to, $subjet, $message);
    }
    
    public function random_password() 
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
            ], 200);
        }else
        {
            return response()->json(["Error" => "El email ya existe"],401);
        }
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
        
        $user = User::where('email', '=', $user_email)->first(); 
    
        return response()->json([

            "name" => $user->name,
            "email" => $user->email, 
        ], 200);
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
    public function update(Request $request)
    {
        $user_email = $request->data->email;
        
        $request_user = User::where('email', $user_email)->first();

        $current_password = decrypt($request_user->password);
       
        if($current_password == $request->new_password)
        //var_dump($request->new_password);exit;
        {
            return response()->json([

                "message" => "tiene que ser la contrasena distinta que la anterior", 
    
            ], 400);
 
        }
        
        if($request->new_password == $request->repeat_new_password)
        {
            $request_user->password = encrypt($request->new_password);
            $request_user->save();

            return response()->json([

                "new password" => $request->new_password,
    
            ], 200);

        }else{
            
            return response()->json([

                "message" => "no tienes permisos", 
    
            ], 400);

        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
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

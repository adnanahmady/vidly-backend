<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Http\Controllers\AuthController;

class UsersController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  public function index()
  {
    $users = Auth::user()->movies()->get();

    if (!empty($user)) {
      return response()->json([
        'status' => 'success',
        'message' => '',
        'result' => $user
      ]);
    }

    return response()->json([
      'status' => 'error',
      'message' => 'user have no movies yet'
    ]);
  }

  public function userMovie($id)
  {
    $user = Auth::user()->movies()->find($id);

    if (!empty($user)) {
      return response()->json([
        'status' => 'success',
        'message' => '',
        'result' => $user
      ]);
    }

    return response()->json([
      'status' => 'error',
      'message' => 'there is no movie with this id'
    ]);
  }

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|unique:users,email',
      'username' => 'required',
      'password' => 'required|min:5',
      'is_admin' => 'required'
    ]);

    $params = $request->all();
    $params['password'] = Hash::make($params['password'], ['rounds' => 10]);
    $params['is_admin'] = $request->is_admin ? $request->is_admin : false;

    if (!$validator->fails()) {
      if ($user_id = User::insertGetId($params)) {
        $user = (object)$params;
        $user->id = $user_id;
        $params['token'] = AuthController::jwt($user);

        return response()->json([
          'status' => 'success',
          'message' => 'user added successfuly',
        ])->header('x-auth-token', $params['token']);
      }
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'expected',
        'result' => $validator->errors()
      ], 401);
    }

    return response()->json(['status' => 'error', 'message' => ['unexpected' => 'there wase some problems']], 401);
  }

  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|min:5'
    ]);

    $user = User::where('email', $request->email)->first();
    if (!empty($user) && !$validator->fails()) {
      if (Hash::check($request->password, $user->password)) {
        $token = AuthController::jwt($user);

        return response()->json([
          'status' => 'success',
          'message' => '',
          'token' => $token
        ]);
      }
    }

    return response()->json([
      'status' => 'error',
      'message' => 'Email OR Password Is Incorract',
      'result' => $validator->errors()
    ], 401);
  }
}

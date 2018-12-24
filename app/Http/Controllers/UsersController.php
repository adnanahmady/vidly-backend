<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Http\Controllers\AuthController;
use App\Exceptions\TwoTypeException;

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

    throw new Exception(
      'user have no movies yet',
      400
    );
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

    throw new Exception(
      'there is no movie with this id',
      400
    );
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
      throw new ValidateException(
        'expected error',
        $validator,
        401
      );
    }

    throw new TwoTypeException(
      'unexpected error',
      'there wase some problems',
      401
    );
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

    throw new ValidateException(
      'Email OR Password Is Incorract',
      $validator,
      401
    );
  }
}

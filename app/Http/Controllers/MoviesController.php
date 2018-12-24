<?php

namespace App\Http\Controllers;

use Auth;
use \Exception;
use App\Movie;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;
use Sentry\SentryLaravel\SentryFacade;
use App\Exceptions\ValidateException;
use App\Exceptions\TwoTypeException;

class MoviesController extends Controller
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
    $data = Movie::with(['genre'])
      ->withCount('likes as like')
      ->get();

    return response()->json($data);
  }

  public function getMovie($id)
  {
    if (preg_match("/[\D+]+/", $id)) {
      throw new Exception('movie id must be integer', 400);
    }

    $data = Movie::where(['id' => $id])->first();

    if (!$data) {
      throw new Exception('movie does not exist', 404);
    }

    return response()->json($data);
  }

  public function getMovieWithGenre($id)
  {
    if (preg_match("/[\D+]+/", $id)) {
      throw new Exception('movie id must be integer', 400);
    }

    $data = Movie::with('genre')
      ->withCount('likes as like')
      ->where(['id' => $id])
      ->first();

    if (!$data) {
      throw new Exception('movie does not exist', 404);
    }

    return response()->json($data);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string',
      'number_in_stock' => 'required|numeric|min:0|max:100',
      'daily_rental_rate' => 'required|numeric|min:0|max:10',
      'genre_id' => 'required|integer|exists:genres,id',
    ]);

    if (!$validator->fails()) {
      $movie = Movie::insertGetId([
        'title' => $request->title,
        'number_in_stock' => $request->number_in_stock,
        'daily_rental_rate' => $request->daily_rental_rate,
        'genre_id' => $request->genre_id,
        'created_at' => date("Y-m-d H:i:s"),
      ]);

      if ($movie) {
        return response()->json([
          'status' => 'success',
          'message' => 'Movie Added Successfuly!!',
          'inserted_id' => $movie
        ]);
      }
    }

    throw new ValidateException(
      'there was some problems with Adding movie', 
      $validator,
      400
    );
  }

  public function edit(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'id' => 'required|integer|exists:movies,id',
      'title' => 'required|string',
      'number_in_stock' => 'required|numeric|min:0|max:100',
      'daily_rental_rate' => 'required|numeric|min:0|max:10',
      'genre_id' => 'required|integer|exists:genres,id',
    ]);

    $_id = $request->id;
    settype($_id, 'string');

    if (!$validator->fails() && $id === $_id) {
      $movie = Movie::where(['id' => $id])->update([
        'title' => $request->title,
        'number_in_stock' => $request->number_in_stock,
        'daily_rental_rate' => $request->daily_rental_rate,
        'genre_id' => $request->genre_id,
        'updated_at' => date("Y-m-d H:i:s"),
      ]);

      if ($movie) {
        return response()->json([
          'status' => 'success',
          'message' => 'Movie Edited Successfuly!!'
        ]);
      }
    } elseif ($id !== $_id) {
      throw new TwoTypeException(
        'there wase a problem during Editing the movie',
        'id`s must be the same',
        400
      );
    }

    throw new ValidateException(
      'there was some problems with Editing movie',
      $validator,
      400
    );
  }

  public function setLike(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:movies,id',
    ]);

    $_id = $request->id;
    settype($_id, 'string');

    if (!$validator->fails() && $id === $_id) {
      $like = Auth::user()->likes()->toggle($_id, ['created_at' => date('Y-m-d H:i:s')]);

      if ($like['attached']) {
        return response()->json([
          'status' => 'success',
          'message' => 'Movie liked',
          'like' => true
        ]);
      } elseif ($like['detached']) {
        return response()->json([
          'status' => 'success',
          'message' => 'Movie disliked',
          'like' => false
        ]);
      }
    } elseif ($id !== $_id) {
      throw new TwoTypeException(
        'there wase a problem during Updating the movie`s like',
        'id`s must be the same',
        400
      );
    }
    
    throw new ValidateException(
      'there was some problems with updating movies like',
      $validator,
      400
    );
  }

  public function destroy(Request $request, $id)
  {
    $token = $request->header('x-auth-token');
    $token = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    if (!$token->is_admin) {
      throw new Exception(
        'data can not be deleted',
        400
      );
    }

    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:movies,id'
    ]);

    $_id = $request->id;
    settype($_id, 'string');

    if (!$validator->fails() && $_id === $id) {
      $movie = Movie::where([
        'id' => $request->id
      ])->delete();

      if ($movie) {
        return response()->json([
          'status' => 'success',
          'message' => 'Movie Deleted Successfuly!!'
        ]);
      }
    } elseif ($id !== $_id) {
      throw new TwoTypeException(
        'there wase a problem during deleteing the movie',
        'id`s must be the same',
        400
      );
    }

    throw new ValidateException(
      'movie is already Deleted',
      $validator,
      404
    );
  }
}

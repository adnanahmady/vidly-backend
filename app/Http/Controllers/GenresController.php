<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use App\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenresController extends Controller
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
    $data = Genre::all();

    return response()->json($data);
  }

  public function getGenre($id)
  {
    try {
      $data = Genre::with('movies')
        ->where(['id' => $id])
        ->first();
    } catch (RelationNotFoundException $exception) {
      return response()->json([
        'status' => 'error',
        'message' => 'there wase some problem'
      ], 400);
    } catch (Exception $exception) {
      if (env('APP_DEBUG', false)) {
        return response()->json([
          'status' => 'error',
          'message' => $exception->getMessage,
          'code' => $exception->getCode()
        ], 400);
      }

      return response()->json([
        'status' => 'error',
        'message' => 'it seems there is some problem'
      ], 400);
    }


    return response()->json($data);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'slug' => 'required|string|unique:genres,slug',
      'name' => 'required|string|unique:genres,name',
    ]);

    if (!$validator->fails()) {
      $genre = Genre::insert([
        'slug' => $request->slug,
        'name' => $request->name,
        'created_at' => date("Y-m-d H:i:s"),
      ]);

      if ($genre) {
        return response()->json([
          'status' => 'success',
          'message' => 'Genre Added Successfuly!!'
        ]);
      }
    }

    return response()->json([
      'status' => 'error',
      'message' => 'An error occured during Adding Genre!!',
      'devMessage' => $validator->errors()
    ]);
  }

  public function edit(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'id' => 'required|integer|exists:genres,id',
      'name' => 'required|string|unique:genres',
    ]);

    if (!$validator->fails() && $id === $request->id) {
      $genre = Genre::where(['id' => $id])->update([
        'name' => $request->name,
      ]);

      if ($genre) {
        return response()->json([
          'status' => 'success',
          'message' => 'Genre Updated Successfuly!!'
        ]);
      }
    }

    return response()->json([
      'status' => 'error',
      'message' => 'An error occured during Updating Genre!!',
      'devMessage' => $validator->errors()
    ]);
  }

  public function destroy(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'id' => 'required|integer|exists:genres,id'
    ]);

    $_id = $request->id;
    settype($_id, 'string');

    if (!$validator->fails() && $id === $_id) {
      $genre = Genre::where([
        'id' => $_id
      ])->delete();

      if ($genre) {
        return response()->json([
          'status' => 'success',
          'message' => 'Genre Deleted Successfuly!!'
        ]);
      }
    }

    return response()->json([
      'status' => 'error',
      'message' => 'An error occured during Deleting Genre!!',
      'devMessage' => $validator->errors()
    ]);
  }
}

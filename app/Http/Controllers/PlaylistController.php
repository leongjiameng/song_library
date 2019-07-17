<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Playlist;
use App\Http\Resources\PlaylistCollection;
use App\Http\Resources\PlaylistResource;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');

        $playlists = Playlist::with('songs')->with('user')
            ->when($name, function($query) use($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->paginate(10);

        return new PlaylistCollection($playlists);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $playlist = new Playlist;
            $user =auth()->user();
            $request->validate([
                'name'             => 'required|max:150',
                'songs'=> 'required'
            ]);
            $playlist->fill($request->all());
            $playlist->user_id = $user->user_id;
            DB::transaction(function() use($playlist, $request) {
                $playlist->saveOrFail();
                $playlist->songs()->sync($request->songs);
            });

            return response()->json([
                'id' => $playlist->id,
                'created_at' => $playlist->created_at,
            ], 201);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
        catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
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
        try {
            $playlist = Playlist::with('songs')::with('user')->find($id);
            if(!$playlist) throw new ModelNotFoundException;

            return new PlaylistResource($playlist);
        }
        catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
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
        try {
            $playlist = Playlist::find($id);
            if(!$playlist) throw new ModelNotFoundException;

            $user =auth()->user();
            if($playlist->user_id != $user->id)
            {
                 return response()->json([
                    'error' => 403,
                    'message' => 'Action Unauthorized'
                ], 403);
            }
            $playlist->fill($request->all());

            DB::transaction(function() use($playlist, $request) {
                $playlist->saveOrFail();
                if( $request->songs != null)
                {
                    $playlist->songs()->sync($request->songs);
                }
            });
            return response()->json(null, 204);
        }
        catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
        catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
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
        $playlist = Playlist::find($id);
        $user =auth()->user();
        if($playlist->user_id != $user->id){
            return response()->json([
                'error' => 403,
                'message' => 'Action Unauthorized'
            ], 403);
        }



        if(!$playlist) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found'
            ], 404);
        }

        $playlist->delete();

        return response()->json(null, 204);
    }



}

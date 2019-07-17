<?php
namespace App\Http\Controllers;
use DB;
use App\Song;
use App\Http\Resources\SongCollection;
use App\Http\Resources\SongResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $year = $request->input('year');
        $artist = $request->input('artist');

        $songs = Song::with(['artists'])
            ->whereHas('artists', function($query) use($artist) {
                return $query->where('name', 'like', "%$artist%");
            })
            ->when($title, function($query) use($title) {
                return $query->where('title', 'like', "%$title%");
            })
            ->when($year, function($query) use($year) {
                return $query->where('year', $year);
            })
            ->paginate(10);

        return new SongCollection($songs);
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
            $song = new Song;
            $request->validate([
                'title'             => 'required|max:150',
                'genre'             => 'required|max:150',
                'year'             => 'required',
                'artists'             => 'required',
                'length'             => 'required',
            ]);
            $song->fill($request->all());

            DB::transaction(function() use($song, $request) {
                $song->saveOrFail();
                $song->artists()->sync($request->artists);
            });

            return response()->json([
                'id' => $song->id,
                'created_at' => $song->created_at,
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
            $song = Song::with('artists')->find($id);
            if(!$song) throw new ModelNotFoundException;

            return new SongResource($song);
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
            $song = Song::with('artists')->find($id);
            if(!$song) throw new ModelNotFoundException;

            $song->fill($request->all());

            DB::transaction(function() use($song, $request) {
                $song->saveOrFail();
                $song->artists()->sync($request->artists);
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
        $song = Song::find($id);

        if(!$song) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found'
            ], 404);
        }

        $song->delete();

        return response()->json(null, 204);
    }
}

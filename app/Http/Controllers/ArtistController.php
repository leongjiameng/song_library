<?php
namespace App\Http\Controllers;
use App\Artist;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArtistController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');

        $artists = Artist::with('songs')
            ->when($name, function($query) use($name) {
                return $query->where('name', 'like', "%$name%");
            })
            ->paginate(10);

        return new ArtistCollection($artists);
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
            $artist = new Artist;
            $request->validate([
                'name'             => 'required|max:150',
                'type'             => 'required|max:150'
            ]);
            $artist->fill($request->all());

            $artist->saveOrFail();

            return response()->json([
                'id' => $artist->id,
                'created_at' => $artist->created_at,
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
            $artist = Artist::with('songs')->find($id);
            if(!$artist) throw new ModelNotFoundException;
            return new ArtistResource($artist);
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
            $artist = Artist::find($id);
            if(!$artist) throw new ModelNotFoundException;

            $artist->fill($request->all());

            $artist->saveOrFail();

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
        $artist = Artist::find($id);

        if(!$artist) {
            return response()->json([
                'error' => 404,
                'message' => 'Not found'
            ], 404);
        }

        $artist->delete();

        return response()->json(null, 204);
    }



}

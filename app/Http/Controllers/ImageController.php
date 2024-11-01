<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image as ImageManager;

class ImageController extends Controller
{

    private $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

//        dump($request->image);
        return response()->json($this->imageService->uploadImage($request->image));
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }

    public function getImgSrc(Request $request){

        if($request->has('ImageId')) {

            $image = Image::where('old_number', $request->get('ImageId'))->first();

            return response()->json([
                'url' => env('APP_URL').'/'.$image->path.$image->name
            ]);

        }

    }

    public function importImage(Request $request){

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $imageUrl = env('ARHIVA_URL')."/images/{$request->image}";

        try {
            $response = @file_get_contents($imageUrl, false, stream_context_create($arrContextOptions));
            $img = ImageManager::read($response);
            $destinationPath = storage_path('app/public/images/'.$request->image);
            $img->save($destinationPath,quality: 80, progressive: true);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $exception){
            return response()->json([
                'success' => false,
            ]);
        }


//        return $img->getClientOriginalExtension();
    }
}

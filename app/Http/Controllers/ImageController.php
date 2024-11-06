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

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $name = $file->getClientOriginalName();
            $imageFile = ImageManager::read($file->getRealPath());
            $destinationPath = storage_path('app/public/images/'.$name);

            $imageFile->save($destinationPath,quality: 80, progressive: true);

        }

    }
}

<?php

namespace App\Services;
use Intervention\Image\Laravel\Facades\Image as ImageManager;
use App\Models\Image;
class ImageService {

    public function uploadFromUrl($url, $name) {

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );

        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));

        $img = ImageManager::read($response);

        $destinationPath = storage_path('app/public/images/'.$name);
        $img->save($destinationPath,quality: 80, progressive: true);

        $image = Image::where('name', $name)->first();
        if (!$image){
            $image = Image::create([
                'name' => $name,
                'path' => 'storage/images/',
                'width' => $img->width(),
                'height' => $img->height(),
            ]);
        }
        return $image;

    }
}

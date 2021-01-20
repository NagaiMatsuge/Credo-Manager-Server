<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


trait UploadTrait
{
    //* Uploading files to database
    public function uploadFile($image_64, string $uploadPath): string
    {
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = $uploadPath . '/' . Str::random(15) . '.' . $extension;
        Storage::disk('public')->put($imageName, base64_decode($image));
        return "uploads/$imageName";
    }
}

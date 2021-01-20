<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


trait UploadTrait
{
    //* Uploading files to database
    // public function uploadFile($image_64, string $uploadPath): string
    // {
    //     $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
    //     $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
    //     $image = str_replace($replace, '', $image_64);
    //     $image = str_replace(' ', '+', $image);
    //     $imageName = $uploadPath . '/' . Str::random(15) . '.' . $extension;
    //     Storage::disk('public')->put($imageName, base64_decode($image));
    //     return "uploads/$imageName";
    // }

    public function uploadFile($image_64, string $uploadPath): string
    {
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = $uploadPath . '/' . Str::random(15) . '.' . $extension;
        $image_content = base64_decode($image);
        $cropped_image = $this->resizeImage($image_content, 100, 100, true);
        Storage::disk('public')->put($imageName, $cropped_image);
        return "uploads/$imageName";
    }

    public function resizeImage($file, $w, $h, $crop = FALSE)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
}

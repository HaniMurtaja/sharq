<?php

namespace App\Traits;

use App\Services\UploadService\Bunny\BunnyCdn;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

trait FileHandler
{
    public function upload($file, $path = '')
    {
        File::ensureDirectoryExists(storage_path('app/public/' . $path));
        $hash_name = rand(1000000, 9999999) . time() . $file->hashName();
        $original_path = $path . '/' . $hash_name;
        $path = 'storage/' . $path;
        $img = Image::make($file);
        $path = $path . '/' . $hash_name;
        //        $img->widen(300);
        $img->save(storage_path('app/public/' . $original_path));
        return $path;
    }

    public function uploadIntoSameServer($file, $path = '')
    {
        File::ensureDirectoryExists(storage_path('app/public/' . $path));
        $hash_name = rand(1000000, 9999999) . time() . $file->hashName();
        $original_path = $path . '/' . $hash_name;
        $path = 'storage/' . $path;
        $img = Image::make($file);
        $path = $path . '/' . $hash_name;
        //        $img->widen(300);
        $img->save(storage_path('app/public/' . $original_path));
        return $path;
    }

    public function download_file($path = '', $title = '')
    {
        $arr = explode('.', $path);
        $mimetype = $arr[count($arr) - 1];
        return response()->download($path, $title . '.' . $mimetype);
    }


    public function delete_file($path = '')
    {
        if (!is_null($path))
            @File::delete($path);
    }

    public function delete_dir($path = '')
    {
        @File::deleteDirectory($path);
    }

    public function loadArrayFromFile($path)
    {
        return File::getRequire($path);
    }

    public function CopyFileContent($src, $target)
    {
        if ($this->FileExists($src))
            File::copy($src, $target);
    }

    public function PutFileContent($path, $content)
    {
        File::put($path, $content);
    }

    public function GetFileContent($path): string
    {
        return File::get($path);
    }

    public function FileExists($path): bool
    {
        return File::exists($path);
    }

    public function upload_files($file, $path = '')
    {

        File::ensureDirectoryExists(storage_path('app/public/' . $path));
        $hash_name = rand(1000000, 9999999) . time() . $file->getClientOriginalName();
        // $img->widen(300);
        $file->move(storage_path('app/public/' . $path), $hash_name);
        $path = 'storage/' . $path . "/" . $hash_name;
        $new_url = $path;
        $client = new \Bunny\Storage\Client(
            env('BunnyapiAccessKey'),
            env('BunnyStorageZones'),
            \Bunny\Storage\Region::FALKENSTEIN
        );
        $client->upload(public_path($path), $new_url);
        $this->delete_file(public_path($path));
        return $new_url;
    }


    public function upload_excel_file($file, $path = '')
    {

        File::ensureDirectoryExists(storage_path('app/public/' . $path));


        if ($file instanceof \Illuminate\Http\UploadedFile) {

            $originalName = $file->getClientOriginalName();
        } else {

            $originalName = basename($file->getRealPath());
        }


        $hash_name = rand(1000000, 9999999) . time() . $originalName;
        $original_path = $path . '/' . $hash_name;


        $file->move(storage_path('app/public/' . $path), $hash_name);


        $file_path = public_path('storage/' . $path . "/" . $hash_name);


        $client = new \Bunny\Storage\Client(
            env('BunnyapiAccessKey'),
            env('BunnyStorageZones'),
            \Bunny\Storage\Region::FALKENSTEIN
        );

        try {

            $client->upload($file_path, $original_path);


            $this->delete_file($file_path);


            $cdnUrl = 'https://' . env('BunnyStorageZones') . '.b-cdn.net/' . $original_path;

            return $cdnUrl;
        } catch (\Exception $e) {

            return response()->json(['error' => 'Error uploading to BunnyCDN: ' . $e->getMessage()], 500);
        }
    }





    public function TempVideo($file)
    {
        //        File::ensureDirectoryExists(public_path('TempVideo'));
        $hash_name = rand(1000000, 9999999) . time() . $file->getClientOriginalName();
        $file->move(storage_path('TempVideo'), $hash_name);

        return public_path('TempVideo') . "/" . $hash_name;
    }

    public function DeleteTempVideo($file)
    {
        File::delete($file);
    }

    public function upload_files_with_type($file, $path = ''): array
    {
        File::ensureDirectoryExists(storage_path('app/public/' . $path));
        $hash_name = rand(1000000, 9999999) . time() . $file->getClientOriginalName();
        // $img->widen(300);
        $file->move(storage_path('app/public/' . $path), $hash_name);
        $path = 'storage/' . $path . "/" . $hash_name;;
        $mimeType = File::mimeType(public_path($path));

        return [
            'path' => $path,
            'type' => $mimeType

        ];
    }
    public function BunnyStorageUpload($path)
    {

        $client = new \Bunny\Storage\Client(
            "425eae66-9cbb-4920-b48209147514-01b6-47b4",
            "alshrouqdelivery",
            \Bunny\Storage\Region::FALKENSTEIN
        );

        $client->upload(public_path('storage/' . $path), $path);
        $this->delete_file(public_path('storage/' . $path));
        return $path;
    }
    public function deleteFromBunnyCDN($filePath)
    {
        $client = new \Bunny\Storage\Client(
            env('BunnyapiAccessKey'),
            env('BunnyStorageZones'),
            \Bunny\Storage\Region::FALKENSTEIN
        );

        try {
            $client->delete($filePath); // Deletes the file from BunnyCDN
        } catch (\Exception $e) {
            // Handle BunnyCDN deletion failure (optional)
        }
    }
}

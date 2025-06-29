<?php

    namespace App\Traits;
    use Illuminate\Support\Facades\File;
    trait Upload
    {
        function upload_image($image, $folder, $prev_image = null)
        {
            $filename = $prev_image;
            File::delete($filename);
            $photo = $image;
            $ext = $photo->getClientOriginalExtension();
            $fileStore = time() . '.' . $ext;
            $path=$photo->storeAs($folder, $fileStore, 'public');
            $image = $fileStore;
            return $image;
        }

        function delete_image($image)
        {
            File::delete( $image);
        }


    }

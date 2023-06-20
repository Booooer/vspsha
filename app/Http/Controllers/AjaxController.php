<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;


class AjaxController extends Controller
{
    public function checkFileSize($file){
        $size = (filesize($file) / 8) / 1024;
        if ($size >= 30000) {
            die('Вес файла превышает 30мб'." Вес файла - $size мб");
        }
        // filesize($file) >= 30000 ? die('Вес файла превышает 30мб') : true;
    }

    public function loadFile($value){
        $path = Storage::disk('local')->path($value);
        return response()->download($path, basename($path));
    }

    public function disableOrder(Request $request){
        $data = $request->all();
        $id = $data['id'];

        if (!$id) {
            return false;
        }

        $delete = Order::where('id', $id)->update(['isActive' => false]);

        if (!$delete) {
            return false;
        }

        return true;
    }

    public function findService(Request $request){
        $data = $request->all();
        $id = $data['id'];

        if (!$id) {
            return false;
        }

        return $service = Service::where('id', $id)->first();
    }

    public function updateService(Request $request){
        $json = $request->all();
        $service = Service::where('id',$json['service-id'])->first();

        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_name = str_replace(" ",'', $file_name);
            $url = Storage::disk('local')->putFileAs('public', $request->file('file'), $file_name);

            $service->update(['url_info' => $file_name]);
        }

        $update = $service->update([
            'title' => strip_tags($json['service-title']),
            'short_description' => strip_tags($json['service-description']),
        ]);

        if (!$update) {
            return false;
        }

        return json_encode('All ok');
    }

    public function getUrlImage(Request $request){
        $data = $request->all();
        $title = $data['title'];

        if (!$title) {
            return false;
        }

        $url = Service::where('title',$title)->first();

        return json_encode($url->url_info);
    }

    public function saveTemporaryImage(Request $request){
        $image = $request->file("file_$request->numberFile");

        if (!$image) {
            return false;
        }

        // $isMake = Storage::makeDirectory('public/crop');
        // $this->checkFileSize($image);

        $image_name = $image->getClientOriginalName();
        $image_name = str_replace(" ",'', $image_name);
        $isUpload = Storage::disk('local')->putFileAs('public', $image, $image_name);

        if ($isUpload) {
            return json_encode($image_name);
        }

        die('Ошибка в обработке файла');
    }

    public function cropImage(Request $request){
        $data = $request->all();

        $manager = new ImageManager(['driver' => 'imagick']);
        $image = $manager->make('storage/1625545538_11-kartinkin-com-p-ikonki-v-stile-anime-anime-krasivo-12.jpg')->crop(693,732,99,76);

        $image->save('storage/baz5.png');
    }

    public function totalSum(Request $request){
        $data = $request->all();

        $final_sum = 0;
        for ($i=0; $i < (count($data) / 2); $i++) {
            $info = DB::table('price_list')->where('size',$data["size_$i"])->first();

            if (!$info) {
                return false;
            }

            $final_sum += $data["count_$i"] * $info->price;
        }

        return $final_sum;
    }
}

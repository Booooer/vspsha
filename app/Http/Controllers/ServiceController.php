<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailOrder;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use ZipArchive;
use Illuminate\Filesystem\Filesystem as File;

class ServiceController extends Controller
{
    public function welcome(){
        $services = Service::where('isSupport',true)->get();

        return view('welcome',compact('services'));
    }

    public function search(Request $request){
        $json = $request->all();
        $data = Service::where('title','like','%'.$json['value'].'%')->get();

        if (!$data) {
            return json_encode('Нет совпадений');
        }
        return json_encode($data);
    }

    public function checkFileSize($file){
        $size = (filesize($file) / 8) / 1024;
        if ($size >= 30000) {
            die('Вес файла превышает 30мб'." Вес файла - $size мб");
        }
        // filesize($file) >= 30000 ? die('Вес файла превышает 30мб') : true;
    }

    protected function convertToZip($phone,$path){
        $path_to_folder = "storage/orders/$phone";

        $zip = new ZipArchive();
        $zip->open("storage/".$phone.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = scandir($path_to_folder);
        foreach($files as $file){
            if ($file == '.' || $file == '..' ){
                continue;
            }
            $f = $path_to_folder.DIRECTORY_SEPARATOR.$file;
            $zip->addFile($f);
        }
        $zip->close();
    }

    protected function cropFiles($request, $json, $uid, $folder, $counter){
        $index = 0;
        $request = $request;
        $json = $json;
        $uid = $uid;
        $folder = $folder;
        $counter = $counter;

        while($counter > 0) {
            $counter--;
            $index++;
            $file_name = ++$uid.'_'.$json["size_$counter"].'_'.$json["count_$counter"].'.jpg';
            $manager = new ImageManager(['driver' => 'imagick']);
            $image = $manager->make($request->file("file_$counter"))->crop($json["cropWidth".$counter],
                                                        $json["cropHeight".$counter],
                                                        $json["cropX".$counter],
                                                        $json["cropY".$counter]);

            $image->save("storage/orders/$folder/".$file_name);
            if ($index > 6) {
                $this->cropFiles($request, $json, $uid, $folder, $counter);
            }
        }
    }

    public function order(Request $request){
        $json = $request->all();

        if ($request->hasFile('file_0')){
            $phone = str_replace(" ", "", $json['phone']);
            $path = 'public/orders/'.$phone;
            $folder = $phone;
            $id = Order::latest()->first();
            $uid = isset($id->id) ? $id->id : 0;

            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            $counter = $request->filesCount;
            $this->cropFiles($request, $json, $uid, $folder, $counter);

            $this->convertToZip($phone,$path);

            $this->clearStorage($phone);

            Order::create([
                'title' => $json['order_name'],
                'file_url' => "public/$folder".'.zip',
                'tel' => $json['phone'],
                'file_size' => 'mixed',
                'file_name' => "$folder".'.zip',
                'total_sum' => $json['total_sum'],
                'quantity' => 0,
                'comment' => $json['order_description'],
            ]);
        }

        return true;
        // if (!$order) {
        //     return false;
        // }

        // $this->sendMail($order);
    }

    protected function clearStorage($phone){
        $clear = new File();

        $clear->deleteDirectory(public_path(('storage/crop')));
        $clear->deleteDirectory(public_path(("storage/orders/$phone")));
    }

    public function sendMail($order){
        Mail::to('tsurkan.maksim2016@gmail.com')
        ->send(new MailOrder($order));
    }

    public function checkOrders(){
        return Order::where('isActive',true)->orderBy('id','DESC')->get();
    }
}

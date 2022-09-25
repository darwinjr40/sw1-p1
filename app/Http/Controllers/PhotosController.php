<?php

namespace App\Http\Controllers;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotosController extends Controller
{
    public function showForm()
    {
        return view('form');
    }

    public function submitForm(Request $request){
        if ($request->hasFile('photo')) {  //existe un archivo con nombre <files>
            $imagen= [];
            // $data = array("evento_id" => $request['evento_id']);
            $files = $request->file('photo'); //retorna un object con los datos de los archivos
            foreach ($files as $file) {
                return "entro";
            }
                $data['pathPrivate'] = Storage::disk('s3')->put(12, $files, 'public');
                $data['path'] = Storage::disk('s3')->url($data['pathPrivate']);
                $imagen[] = $data;
            // $request['datos'] = $imagen;
            return "nise";
        }
    }
    // public function submitForm(Request $request)
    // {
    //     // return env('AWS_DEFAULT_REGION');
    //     $client = new RekognitionClient([
    //         // 'region'    => env('AWS_DEFAULT_REGION'),
    //         'region'    => config('services.ses.region'),
    //         'version'   => 'latest'
    //     ]);

    //     $image = fopen($request->file('photo')->getPathName(), 'r');
    //     $bytes = fread($image, $request->file('photo')->getSize());
    //     // return $image . '  : '. $bytes; 
    //     // dd($request);
    //     if ($request->input('type') === 'nudity') {
    //         $results = $client->detectModerationLabels([
    //             'Image' => ['Bytes' => $bytes],
    //             // 'MinConfidence' => intval($request->input('confidence'))
    //             'MinConfidence' => 50
    //         ]);
    //         // ])['ModerationLabels'];
    //         return $results;
    //         $results = $results->get("ModerationLabels");
    //         if (array_search('Explicit Nudity', array_column($results, 'Name'))) {
    //             $message = 'This photo may contain nudity';
    //         } else {
    //             $message = 'This photo does not contain nudity';
    //         }
    //     } else { //text
    //         $results = $client->detectText([
    //             'Image' => ['Bytes' => $bytes],
    //             'MinConfidence' => intval($request->input('confidence'))
    //         ])['TextDetections'];
    //         $string = '';
    //         foreach ($results as $item) {
    //             if ($item['Type'] === 'WORD') {
    //                 $string .= $item['DetectedText'] . ' ';
    //             }
    //         }
    //         $message = empty($string) ? "This photo does not have any words" :  'This photo says ' . $string;
    //     }
    //     request()->session()->flash('success', $message);
    //     return view('form', ['results' => $results]);
    // }
}

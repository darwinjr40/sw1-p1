<?php

namespace App\Http\Controllers;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotosController extends Controller
{
    public function showForm()
    {
        return env('AWS_DEFAULT_REGION');
        return view('form');
    }


    public function submitForm(Request $request)
    {
        if ($request->hasFile('file')) {
            $client = new RekognitionClient([
                'region'    => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
            ]);

            $image = fopen($request->file('file')->getPathname(), 'r');
            $bytes = fread($image, $request->file('file')->getSize());
            $results = $client->detectModerationLabels([
                'Image' => [
                    'Bytes' => $bytes,
                ],
                'MinConfidence' => 50,
            ]);

            return $results;
            $resultLabels = $results->get('ModerationLabels');
        }
    }

    public function subirFile(Request $request)
    {
        // return "nise";

        if ($request->hasFile('file')) {
            $client = new RekognitionClient([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
            ]);

            $image = fopen($request->file('file')->getPathname(), 'r');
            $bytes = fread($image, $request->file('file')->getSize());

            $results = $client->detectModerationLabels([
                'Image' => [
                    'Bytes' => $bytes,
                ],
                'MinConfidence' => 50,
            ]);

            $resultLabels = $results->get('ModerationLabels');
            // return $resultLabels;


            return response()->json(['message' => 'no se detectaron etiquetas']);
        } else {
            return response()->json(['message' => 'Error al subir el aaarchivo']);
        }
    }
    // public function submitForm(Request $request)
    // {
    //     // return config('services.ses.region');
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

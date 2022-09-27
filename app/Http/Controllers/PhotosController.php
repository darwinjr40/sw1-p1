<?php

namespace App\Http\Controllers;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotosController extends Controller
{
  public function showForm()
  {
    // return env('AWS_DEFAULT_REGION');
    return view('form');
  }

  public function subirFile(Request $request)
  {
    if ($request->hasFile('files')) {
      $client = new RekognitionClient([
        'region'    => env('AWS_DEFAULT_REGION'),
        'version'   => 'latest'
      ]);
      $bytes= [];
      $files = $request->file('files'); //retorna un object con los datos de los archivos
      foreach ($files as $f) {
        $i = fopen($f->getPathName(), 'r');
        $b = fread($i, $f->getSize());
        $bytes[] = $b;
      }
      $results = $client->compareFaces([
        'SimilarityThreshold' => 0,
        'SourceImage' => [
          'Bytes' => $bytes[0]
        ],
        'TargetImage' => [
          'Bytes' => $bytes[1]
        ],
      ]);
      $results = $results->get('FaceMatches');
      return $results;
      return response()->json(['message' => 'todo nise']);
      $string = '';
      // return $results;
      foreach ($results as $item) {
        if ($item['Type'] === 'WORD') {
          $string .= $item['DetectedText'] . ' ';
        }
      }
      $message = empty($string) ? "This photo does not have any words" :  'This photo says ' . $string;

      return response()->json(['message' => 'no se detectaron etiquetas']);
    } else {
      return response()->json(['message' => 'Error al subir el aaarchivo']);
    }
  }

  public function submitForm(Request $request)
  {
    $client = new RekognitionClient([
      'region'    => env('AWS_DEFAULT_REGION'),
      // 'region'    => config('services.ses.region'),
      'version'   => 'latest'
    ]);
    $image = fopen($request->file('file')->getPathName(), 'r');
    $bytes = fread($image, $request->file('file')->getSize());
    if ($request->input('type') === 'nudity') {
      $results = $client->detectModerationLabels([
        'Image' => ['Bytes' => $bytes],
        // 'MinConfidence' => intval($request->input('confidence'))
        'MinConfidence' => 50
      ]);
      $results = $results->get("ModerationLabels");
      if (array_search('Explicit Nudity', array_column($results, 'Name'))) {
        $message = 'This photo may contain nudity';
      } else {
        $message = 'This photo does not contain nudity';
      }
    } else { //text
      $results = $client->detectText([
        'Image' => ['Bytes' => $bytes],
        'MinConfidence' => intval($request->input('confidence'))
      ])['TextDetections'];

<<<<<<< HEAD
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
=======
      $string = '';
      // return $results;
      foreach ($results as $item) {
        if ($item['Type'] === 'WORD') {
          $string .= $item['DetectedText'] . ' ';
>>>>>>> 5d9228588a99248aa918b8610bb8c005cafe6169
        }
      }
      $message = empty($string) ? "This photo does not have any words" :  'This photo says ' . $string;
    }
    request()->session()->flash('success', $message);
    return view('form', ['results' => $results]);
  }
}

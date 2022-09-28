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
      $bytes = [];
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

      $string = '';
      // return $results;
      foreach ($results as $item) {
        if ($item['Type'] === 'WORD') {
          $string .= $item['DetectedText'] . ' ';
        }
      }
      $message = empty($string) ? "This photo does not have any words" :  'This photo says ' . $string;
    }
    request()->session()->flash('success', $message);
    return view('form', ['results' => $results]);
  }

  public  function subirImagen(Request $request)
  {
    if ($request->hasFile('files')) {  //existe un archivo con nombre <files>
      $imagen = [];
      // $data = array("evento_id" => $request['evento_id']);
      $file = $request->file('files'); //retorna un object con los datos de los archivos
        $data['pathPrivate'] = Storage::disk('s3')->put(12, $file, 'public');
        $data['path'] = Storage::disk('s3')->url($data['pathPrivate']);
        return response()->json([
          'pathPrivate' => $data['pathPrivate'],
          'path' => $data['path']
      ]);
    }

    return response()->json(['message' => 'no se detectaron etiquetas']);
    
    // if (isset($response['errors'])) {
    //   return back()->withErrors($response->json()['errors']);
    // } else {
    //   return back()->with('success', $response->json()['message']);
    // }
  }
}

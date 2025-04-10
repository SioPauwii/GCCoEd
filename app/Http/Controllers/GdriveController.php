<?php

namespace App\Http\Controllers;

use App\Models\Files;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\learner;
use App\Models\mentor;


class GdriveController extends Controller
{
    public function token()
    {
        $client_id = \Config('services.google.client_id');
        $client_secret = \Config('services.google.client_secret');
        $refresh_token = \Config('services.google.refresh_token');

        $response = Http::post('https://oauth2.googleapis.com/token', [

            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',

        ]);
        //dd($response);
        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        return $accessToken;
    }

    public function create()
    {
        $files = Files::all();

        return view('create', compact('files'));
    }

    public function store(Request $request)
    {
        $folder_id = '1gVJfdCriVQ0PILgqnE89IfnKOGf7NjTe';

        $request->validate([
            'file' => 'file|required|mimes:jpg,jpeg,png,doc,docx,pdf|max:25600',
            'file_name' => 'required',
        ]);

        $accessToken = $this->token();
        $name = $request->file->getClientOriginalName();

        $path = $request->file->getRealPath();

        $metadata = [
            'name'=>$name,
            'parents'=>[$folder_id]
        ];

        $response=Http::withToken($accessToken)
        ->attach('metadata',json_encode($metadata),'metadata.json')
        ->attach('data',file_get_contents($path),$name)
        ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($response->successful()) {
            $file_id = json_decode($response->body())->id;

            $uploadedfile = new Files;
            $uploadedfile->user_id = Auth::user()->id;
            $uploadedfile->file_name = $name;
            $uploadedfile->name = $name;
            $uploadedfile->fileid = $file_id;
            $uploadedfile->save();

            return response('File Uploaded to Google Drive');
        }

        return response('Failed to Upload to Google Drive');
    }

    public function show(Request $request, $userId)
    {
        // Retrieve files owned by the selected user
        $files = Files::where('user_id', $userId)->get();

        // Check if the user has any files
        if ($files->isEmpty()) {
            return response('No files found for the selected user.', 404);
        }

        // If a specific file ID is provided for download
        if ($request->has('file_id')) {
            $fileId = $request->input('file_id');
            $file = Files::where('user_id', $userId)->where('id', $fileId)->first();

            // Check if the file exists and belongs to the user
            if (!$file) {
                return response('File not found or does not belong to the selected user.', 404);
            }

            // Get the file extension
            $ext = pathinfo($file->name, PATHINFO_EXTENSION);

            // Get the access token
            $accessToken = $this->token();

            // Make a GET request to Google Drive API to retrieve the file
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://www.googleapis.com/drive/v3/files/{$file->fileid}?alt=media");

            // Check if the retrieval was successful
            if ($response->successful()) {
                $filePath = '/downloads/' . $file->file_name . '.' . $ext;

                // Save the file to local storage
                Storage::put($filePath, $response->body());

                // Return the file as a download
                return Storage::download($filePath);
            }

            return response('Failed to retrieve the file from Google Drive.', 500);
        }

        // Return the list of files as a response
        return response()->json([
            'message' => 'Files retrieved successfully.',
            'files' => $files,
        ], 200);
    }

    public function delete($id)
    {
        // Retrieve the file record from the database using the provided ID
        $file = Files::find($id);

        // Check if the file exists in the database
        if (!$file) {
            return response('File not found in the database.', 404);
        }

        // Get the access token
        $accessToken = $this->token();

        // Make a DELETE request to Google Drive API to delete the file
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->delete("https://www.googleapis.com/drive/v3/files/{$file->fileid}");

        // Check if the deletion was successful
        if ($response->successful()) {
            // Delete the file record from the database
            $file->delete();

            return response('File deleted successfully from Google Drive and database.', 200);
        }

        // Log the error for debugging
        Log::error('Failed to delete file from Google Drive', [
            'file_id' => $file->fileid,
            'response' => $response->body(),
        ]);

        return response('Failed to delete the file from Google Drive.', $response->status());
    }

    public function imageUp(Request $request) {
        $folder_id = '16uhYG_VG_QaIp2pDz7YAqmpzCYnnQKYC';

        $request->validate([
            'image' => 'file|required|mimes:jpg,jpeg,png|max:2048',
        ]);

        $accessToken = $this->token();
        $name = $request->image->getClientOriginalName();
        $path = $request->image->getRealPath();

        $metadata = [
            'name'=>$name,
            'parents'=>[$folder_id]
        ];

        $response = Http::withToken($accessToken)
        ->attach('metadata',json_encode($metadata),'metadata.json')
        ->attach('data',file_get_contents($path),$name)
        ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($response->successful()) {
            $file_id = json_decode($response->body())->id;

            // // Store the file ID in the respective table based on the role
            // if ($role === 'mentor') {
            //     $mentor = mentor::where('ment_inf_id', Auth::id())->first();
            //     if ($mentor) {
            //         $mentor->image = $file_id;
            //         $mentor->save();
            //     }
            // } elseif ($role === 'learner') {
            //     $learner = learner::where('learn_inf_id', Auth::id())->first();
            //     if ($learner) {
            //         $learner->image = $file_id;
            //         $learner->save();
            //     }
            // }

            return $file_id;
        }
    }

    public function storeCreds(Request $request) {
        $parent_id = '1TnFc_7Xo4f09eXNjpKgWbqGwNmSJuM_R';

        $request->validate([
            'credentials' => 'required|array|min:1',
            'credentials.*' => 'file|mimes:jpg,jpeg,png,docx,pdf|max:25600',
        ]);

        $accessToken = $this->token();
        $file_ids = [];
        foreach ($request->credentials as $credential) {
            $name = $credential->getClientOriginalName();
            $path = $credential->getRealPath();

            $metadata = [
                'name'=>$name,
                'parents'=>[$parent_id]
            ];

            $response = Http::withToken($accessToken)
            ->attach('metadata',json_encode($metadata),'metadata.json')
            ->attach('data',file_get_contents($path),$name)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

            if ($response->successful()) {
                $file_id = json_decode($response->body())->id;
                $file_ids[] = $file_id;
            }
        }

        return $file_ids;
    }
}
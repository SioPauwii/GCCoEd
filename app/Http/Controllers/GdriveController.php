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
use App\Models\Schedule;


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
            // 'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,doc,docx,pdf|max:25600',
        ]);

        $uploadedFiles = $request->file('files');

        $accessToken = $this->token();
        $file_ids = [];

        if(is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $name = $file->getClientOriginalName();
                $path = $file->getRealPath();
                $sizeInKb = round($file->getSize() / 1024, 2); // Size in KB

                $metadata = [
                    'name' => $name,
                    'parents' => [$folder_id]
                ];

                $response = Http::withToken($accessToken)
                    ->attach('metadata', json_encode($metadata), 'metadata.json')
                    ->attach('data', file_get_contents($path), $name)
                    ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

                if ($response->successful()) {
                    $file_id = json_decode($response->body())->id;

                    // Get the MIME type of the file
                    $mimeType = $file->getMimeType();

                    // Map MIME types to simpler file types
                    $fileTypeMap = [
                        'application/msword' => 'Word',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
                        'application/pdf' => 'PDF',
                        'image/jpeg' => 'JPG',
                        'image/png' => 'PNG',
                    ];

                    // Default to 'Unknown' if MIME type is not in the map
                    $fileType = $fileTypeMap[$mimeType] ?? 'Unknown';

                    // Store the file details in the database
                    $uploadedfile = new Files;
                    $uploadedfile->owner_id = Auth::user()->id;
                    $uploadedfile->file_name = $name;
                    $uploadedfile->File_type = $fileType; // Store the simplified file type
                    $uploadedfile->fileid = $file_id;
                    $uploadedfile->file_size = $sizeInKb;
                    $uploadedfile->save();

                    $file_ids[] = $file_id;
                }
            }
        }

        return response()->json([
            'message' => 'Files uploaded successfully to Google Drive.',
            'file_ids' => $file_ids,
        ]);
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

    public function getMentorFiles()
    {
        $user = Auth::user();

        // Retrieve files owned by the logged-in mentor
        $files = Files::where('owner_id', $user->id)->get();

        // Check if the mentor has any files
        if ($files->isEmpty()) {
            return response()->json(['message' => 'No files found for the logged-in mentor'], 404);
        }

        // Return the list of files as a response
        return response()->json([
            'message' => 'Files retrieved successfully.',
            'files' => $files,
        ], 200);
    }

    public function getMentorsFiles()
    {
        $user = Auth::user();

        // Get the learner's created schedules and their unique participant IDs
        $participantIds = Schedule::where('creator_id', $user->id)
            ->distinct()
            ->pluck('participant_id')
            ->toArray();

        // Get mentors' user IDs from participant IDs
        $mentorUserIds = Mentor::whereIn('mentor_no', $participantIds)
            ->pluck('ment_inf_id')
            ->toArray();

        // Get files owned by these mentors with only specified fields
        $files = Files::whereIn('owner_id', $mentorUserIds)
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'file_id' => $file->fileid,
                    'file_name' => $file->file_name,
                    "owner_id" => $file->owner_id,
                ];
            });

        if ($files->isEmpty()) {
            return response()->json([
                'message' => 'No files found from scheduled mentors',
                'files' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Files retrieved successfully',
            'files' => $files
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

    public function retMentPfp($userID) {
        $user = Mentor::where('ment_inf_id', $userID)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $file_id = $user->image;
    
        if (!$file_id) {
            return response()->json(['message' => 'Image not found for the user'], 404);
        }
    
        $accessToken = $this->token();
    
        // Get file metadata from Google Drive
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://www.googleapis.com/drive/v3/files/{$file_id}?fields=webContentLink,webViewLink");
    
        if ($response->successful()) {
            $data = json_decode($response->body(), true);
            return response()->json([
                'webContentLink' => $data['webContentLink'], // Direct download link
                'webViewLink' => $data['webViewLink'],       // Viewable link
            ]);
        }
    
        return response()->json(['message' => 'Failed to retrieve image URL'], 500);
    }

    public function streamImg($file_id) {
        $accessToken = $this->token();
        
        $response = Http::withToken($accessToken)
        ->get("https://www.googleapis.com/drive/v3/files/{$file_id}",[
            'alt' => 'media'
        ]);

        return response($response->body(), 200, [ 
            'Content-Type' => $response->header('Content-Type'),
        ]);
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

    public function previewFile($file_id)
    {
        $accessToken = $this->token();

        $file = Files::where('id', $file_id)->first();

        $id = $file->fileid;
        // Get file metadata from Google Drive
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://www.googleapis.com/drive/v3/files/{$id}?fields=webViewLink");

        if ($response->successful()) {
            $data = json_decode($response->body(), true);

            // Return the webViewLink for previewing the file
            return response()->json([
                'message' => 'File preview link retrieved successfully.',
                'webViewLink' => $data['webViewLink'], // Viewable link
            ]);
        }

        return response()->json([
            'message' => 'Failed to retrieve file preview link.',
            'status' => $response->status(),
            'error' => json_decode($response->body(), true), // Include the error details from the API
        ], $response->status());
    }

    public function downloadFile($file_id)
    {
        $accessToken = $this->token();

        // Retrieve the file record from the database
        $file = Files::where('id', $file_id)->first();

        if (!$file) {
            return response()->json(['message' => 'File not found in the database.'], 404);
        }

        $googleFileId = $file->fileid;

        // Make a request to Google Drive API to download the file
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://www.googleapis.com/drive/v3/files/{$googleFileId}?alt=media");

        if ($response->successful()) {
            // Stream the file directly to the user's browser
            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type'),
                'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"',
            ]);
        }

        // Log the error for debugging
        Log::error('Failed to download file', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        // Return a detailed error response
        return response()->json([
            'message' => 'Failed to download file.',
            'status' => $response->status(),
            'error' => json_decode($response->body(), true), // Include the error details from the API
        ], $response->status());
    }

    public function getMentorCreds($mentId)
    {
        try {
            // Get authenticated user's mentor record
            $mentor = Mentor::where('ment_inf_id', $mentId)->first();

            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mentor record not found'
                ], 404);
            }

            // Get credentials array from the mentor record
            $credentialIds = $mentor->credentials;

            if (empty($credentialIds)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No credentials found',
                    'credentials' => []
                ], 200);
            }

            $accessToken = $this->token();
            $credentials = [];

            // Fetch each credential file's details from Google Drive
            foreach ($credentialIds as $fileId) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get("https://www.googleapis.com/drive/v3/files/{$fileId}?fields=id,name,webViewLink,webContentLink");

                if ($response->successful()) {
                    $fileData = json_decode($response->body(), true);
                    $credentials[] = [
                        'id' => $fileData['id'],
                        'name' => $fileData['name'],
                        'previewLink' => $fileData['webViewLink'],
                        'downloadLink' => $fileData['webContentLink']
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Credentials retrieved successfully',
                'credentials' => $credentials
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve credentials',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
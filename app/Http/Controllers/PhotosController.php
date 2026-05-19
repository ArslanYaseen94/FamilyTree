<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PhotosController extends Controller
{
    public function delete(Request $request)
    {
        $photoUrl = $request->input('photo');
        $userId = Auth::id();
        $userFolder = 'uploads/' . $userId;

        // Parse path from the full URL
        $parsedUrl = parse_url($photoUrl, PHP_URL_PATH); // e.g., /uploads/1/media123.jpg
        $relativePath = ltrim($parsedUrl, '/'); // remove leading slash
        $fullPath = public_path($relativePath); // e.g., /var/www/html/public/uploads/1/media123.jpg

        // Validate the file is inside the user's folder
        if (str_starts_with($fullPath, public_path($userFolder)) && File::exists($fullPath)) {

            // Determine the type based on extension
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            $videoExtensions = ['mp4', 'mov', 'webm', 'ogg'];

            $type = 'unknown';
            if (in_array($extension, $imageExtensions)) {
                $type = 'image';
            } elseif (in_array($extension, $videoExtensions)) {
                $type = 'video';
            }

            File::delete($fullPath);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully.',
                'type' => $type,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found or not allowed.',
        ]);
    }
    public function index()
    {
        $userId = Auth::id();
        $folderPath = public_path('uploads/' . $userId);

        $photos = [];

        if (File::exists($folderPath)) {
            $files = File::files($folderPath);
            foreach ($files as $file) {
                $photos[] = asset('uploads/' . $userId . '/' . $file->getFilename());
            }
        }

        return view('user-view.photos.index', compact('photos'));
    }
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'media' => 'required|array',
            'media.*' => 'required|file|mimes:jpeg,jpg,png,gif,bmp,webp,svg|max:5120',
        ], [
            'media.*.mimes' => __('messages.Only image files are allowed (jpeg, jpg, png, gif, bmp, webp, svg).'),
            'media.*.max' => __('messages.Each image must be less than 5MB.'),
        ]);

        $userId = Auth::id();
        $folderPath = public_path('uploads/' . $userId);

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $existingFiles = File::files($folderPath);
        $existingCount = count($existingFiles);

        $uploads = $request->file('media');
        $uploadCount = count($uploads);

        if (($existingCount + $uploadCount) > 300) {
            return redirect()->back()
                ->with(__('messages.error'), __('messages.Upload limit exceeded. You can only have up to 300 photos.'));
        }

        foreach ($uploads as $file) {
            $mime = $file->getMimeType();
            if (!str_starts_with($mime, 'image/')) {
                continue;
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = 'media_' . time() . '_' . uniqid() . '.' . $extension;
            $file->move($folderPath, $fileName);
        }

        return redirect()->back()
            ->with(__('messages.success'), __('messages.Photos uploaded successfully!'));
    }
}

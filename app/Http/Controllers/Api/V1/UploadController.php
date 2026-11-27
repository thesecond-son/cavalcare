<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * POST /uploads (both apps): multipart "file", JPEG/PNG/WebP image or PDF,
 * max 15 MB. Returns upload_id to use in the next call.
 *
 * Photos are re-encoded, which drops EXIF data (including GPS location)
 * before anything is stored (spec 13.1).
 */
class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:15360', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf'],
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $dir  = 'uploads/' . now()->format('Y/m');
        $name = Str::uuid()->toString();

        if ($mime === 'application/pdf') {
            $path = $file->storeAs($dir, $name . '.pdf', 'local');
        } else {
            if (! function_exists('imagecreatefromjpeg')) {
                report(new \RuntimeException('GD extension missing: image uploads cannot be cleaned.'));
                return \App\Support\ApiError::make('upload_unavailable', 'Photo uploads aren\'t available right now. Please upload a PDF instead.', 503);
            }
            $path = $dir . '/' . $name . '.jpg';
            Storage::disk('local')->put($path, $this->stripMetadata($file->getRealPath(), $mime));
            $mime = 'image/jpeg';
        }

        $upload = Upload::create([
            'user_id'       => $request->user()->id,
            'disk'          => 'local',
            'path'          => $path,
            'original_name' => Str::limit($file->getClientOriginalName(), 200, ''),
            'mime'          => $mime,
            'size_bytes'    => Storage::disk('local')->size($path),
        ]);

        return response()->json(['data' => [
            'upload_id'  => $upload->id,
            'mime'       => $upload->mime,
            'size_bytes' => $upload->size_bytes,
        ]], 201);
    }

    /** Re-encodes an image as JPEG (quality 88), dropping all metadata. */
    private function stripMetadata(string $path, string $mime): string
    {
        $image = match ($mime) {
            'image/png'  => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default      => imagecreatefromjpeg($path),
        };
        abort_unless($image, 422, 'That image could not be read.');

        // Flatten transparency onto white so PNG screenshots stay readable.
        $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        ob_start();
        imagejpeg($canvas, null, 88);
        $bytes = ob_get_clean();
        imagedestroy($image);
        imagedestroy($canvas);

        return $bytes;
    }
}

<?php

// app/Listeners/SendEmbedToFlask.php
namespace App\Listeners;

use App\Events\StudentPhotoUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendEmbedToFlask implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StudentPhotoUploaded $event)
    {
        // Ambil konten file
        $path = $event->photo->store('temp-photos');
        $full = storage_path('app/' . $path);

        // Kirim ke Flask /embed
        $response = Http::attach(
            'file',
            file_get_contents($full),
            basename($full)
        )->post(config('services.flask.url') . '/embed', [
            'nisn' => $event->nisn
        ]);

        // Bersihkan temp
        Storage::delete($path);

        if (! $response->ok()) {
            Log::error("Flask embed failed for {$event->nisn}", ['resp' => $response->body()]);
        }
    }
}

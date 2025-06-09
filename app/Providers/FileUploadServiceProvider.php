<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('fileUpload', function ($app) {
            return new class {
                public function uploadImage($file, $path = 'uploads'): string
                {
                    if (!$file) {
                        return '';
                    }

                    $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs($path, $filename, 'public');

                    return $filePath;
                }

                public function deleteFile($path): bool
                {
                    if (!$path) {
                        return false;
                    }

                    return Storage::disk('public')->delete($path);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 
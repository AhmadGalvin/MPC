<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Str;

abstract class UploadFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(
            $this->getBaseRules(),
            $this->getFileValidationRules()
        );
    }

    /**
     * Get base validation rules
     */
    protected function getBaseRules(): array
    {
        return [];
    }

    /**
     * Get file validation rules
     */
    protected function getFileValidationRules(): array
    {
        return [
            'file' => [
                'required',
                File::types($this->getAllowedMimeTypes())
                    ->max($this->getMaxFileSize())
                    ->dimensions(
                        minWidth: $this->getMinWidth(),
                        minHeight: $this->getMinHeight(),
                        maxWidth: $this->getMaxWidth(),
                        maxHeight: $this->getMaxHeight()
                    )
            ]
        ];
    }

    /**
     * Get allowed mime types
     */
    protected function getAllowedMimeTypes(): array
    {
        return ['jpg', 'jpeg', 'png'];
    }

    /**
     * Get max file size in kilobytes
     */
    protected function getMaxFileSize(): int
    {
        return 2048; // 2MB
    }

    /**
     * Get minimum width for image
     */
    protected function getMinWidth(): int
    {
        return 100;
    }

    /**
     * Get minimum height for image
     */
    protected function getMinHeight(): int
    {
        return 100;
    }

    /**
     * Get maximum width for image
     */
    protected function getMaxWidth(): int
    {
        return 2000;
    }

    /**
     * Get maximum height for image
     */
    protected function getMaxHeight(): int
    {
        return 2000;
    }

    /**
     * Store uploaded file
     */
    protected function storeFile(string $path, string $disk = 'public'): string
    {
        $file = $this->file('file');
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        return $file->storeAs($path, $filename, $disk);
    }
} 
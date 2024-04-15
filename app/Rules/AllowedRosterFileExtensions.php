<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\PotentiallyTranslatedString;

class AllowedRosterFileExtensions implements ValidationRule
{
    private const ALLOWED_ROSTER_EXTENSIONS = [
        "pdf",
        "txt",
        "xls",
        "xlsx",
        "html",
        "webcal"
    ];

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail($this->message($attribute));
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        if (!in_array($extension, self::ALLOWED_ROSTER_EXTENSIONS)) {
            $fail($this->message($attribute));
        }
    }

    private function message(string $attribute = ""): string
    {
        $extensions = implode(", ", self::ALLOWED_ROSTER_EXTENSIONS);

        return $attribute ?
            sprintf(
                "The %s field must be a file with any of the extensions: %s.",
                $attribute,
                $extensions
            ) :
            sprintf(
                "Only files with any of the extensions %s are allowed.",
                $extensions
            )
        ;
    }
}

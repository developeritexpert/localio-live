<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'subject',
        'body',
        'variables',
        'is_active',
        'status',
    ];

    protected $appends = [
        'status',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['is_active'] = ($value === 'active' || $value === true || $value === 1 || $value === '1');
    }

    public function translations()
    {
        return $this->hasMany(MailTemplateTranslation::class);
    }

    public function getTranslation($langId)
    {
        if ($langId == 1) {
            return [
                'subject' => $this->subject,
                'body' => $this->body
            ];
        }

        $translation = $this->translations()->where('lang_id', $langId)->first();

        if ($translation) {
            return [
                'subject' => $translation->subject,
                'body' => $translation->body
            ];
        }

        return [
            'subject' => $this->subject,
            'body' => $this->body
        ];
    }

    public function updateTranslation($langId, $subject, $body)
    {
        if ($langId == 1) {
            $this->update([
                'subject' => $subject,
                'body' => $body
            ]);
            return $this;
        }

        return $this->translations()->updateOrCreate(
            ['lang_id' => $langId],
            [
                'subject' => $subject,
                'body' => $body
            ]
        );
    }

    public static function getByKey($key, $langId = 1)
    {
        $template = self::where('key', $key)->where('is_active', true)->first();

        if (!$template) {
            return null;
        }

        $translation = $template->getTranslation($langId);

        return [
            'id' => $template->id,
            'key' => $template->key,
            'subject' => $translation['subject'],
            'body' => $translation['body'],
            'variables' => $template->variables,
            'is_active' => $template->is_active,
            'status' => $template->status,
        ];
    }

    public function renderTemplate($langId, $variables = [])
    {
        $translation = $this->getTranslation($langId);

        $subject = $translation['subject'];
        $body = $translation['body'];

        // Replace variables in subject and body
        foreach ($variables as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }
            $valStr = (string)$value;
            // Support multiple placeholder formats
            $placeholders = [
                '{{ $' . $key . ' }}',
                '{{$' . $key . '}}',
                '{{ ' . $key . ' }}',
                '{{' . $key . '}}',
            ];
            foreach ($placeholders as $placeholder) {
                $subject = str_replace($placeholder, $valStr, $subject);
                $body = str_replace($placeholder, $valStr, $body);
            }
        }

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}

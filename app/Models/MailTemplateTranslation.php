<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailTemplateTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'mail_template_id',
        'lang_id',
        'subject',
        'body'
    ];
     /**
     * Get the mail template that owns this translation
     */
    public function mailTemplate()
    {
        return $this->belongsTo(MailTemplate::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}

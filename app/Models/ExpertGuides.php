<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ExpertGuides extends Model
{
    use HasFactory;
    protected $table = 'expert_guides';

    protected $fillable = [
        'lang_id', 'title', 'description', 'education_title', 'education_description',
        'smart_search', 'smart_search_description', 'how_to_check_email','permanent_url',
        'overview', 'email_description', 'webmail', 'webmail_description',
        'email_application', 'email_app_description', 'imap', 'imap_pop','right_tool_heading','get_start_button','meta_title','meta_description','assistant'
    ];



}



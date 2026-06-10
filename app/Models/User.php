<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'number',
        'user_type',
        'country_id',
        'profile_image',
        'job_title',
        'industry',
        'company_size',
        'status',
        'business_id', // Added to allow mass assignment of business_id
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getUserImageAttribute($mediaId)
    {
        $media = Media::find($this->profile_image);

        if ($media) {
            return asset($media->dir_path . '/' . $media->file_name);
        }

        return null;
    }

    public function disabledMailTemplates()
    {
        return $this->belongsToMany(MailTemplate::class, 'disabled_mail_templates');
    }

    public function hasDisabledTemplate($templateId)
    {
        return $this->disabledMailTemplates->contains('id', $templateId);
    }

    /**
     *  Relationship: A user belongs to a business.
     */

     public function business()
     {
         return $this->belongsTo(Business::class, 'business_id');
     }
}

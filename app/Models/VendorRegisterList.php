<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class VendorRegisterList extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'first_name',
//         'last_name',
//         'email',
//         'password',
//         'business_id',
//         'status',   // Add status here
//     ];

//     protected $hidden = [
//         'password', // Hide password when returning models
//     ];

//     // Relationship to Business model
//     public function business()
//     {
//         return $this->belongsTo(Business::class, 'business_id');
//     }
// }

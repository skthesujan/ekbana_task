<?php

namespace App\Models;

use App\Models\CompanyCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'image', 'description', 'status'];

    public function category()
    {
        return $this->belongsTo(CompanyCategory::class, 'category_id');
    }

    // If company deleted than image associated with that company should also be deleted.
    protected static function booted()
    {
        static::deleting(function ($company) {
            if ($company->image && Storage::disk('public')->exists($company->image)) {
                Storage::disk('public')->delete($company->image);
            }
        });
    }
}

<?php

namespace App\Models;

use App\Models\CompanyCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'image', 'description', 'status'];

    public function category()
    {
        return $this->belongsTo(CompanyCategory::class, 'category_id');
    }
}

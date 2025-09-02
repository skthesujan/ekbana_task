<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyCategory extends Model
{
    use HasFactory;
    protected $fillable = ['title'];

    public function companies()
    {
        return $this->hasMany(Company::class, 'category_id');
    }
}

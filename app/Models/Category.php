<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';

    protected $fillable = ['name', 'description'];

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'category_id');
    }
}

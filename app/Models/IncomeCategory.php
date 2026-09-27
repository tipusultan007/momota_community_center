<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class IncomeCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'description',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}

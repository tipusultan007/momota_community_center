<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

class ExpenseCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'description',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

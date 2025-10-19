<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static ContactFactory<$this> factory($count = null, $state = [])
 * @method static Contact create(array $attributes = [])
 * @method static Builder|Contact query()
 * @method static void truncate()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact search(?string $term)
 */
class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;
    protected $fillable = ['email','first_name','last_name'];

    public function scopeSearch(Builder $q, ?string $term): void
    {
        if (in_array($term, [null, '', '0'], true)) {
            return;
        }

        $q->where(function ($x) use ($term): void {
            $x->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

}

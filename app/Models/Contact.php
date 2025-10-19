<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Contact
 *
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<self> query()
 * @method static Builder<self> newQuery()
 * @method static Builder<self> newModelQuery()
 * @method static self create(array<string, mixed> $attributes = [])
 * @method static Factory<self> factory(?int $count = null, array<string, mixed>|callable $state = [])
 * @method static void truncate()
 * @method static Builder<self> search(?string $term)
 */
class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;
    protected $fillable = ['email','first_name','last_name'];

    /**
     * @param Builder<self> $q
     */
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

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Get the user's full name.
     *
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => trim($this->first_name.' '.$this->last_name)
        );
    }

    /**
     * Get the user's full name including salutation and middle name.
     */
    public function fullName(): string
    {
        $parts = array_filter([
            $this->salutation,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::upper(
            Str::substr($this->first_name, 0, 1).Str::substr($this->last_name, 0, 1)
        );
    }

    /**
     * Get the rental objects where this user is a contact.
     *
     * @return BelongsToMany<RentalObject, $this>
     */
    public function rentalObjects(): BelongsToMany
    {
        return $this->belongsToMany(RentalObject::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the flats where this user is a tenant.
     *
     * @return BelongsToMany<Flat, $this>
     */
    public function tenancies(): BelongsToMany
    {
        return $this->belongsToMany(Flat::class)
            ->withPivot(['move_in_date', 'move_out_date'])
            ->withTimestamps();
    }

    /**
     * Get the notes created by this user.
     *
     * @return HasMany<Note, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the listings created by this user.
     *
     * @return HasMany<Listing, $this>
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'created_by');
    }
}

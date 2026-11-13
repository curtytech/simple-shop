<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'email_verified_at',
        'password',
        'slogan',
        'celphone',
        'logo',
        'banner',
        'role',
        'reference_point',
        'address',
        'number',
        'city',
        'state',
        'country',
        'zipcode',
        'instagram',
        'facebook',
        'site',
        'mercadopago_public_key',
        'mercadopago_access_token',
        'mercadopago_sandbox',
        'mercadopago_environment',
        'mercadopago_webhook_url',
        'mercadopago_webhook_secret',
        'mercadopago_sandbox_public_key',
        'mercadopago_sandbox_access_token',
        'mercadopago_sandbox_webhook_url',
        'mercadopago_sandbox_webhook_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'mercadopago_sandbox' => 'boolean',
    ];

    public function setMercadopagoEnvironmentAttribute(string $environment): void
    {
        $this->attributes['mercadopago_environment'] = $environment;
        $this->attributes['mercadopago_sandbox'] = $environment === 'sandbox';
    }

    /**
     * Return the credentials configured for the requested or active environment.
     *
     * @return array{public_key: ?string, access_token: ?string, webhook_url: ?string, webhook_secret: ?string}
     */
    public function mercadopagoCredentials(?string $environment = null): array
    {
        $isSandbox = ($environment ?? $this->mercadopago_environment) === 'sandbox';
        $prefix = $isSandbox ? 'mercadopago_sandbox_' : 'mercadopago_';

        return [
            'public_key' => $this->getAttribute($prefix . 'public_key'),
            'access_token' => $this->getAttribute($prefix . 'access_token'),
            'webhook_url' => $this->getAttribute($prefix . 'webhook_url'),
            'webhook_secret' => $this->getAttribute($prefix . 'webhook_secret'),
        ];
    }

    /**
     * Get the categories for the user.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the products for the user.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the clients for the user.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the carts for the store.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}

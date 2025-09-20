<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'password',
        'logo',
        'banner',
        'role',
        'phone',
        'celphone',
        'slogan',
        'image_banner',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the appointment times for the user.
     */
    public function appointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class);
    }

    /**
     * Get active appointment times for the user.
     */
    public function activeAppointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class)->where('is_active', true);
    }

    /**
     * Get available appointment times for the user.
     */
    public function availableAppointmentTimes()
    {
        return $this->hasMany(AppointmentTime::class)
            ->where('type', 'available')
            ->where('is_active', true);
    }

    /**
     * Get break times for the user.
     */
    public function breakTimes()
    {
        return $this->hasMany(AppointmentTime::class)
            ->whereIn('type', ['break', 'lunch'])
            ->where('is_active', true);
    }

    /**
     * Get the appointments for the user.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get upcoming appointments for the user.
     */
    public function upcomingAppointments()
    {
        return $this->hasMany(Appointment::class)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('appointment_time');
    }

    /**
     * Get today's appointments for the user.
     */
    public function todayAppointments()
    {
        return $this->hasMany(Appointment::class)
            ->where('date', now()->toDateString())
            ->orderBy('appointment_time');
    }

    /**
     * Get the services for the user.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the categories for the user.
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the products for the user.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

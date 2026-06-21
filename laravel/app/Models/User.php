<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyNewEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'handle', 'bio', 'avatar_path', 'suno_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $appends = ['avatar_url'];

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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReviewer(): bool
    {
        return in_array($this->role, ['reviewer', 'admin'], true);
    }

    /**
     * メール通知の送信先。メールアドレス変更の確認だけは保留中の新アドレス宛に送る。
     */
    public function routeNotificationForMail(Notification $notification): string
    {
        if ($notification instanceof VerifyNewEmail) {
            return $notification->newEmail;
        }

        return $this->email;
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'submitted_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * アバター画像のURL。S3など外部URLはそのまま、ローカル保存パスは Storage::url で解決する。
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return str_starts_with($this->avatar_path, 'http')
            ? $this->avatar_path
            : Storage::url($this->avatar_path);
    }
}

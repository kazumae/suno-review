<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyNewEmail extends Notification
{
    use Queueable;

    /**
     * @param  string  $newEmail  認証対象の新しいメールアドレス（保留中）
     */
    public function __construct(public string $newEmail)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('【'.config('app.name').'】メールアドレス変更の確認')
            ->greeting('メールアドレス変更の確認')
            ->line('このメールアドレスを '.config('app.name').' のアカウントに設定するリクエストを受け付けました。')
            ->line('下のボタンを押すと変更が完了します。リンクの有効期限は'.$this->expireMinutes().'分です。')
            ->action('メールアドレスを確認する', $url)
            ->line('心当たりがない場合は、このメールを無視してください。アカウントには何も変更されません。')
            ->salutation('— '.config('app.name'));
    }

    /**
     * 署名付き（期限付き）確認URLを生成。hash は新メールアドレスから導出し、
     * 発行後に保留先が変わった場合はリンクを無効化できるようにする。
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'profile.email.verify',
            Carbon::now()->addMinutes($this->expireMinutes()),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($this->newEmail),
            ]
        );
    }

    protected function expireMinutes(): int
    {
        return (int) config('auth.verification.expire', 60);
    }
}

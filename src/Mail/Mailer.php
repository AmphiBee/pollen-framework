<?php

declare(strict_types=1);

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

if (! function_exists('wp_mail')) {
    /**
     * Override wp_mail to use the Laravel mailer.
     *
     * @param  string|array  $to Array or comma-separated list of email addresses to send message.
     * @param  string  $subject Email subject
     * @param  string  $message Message contents
     * @param  string|array  $headers Optional. Additional headers.
     * @param  string|array  $attachments Optional. Files to attach.
     * @return bool Whether the email contents were sent successfully.
     */
    function wp_mail($to, $subject, $message, $headers = '', $attachments = [])
    {
        $values = apply_filters('wp_mail', [$to, $subject, $message, $headers, $attachments]);

        [$to, $subject, $headers, $attachments] = array_values($values);

        Mail::raw($message, function (Message $message) use ($to, $subject, $attachments) {
            $message->to($to)->subject($subject);

            if (! is_array($attachments)) {
                $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
            }

            $attachments = array_filter($attachments);

            if (! empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }
        });

        return true;
    }
}

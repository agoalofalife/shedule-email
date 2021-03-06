<?php
namespace agoalofalife\Tests\Modes;

use agoalofalife\postman\Models\SheduleEmail;
use agoalofalife\postman\Modes\Each;
use agoalofalife\postman\Models\EmailUser;
use agoalofalife\postman\Models\User;
use MailThief\Testing\InteractsWithMail;
use agoalofalife\Tests\TestCase;

class EachTest extends TestCase
{
    use InteractsWithMail;

    public function testPostEmail() : void
    {
        $this->artisan('postman:seed');
        $email =  factory(SheduleEmail::class)->create();
        $user  = factory(User::class)->create();

        factory(EmailUser::class)->create([
            'email_id' => $email->id,
            'user_id' => $user->id,
        ]);
        $task = SheduleEmail::all()->random();

        (new Each())->postEmail($task);

        $this->seeMessageFrom(config('mail.from.address'));
        $this->seeMessageWithSubject($task->email->theme);
        $this->seeMessageFor($user->email);
    }
}
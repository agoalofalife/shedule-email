<?php
namespace agoalofalife\Tests\Modes;

use agoalofalife\postman\Models\SheduleEmail;
use agoalofalife\postman\Modes\OneToAll;
use agoalofalife\Tests\TestCase;
use agoalofalife\postman\Models\EmailUser;
use agoalofalife\postman\Models\User;
use agoalofalife\postman\Models\Status;
use MailThief\Testing\InteractsWithMail;

class OneToAllTest extends TestCase
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

        (new OneToAll)->postEmail($task);

        $this->seeMessageWithSubject($task->email->theme);
        $this->seeMessageFor($user->email);
    }
}
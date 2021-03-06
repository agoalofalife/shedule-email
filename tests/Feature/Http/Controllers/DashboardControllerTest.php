<?php

namespace agoalofalife\Tests\Feature\Http\Controllers;

use agoalofalife\postman\Models\ModePostEmail;
use agoalofalife\postman\Models\SheduleEmail;
use agoalofalife\Tests\TestCase;
use agoalofalife\postman\Models\User;
use agoalofalife\postman\Models\Status;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class DashboardControllerTest extends TestCase
{
    use InteractsWithDatabase;

    public function testIndex() : void
    {
        factory(SheduleEmail::class, 5)->create();

        $this->get('/postman/api/dashboard.table.tasks')->assertJsonStructure([
            [
                'id',
                'date',
                'email_id',
                'mode_id',
                'status_id',
                'status' => [
                   'color_rgb',
                    'created_at',
                    'deleted_at',
                    'description',
                    'id',
                    'name',
                    'updated_at'
                ],
                'email' => [
                    'id',
                    'theme',
                    'text',
                    'users'
                ],
                'mode' => [
                    'id',
                    'name',
                    'description'
                ],
            ]
        ])->assertStatus(200);
    }

    public function testListMode() : void
    {
        factory(ModePostEmail::class, 1)->create();
        $this->get('/postman/api/dashboard.table.listMode')->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ])->assertStatus(200);
    }

    public function testUsers() : void
    {
        factory(User::class, 5)->create();
        $this->get('/postman/api/dashboard.table.users')->assertJsonStructure([
            '*' => [
             'id',
             'name',
             'email',
             'password',
             'remember_token',
             'created_at',
             'updated_at',
            ]
        ])->assertStatus(200);
    }

    public function testStatuses() : void
    {
        $this->artisan('postman:seed');
        $this->get('/postman/api/dashboard.table.statuses')->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'description',
                'unique_name',
                'color_rgb',
                'created_at',
                'updated_at',
            ]
        ])->assertStatus(200);
    }

    public function testTableColumn() : void
    {
        $this->get('/postman/api/dashboard.table.column')->assertJsonStructure([
            'columns' => [
                '*' => [
                    'prop',
                    'size',
                    'label'
                ],
            ]
        ])->assertJsonStructure([
            'button' => [
                'edit',
                'remove'
            ]
        ])->assertStatus(200);
    }

    public function testFormColumn() : void
    {
        $this->get('/postman/api/dashboard.table.formColumn')->assertJsonStructure([
            'date' => [
                'label',
                'rule' => [
                    'required',
                    'message',
                    'trigger',
                ],
            ],
            'theme' => [
                    'label'
                ],
                'text' => [
                    'label'
                ],
                'type' => [
                    'label',
                    'placeholder'
                ],
                'users' => [
                    'label',
                    'placeholder'
                ],
                'button' => [
                    'success',
                    'cancel'
                ],
                'popup' => [
                    'question',
                    'title',
                    'confirmButtonText',
                    'cancelButtonText',
                    'success.message',
                    'info.message'
                ]
        ]);
    }

    public function testCreateTask() : void
    {
        $users = factory(User::class, 3)->create()->map(function ($value) {return $value->id;});
        $status_id = factory(Status::class)->create()->id;
        $this->artisan('postman:seed');

        $theme = $this->faker()->word;
        $text  = $this->faker()->text;
        $mode  = ModePostEmail::all()->random()->id;
        $date  = $this->faker()->date('Y-m-d H:i:s');
        $this->post('/postman/api/dashboard.table.tasks.create',[
            'theme' => $theme,
            'text' => $text,
            'users' => $users->toArray(),
            'date' => $date,
            'mode' => $mode,
            'statuses' => $status_id,
        ])->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('emails', [
            'theme' => $theme,
            'text' => $text
        ]);

        $this->assertDatabaseHas('email_user', [
            'user_id' => $users->random(),
        ]);

        $this->assertDatabaseHas('shedule_emails', [
            'date' => $date,
            'mode_id' => $mode,
            'status_id' => $status_id,
        ]);
    }

    public function testUpdateTask() : void
    {
        $task = factory(SheduleEmail::class)->create();
        $status_id = factory(Status::class)->create()->id;
        $theme = $this->faker()->word;
        $text  = $this->faker()->text;
        $mode  = ModePostEmail::all()->random()->id;
        $date  = $this->faker()->date('Y-m-d H:i:s');
        $users = factory(User::class, 3)->create()->map(function ($value) {return $value->id;});

        $this->put('/postman/api/dashboard.table.tasks.update',[
            'id' => $task->id,
            'mode' => $mode,
            'date' => $date,
            'theme' => $theme,
            'text' => $text,
            'users' => $users->toArray(),
            'statuses' => $status_id,
        ])->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('shedule_emails', [
            'mode_id' => $mode,
            'date' => $date,
            'status_id' => $status_id,
        ]);
        $this->assertDatabaseHas('emails', [
            'theme' => $theme,
            'text' => $text,
        ]);
        $this->assertDatabaseHas('email_user', [
            'email_id' => $task->email->id,
            'user_id' => $users->random(),
        ]);
    }

    public function testRemove() : void
    {
        $task = factory(SheduleEmail::class)->create();

        $this->delete('/postman/api/dashboard.table.tasks.remove/' . $task->id)->assertStatus(200);
        $this->assertSoftDeleted('shedule_emails', [
            'id' => $task->id
        ]);
    }
}
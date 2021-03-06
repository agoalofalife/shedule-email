<?php
return [
    'switcher' => true,
     /*
     |--------------------------------------------------------------------------
     | List modes classes
     |--------------------------------------------------------------------------
     | All list classes mode. Here you can add custom modes.
     */
    'modes' => [
        \agoalofalife\postman\Modes\OneToAll::class,
        \agoalofalife\postman\Modes\Each::class,
    ],
    /*
     |--------------------------------------------------------------------------
     | Middleware
     |--------------------------------------------------------------------------
     | Set middleware
     */
     'middleware' => '',
     /*
      |--------------------------------------------------------------------------
      | Templates Email
      |--------------------------------------------------------------------------
      | Set template emails
      */
     'templates' => [
         \agoalofalife\postman\Modes\Each::class => [
             'name_template' => 'postman::email',
             'variable' => 'html'
         ],
         \agoalofalife\postman\Modes\OneToAll::class => [
             'name_template' => 'postman::email',
             'variable' => 'html'
         ],
     ],
      /*
      |--------------------------------------------------------------------------
      | Sizes table
      |--------------------------------------------------------------------------
      | You can set your sizes for table
      */
    'ui' => [
        'table' => [
            'id' => 60,
            'date' => 180,
            'email.theme' => 180,
            'email.text' => 400,
            'mode.name' => 140,
            'status.name' => 130,
            'updated_at' => 140,
            'operations' => 240,
        ]
    ]
];

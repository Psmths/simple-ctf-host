<?php
    $fields = [
        'challenge_name' => 'string | required | alphanumeric | between: 3, 25',
        'challenge_text' => 'string | required | alphanumeric | between: 3, 25',
        'challenge_category' => 'string | required | alphanumeric | between: 3, 25',
        'challenge_subcategory' => 'string | required | alphanumeric | between: 3, 25',
        'challenge_difficulty' => 'string | required | alphanumeric | between: 3, 25',
    ];

    $messages = [
        'challenge_name' => [
            'required' => 'Please enter the challenge name.'
        ],
        'challenge_text' => [
            'required' => 'Please enter the challenge name.'
        ],
        'challenge_category' => [
            'required' => 'Please enter the challenge name.'
        ],
        'challenge_subcategory' => [
            'required' => 'Please enter the challenge name.'
        ],
        'challenge_difficulty' => [
            'required' => 'Please enter the challenge name.'
        ]
    ];
?>
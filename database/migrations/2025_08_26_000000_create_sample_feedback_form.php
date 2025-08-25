<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Create a sample feedback form similar to the screenshot
        $schema = [
            [
                'type' => 'section',
                'data' => [
                    'heading' => 'feedback',
                    'description' => '',
                    'collapsible' => true,
                    'grid' => 1,
                    'fields' => [
                        [
                            'type' => 'text_input',
                            'data' => [
                                'name' => 'email',
                                'label' => 'please use the same email address you used on registration, so we can add points to your account',
                                'placeholder' => 'Enter your email',
                                'required' => true
                            ]
                        ],
                        [
                            'type' => 'radio',
                            'data' => [
                                'name' => 'rate_experience',
                                'label' => 'rate your experience',
                                'required' => true,
                                'options' => [
                                    ['label' => 'One', 'value' => '1'],
                                    ['label' => 'Two', 'value' => '2'],
                                    ['label' => 'Three', 'value' => '3'],
                                    ['label' => 'Four', 'value' => '4'],
                                    ['label' => 'Five', 'value' => '5']
                                ]
                            ]
                        ],
                        [
                            'type' => 'select',
                            'data' => [
                                'name' => 'recommend',
                                'label' => 'would you recommend our services to others',
                                'required' => true,
                                'options' => [
                                    ['label' => 'Yes, definitely', 'value' => 'yes'],
                                    ['label' => 'Maybe', 'value' => 'maybe'],
                                    ['label' => 'No', 'value' => 'no']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'section',
                'data' => [
                    'heading' => 'your info',
                    'description' => '',
                    'collapsible' => true,
                    'grid' => 1,
                    'fields' => [
                        [
                            'type' => 'text_input',
                            'data' => [
                                'name' => 'user_email',
                                'label' => 'your Email',
                                'required' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('dynamic_forms')->insert([
            'name' => 'Feedback',
            'slug' => 'feedback',
            'description' => 'send us your Feedback about our service',
            'schema' => json_encode($schema),
            'success_message' => 'Thank you for your feedback!',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function down(): void
    {
        DB::table('dynamic_forms')->where('slug', 'feedback')->delete();
    }
};

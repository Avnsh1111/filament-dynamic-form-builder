<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dynamic_forms', function (Blueprint $table) {
            $table->json('notify_emails')->nullable()->after('is_active');
            $table->string('email_subject')->nullable()->after('notify_emails');
            $table->text('email_template')->nullable()->after('email_subject');
            $table->boolean('send_copy_to_submitter')->default(false)->after('email_template');
        });
    }

    public function down(): void
    {
        Schema::table('dynamic_forms', function (Blueprint $table) {
            $table->dropColumn([
                'notify_emails',
                'email_subject',
                'email_template',
                'send_copy_to_submitter',
            ]);
        });
    }
};

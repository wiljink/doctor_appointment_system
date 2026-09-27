<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('patient')->index()->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('blood_group', 5)->nullable()->after('gender');
            $table->text('address')->nullable()->after('blood_group');
            $table->string('emergency_contact')->nullable()->after('address');
            $table->string('avatar')->nullable()->after('emergency_contact');
            $table->boolean('is_active')->default(true)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'date_of_birth',
                'gender',
                'blood_group',
                'address',
                'emergency_contact',
                'avatar',
                'is_active',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('code');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('verifications');
    }
};

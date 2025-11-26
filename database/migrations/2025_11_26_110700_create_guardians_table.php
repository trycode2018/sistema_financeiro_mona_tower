<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('relationship'); // pai, mãe, tutor, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guardians');
    }
};
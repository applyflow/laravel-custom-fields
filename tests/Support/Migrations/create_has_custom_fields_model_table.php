<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHasCustomFieldsModelTable extends Migration
{
    public function up()
    {
        Schema::create('has_custom_fields_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('has_custom_fields_models');
    }
}

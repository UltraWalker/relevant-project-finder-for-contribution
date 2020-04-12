<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotTablePackagesTags extends Migration
{
    const TABLENAME = "package_tag";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLENAME, function (Blueprint $table) {
            $table->integer('package_id')->unsigned()->index();
            $table->integer('tag_id')->unsigned()->index();

            $table->primary(['package_id', 'tag_id']);

            $table->timestamps();
        });

//        Schema::table(self::TABLENAME, function (Blueprint $table) {
//            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
//            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLENAME);
    }
}

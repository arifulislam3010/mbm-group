<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->mediumText('syllabus')->nullable();
            $table->mediumText('details')->nullable();
            $table->mediumText('objective')->nullable();
            $table->mediumText('course_motto')->nullable();
            $table->longText('requirement')->nullable();
            $table->mediumText('course_requirment')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('admin_featured')->default(false);
            $table->tinyInteger('status')->nullable();
            $table->longText('marks')->nullable();
            $table->integer('banner_cb_id')->unsigned()->nullable();
            $table->integer('video_cb_id')->unsigned()->nullable();
            $table->integer('discount_id')->unsigned()->nullable();
            $table->integer('clone_status')->nullable();
            $table->tinyInteger('payment_status')->default('0');
            $table->tinyInteger('enrolment_approval_status')->default('0')->comment('0 for Auto and 1 for Manual');
            $table->tinyInteger('payment_point_status')->default('1')->comment('1 for Enrolment, 2 for Exam, 3 for Certificate')->nullable();
            $table->float('payment_point_amount',8, 2)->nullable();
            $table->string('code')->nullable();
            $table->string('duration')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('reg_start_date')->nullable();
            $table->date('reg_end_date')->nullable();
            $table->tinyInteger('admission_status')->default('1');
            $table->string('limit')->nullable();
            $table->tinyInteger('migration_allowe')->default('0');
            $table->tinyInteger('migration_fee')->default('0');
            $table->tinyInteger('mig_pa_status')->default('0');
            $table->tinyInteger('courses_for_status')->default('0');
            $table->tinyInteger('study_mode')->default(0)->comment('0 for Open, 1 for Setp by Step');
            $table->float('mig_payment_amount',8, 2)->nullable();
            $table->integer('certificate_id')->unsigned()->nullable();
            $table->string('certificate_alias_name')->nullable();
            $table->boolean('certificate_approval_status')->default('0')->comment('0 for Auto, 1 for Manual');
            $table->date('certificate_approval_date')->nullable();
            $table->integer('course_id')->unsigned();
            $table->string('course_alias_name')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_batches');
    }
}

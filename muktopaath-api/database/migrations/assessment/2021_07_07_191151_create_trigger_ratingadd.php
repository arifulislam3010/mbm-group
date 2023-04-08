<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggerRatingadd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER `after_adding_rating` AFTER INSERT ON `reviews`
            FOR EACH ROW UPDATE course_batches
            SET rating_sum = rating_sum + NEW.rating, rating_count = rating_count + 1
            WHERE course_batches.id = NEW.course_batch_id
        ');

        DB::unprepared('
            CREATE TRIGGER `after_update_rating` AFTER UPDATE ON `reviews`
            FOR EACH ROW UPDATE course_batches
            SET rating_sum = rating_sum - OLD.rating + NEW.rating
            WHERE course_batches.id = NEW.course_batch_id
        ');

        DB::unprepared('
            CREATE TRIGGER `after_delete_rating` AFTER DELETE ON `reviews`
            FOR EACH ROW UPDATE course_batches
            SET rating_sum = rating_sum - OLD.rating , rating_count = rating_count - 1
            WHERE course_batches.id = OLD.course_batch_id
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
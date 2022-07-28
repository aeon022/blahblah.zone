<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateProjectSprintTasksTable
 *
 * The Migrations is Defined for Project Sprint Task.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\ProjectSprintTask
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateProjectSprintTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.project_sprint_tasks_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Schema::create(
            config('core.acl.project_sprint_tasks_table'),
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('project_sprint_id')->unsigned();
                $table->foreign('project_sprint_id')
                    ->references('id')
                    ->on(config('core.acl.project_sprints_table'))
                    ->onDelete('cascade');
                $table->string('task_name');
                $table->text('description')->nullable();
                $table->tinyInteger('status')
                    ->default(1)
                    ->comment('task:1=Draft,2=InProgress,3=Completed|story:1=Open,2=InProgress,3=OnHold,4=Waiting,5=Cancel,6=Completed');
                $table->string('type');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('hours')->nullable();
                $table->softDeletes();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.project_sprint_tasks_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

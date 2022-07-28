<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 * Class CreateTodoUserPivotTable
 *
 * The Migrations is Defined for Todo.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Todo
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateTodoUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.todo_user_pivot'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Schema::create(
            config('core.acl.todo_user_pivot'),
            function (Blueprint $table) {
                $table->integer('todo_id')->unsigned()->index();
                $table->foreign('todo_id')
                    ->references('id')
                    ->on(config('core.acl.user_todos_table'))
                    ->onDelete('cascade');
                $table->integer('user_id')->unsigned()->index();
                $table->foreign('user_id')
                    ->references('id')
                    ->on(config('core.acl.users_table'))
                    ->onDelete('cascade');
                $table->primary(['todo_id', 'user_id']);
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
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop(config('core.acl.todo_user_pivot'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserTodosTable
 *
 * The Migrations is Defined for User Todos.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Todos
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateUserTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.user_todos_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Schema::create(
            config('core.acl.user_todos_table'),
            function (Blueprint $table) {
                $table->increments('id')->unsigned();
                $table->integer('user_id')->unsigned()->nullable(false);
                $table->foreign('user_id')
                    ->references('id')
                    ->on(config('core.acl.users_table'))
                    ->onDelete('cascade');
                $table->string('title')->nullable(false);
                $table->tinyInteger('status')
                    ->default(1)
                    ->nullable(false)
                    ->comment(
                        '1=not_started, 2=in_progress, 3=on_hold, 4=completed'
                    );
                $table->date('due_date')->nullable(false);
                $table->integer('order')->nullable();
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
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.user_todos_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

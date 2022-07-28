<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSettingsTable
 *
 * The Migrations is Defined for Setting.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Setting
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(config('core.acl.users_settings_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Schema::create(
            config('core.acl.users_settings_table'),
            function (Blueprint $table) {
                $table->increments('id')->unsigned();
                $table->string('company_name', 150)->nullable();
                $table->string('company_legal_name', 150)->nullable();
                $table->string('company_short_name', 50)->default('VipsPM');
                $table->string('contact_person', 150)->nullable();
                $table->text('company_address')->nullable();
                $table->bigInteger('company_country')->default(8);
                $table->string('company_city', 150)->nullable();
                $table->string('company_zipcode', 10)->nullable();
                $table->string('company_phone', 15)->nullable();
                $table->string('company_email')->nullable();
                $table->string('company_website')->nullable();
                $table->string('company_from_email')->nullable();
                $table->boolean('post_mark')->default(false);
                $table->string('smtp_protocol', 50)->nullable();
                $table->string('smtp_host')->nullable();
                $table->string('smtp_user')->nullable();
                $table->string('smtp_password')->nullable();
                $table->string('smtp_port', 10)->nullable();
                $table->string('smtp_encryption', 10)->nullable();
                $table->string('sparkpost_secret')->nullable();
                $table->string('mailgun_domain')->nullable();
                $table->string('mailgun_secret')->nullable();
                $table->string('mandrill_secret')->nullable();
                $table->boolean('is_announcement_notification')->default(false);
                $table->boolean('active_cronjob')->default(true);
                $table->boolean('automatic_backup')->default(false);
                $table->string('last_autobackup')->default(0);
                $table->string('cronjob_link')->nullable();
                $table->string('last_cronjob_run')->nullable();
                $table->string('login_background')->nullable();
                $table->string('company_logo')->nullable();
                $table->string('company_sidebar_logo')->nullable();
                $table->text('sidebar_background_images')->nullable();
                $table->boolean('is_announcement_dashboard')->default(true);
                $table->boolean('is_activities_dashboard')->default(true);
                $table->boolean('is_todos_dashboard')->default(true);
                $table->boolean('is_meetings_dashboard')->default(true);
                $table->boolean('is_projects_dashboard')->default(true);
                $table->boolean('is_tasks_dashboard')->default(true);
                $table->boolean('is_defects_dashboard')->default(true);
                $table->boolean('is_incidents_dashboard')->default(true);
                $table->string('default_language', 50)->nullable();
                $table->string('default_locale', 100)->nullable();
                $table->string('timezone')->nullable();
                $table->integer('tables_pagination_limit')->default(10);
                $table->string('date_format', 50)->default('YYYY-MM-DD');
                $table->string('purchase_key')->nullable();
                $table->string('unique_coder')->nullable();
                $table->string('update_url')->nullable();
                $table->boolean('is_collapsed_menu')->default(false);
                $table->boolean('is_sidebar_background')->default(true);
                $table->string('theme_layout', 50)->default('light');
                $table->string('sidebar_bg_color', 100)->default('pomegranate-gr');
                $table->string('sidebar_transparent_bg_image', 100)->default('bg_glass_1');
                $table->string('sidebar_bg_custom_color', 50)->default('#000000');
                $table->string('sidebar_font_color', 50)->default('black');
                $table->string('sidebar_width', 50)->default('medium');
                $table->integer('sidebar_bg_image')->default(0);
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
        Schema::dropIfExists(config('core.acl.users_settings_table'));
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

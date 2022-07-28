<?php

use Illuminate\Database\Seeder;
use Modules\CustomField\Database\Seeders\CustomFieldDatabaseSeeder;
use Modules\EmailGroup\Database\Seeders\EmailGroupDatabaseSeeder;
use Modules\EmailTemplate\Database\Seeders\EmailTemplateDatabaseSeeder;
use Modules\Helper\Database\Seeders\HelperDatabaseSeeder;
use Modules\Menu\Database\Seeders\MenuDatabaseSeeder;
use Modules\Setting\Database\Seeders\SettingDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(HelperDatabaseSeeder::class);
        $this->call(CustomFieldDatabaseSeeder::class);
        $this->call(EmailGroupDatabaseSeeder::class);
        $this->call(EmailTemplateDatabaseSeeder::class);
        $this->call(MenuDatabaseSeeder::class);
        $this->call(UserDatabaseSeeder::class);
        $this->call(SettingDatabaseSeeder::class);
    }
}

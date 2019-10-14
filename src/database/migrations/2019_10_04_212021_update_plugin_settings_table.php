<?php

use AlexStack\LaravelCms\Models\LaravelCmsSetting;
use Illuminate\Database\Migrations\Migration;

class UpdatePluginSettingsTable extends Migration
{
    private $config;
    private $table_name;

    public function __construct()
    {
        $this->config     = include base_path('config/laravel-cms.php');
        $this->table_name = $this->config['table_name']['settings'];
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        $setting_data = [
            'category'        => 'plugin',
            'param_name'      => 'page-tab-sub-page',
            'input_attribute' => '{"rows":10,"required":"required"}',
            'enabled'         => 1,
            'sort_value'      => 50,
            'abstract'        => 'Display sub pages of the current page when you edit a page, drag & drop to change sort orders(sort_value). <a href="https://www.laravelcms.tech" target="_blank"><i class="fas fa-link mr-1"></i>Tutorial</a>',
            'param_value'     => '{
"plugin_name" : "Sub Page",
"blade_file" : "sub-page",
"tab_name" : "<i class=\'fas fa-list-alt mr-1\'></i>__(sub,page)",
"php_class"  : "Amila\\\\LaravelCms\\\\Plugins\\\\SubPage\\\\Controllers\\\\SubPageController",
"number_per_page" : 40,
"plugin_type" : "page-tab"
}',
        ];
        LaravelCmsSetting::UpdateOrCreate(
            ['category'=>'plugin', 'param_name' => 'page-tab-sub-page'],
            $setting_data
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        LaravelCmsSetting::where('param_name', 'page-tab-sub-page')->where('category', 'plugin')->delete();
    }
}

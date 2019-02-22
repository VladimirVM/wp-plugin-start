<?php
namespace WPPluginStart\Plugin;

class Control
{

    public function __construct()
    {
        register_activation_hook(Settings::$plugin_main_file, ['WPPluginStart\Plugin\Control', 'activation']);
        register_deactivation_hook(Settings::$plugin_main_file, ['WPPluginStart\Plugin\Control', 'deactivation']);
        register_uninstall_hook(Settings::$plugin_main_file, ['WPPluginStart\Plugin\Control', 'uninstall']);
        add_action('upgrader_process_complete', [$this, 'upgrade'], 10, 2);
    }


    static function upgrade($upgrader_object, $options)
    {

    }

    static function init()
    {

    }


    public static function activation()
    {
        $options = Settings::get('options');
        $autoload = Settings::get('options_autoload', []);
        foreach ($options as $key => $value) {
            add_option($key, $value, '', in_array($key, $autoload));
        }

        return DB\Table::createAll();
    }

    public static function deactivation()
    {

    }

	public static function uninstall()
    {
        $options = Settings::get('options');
        foreach ($options as $key => $value) {
            delete_option($key);
        }
        return DB\Table::removeAll();
    }

}

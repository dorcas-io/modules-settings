<?php

namespace Dorcas\ModulesSettings;
use Illuminate\Support\ServiceProvider;

class ModulesSettingsServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-settings');
		$this->publishes([
			__DIR__.'/config/modules-settings.php' => config_path('modules-settings.php'),
		], 'dorcas-modules');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-settings')
		], 'dorcas-modules');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-settings.sub-menu'
	     );
	}

}


?>
<?php

namespace Dorcas\ModulesSettings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesSettings\Models\ModulesSettings;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModulesSettingsController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-settings.title')],
            'header' => ['title' => config('modules-settings.title')],
            'selectedMenu' => 'settings-main'
        ];
    }

    public function index()
    {
    	//$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-settings::index', $this->data);
    }


}
<?php

namespace Dorcas\ModulesSettings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\Contactform\Models\ModulesSettings;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;

class ModulesSettingsController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Settings Module'],
            'header' => ['title' => 'Settings Module'],
            'selectedMenu' => 'sales'
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-settings::index', $this->data);
    }


}
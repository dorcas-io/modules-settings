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
use App\Dorcas\Hub\Enum\Banks;


class ModulesSettingsController extends Controller {

    public function __construct()
    {
        parent::__construct();
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-settings.title')],
            'header' => ['title' => 'Settings'],
            'selectedMenu' => 'modules-settings',
            'submenuConfig' => 'navigation-menu.modules-settings.sub-menu',
            'submenuAction' => ''
        ];  
    }

    public function index()
    {
    	//$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-settings::index', $this->data);
    }

    public function business_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        $location = ['address1' => '', 'address2' => '', 'state' => ['data' => ['id' => '']]];
        # the location information
        $locations = $this->getLocations($sdk);
        $location = !empty($locations) ? $locations->first() : $location;
        $this->data['states'] = $sts = Controller::getDorcasStates($sdk);
        # get the states
        $this->data['location'] = $location;
        return view('modules-settings::business', $this->data);
    }

    public function business_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required_if:action,update_business|string|max:100',
            'registration' => 'nullable|string|max:30',
            'phone' => 'required_if:action,update_business|string|max:30',
            'email' => 'required_if:action,update_business|email|max:80',
            'website' => 'nullable|url|max:80',
            'address1' => 'required_if:action,update_location|string|max:100',
            'address2' => 'nullable|string|max:100',
            'city' => 'required_if:action,update_location|string|max:100',
            'state' => 'required_if:action,update_location|string|max:50',
        ]);
        # validate the request
        try {
            $company = $request->user()->company(true, true);
            # get the company information
            if ($request->action === 'update_business') {
                # update the business information
                $query = $sdk->createCompanyService()
                                ->addBodyParam('name', $request->name, true)
                                ->addBodyParam('registration', $request->input('registration', ''))
                                ->addBodyParam('phone', $request->input('phone', ''))
                                ->addBodyParam('email', $request->input('email', ''))
                                ->addBodyParam('website', $request->input('website', ''))
                                ->send('PUT');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your business information. Please try again.');
                }
                $message = ['Successfully updated business information for '.$request->name];
            } else {
                # update address information

                $locations = $this->getLocations($sdk);
                $location = !empty($locations) ? $locations->first() : null;
                $query = $sdk->createLocationResource();
                # get the query
                $query = $query->addBodyParam('address1', $request->address1)
                                ->addBodyParam('address2', $request->address2)
                                ->addBodyParam('city', $request->city)
                                ->addBodyParam('state', $request->state);
                # add the payload
                if (!empty($location)) {
                    $response = $query->send('PUT', [$location->id]);
                } else {
                    $response = $query->send('POST');
                }
                if (!$response->isSuccessful()) {
                    throw new \RuntimeException('Sorry but we encountered issues while updating your address information.');
                }
                Cache::forget('business.locations.'.$company->id);
                # forget the cache data
                $message = ['Successfully updated your company address information.'];
            }
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function personal_index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('modules-settings::personal', $this->data);
    }

    
    /**
     * @param Request $request
     * @param Sdk $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function personal_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:80',
            'gender' => 'nullable|string|in:female,male'
        ]);
        # validate the request
        try {
            $query = $sdk->createProfileService()
                            ->addBodyParam('firstname', $request->firstname)
                            ->addBodyParam('lastname', $request->lastname)
                            ->addBodyParam('phone', $request->phone)
                            ->addBodyParam('email', $request->email)
                            ->addBodyParam('gender', (string) $request->gender)
                            ->send('PUT');
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException($query->getErrors()[0]['title']);
            }
            $response = (tabler_ui_html_response(['Successfully updated profile information']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function security_index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('modules-settings::security', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function security_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'password' => 'required|string|confirmed'
        ]);
        # validate the request
        try {
            $query = $sdk->createProfileService()
                            ->addBodyParam('password', $request->password)
                            ->send('PUT');
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException($query->getErrors()[0]['title']);
            }
            $response = (tabler_ui_html_response(['Successfully updated profile information']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function customization_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        return view('modules-settings::customization', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function customization_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'logo' => 'required_if:action,customise_logo|image',
        ]);
        # validate the request
        try {
            if ($request->action === 'customise_logo') {
                # update the business information
                $file = $request->file('logo');
                $query = $sdk->createCompanyService()
                                ->addMultipartParam('logo', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                                ->send('post');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your business logo. Please try again.');
                }
                $message = ['Successfully updated your customisation preference'];
            }
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function billing_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        $configuration = !empty($company->extra_data) ? $company->extra_data : [];
        $this->data['billing'] = $configuration['billing'] ?? [];
        if (!empty($configuration['paystack_authorization_code']) && !isset($this->data['billing']['auto_billing'])) {
            $this->data['billing']['auto_billing'] = true;
        } elseif (!isset($this->data['billing']['auto_billing'])) {
            $this->data['billing']['auto_billing'] = false;
        }
        $this->data['billing']['auto_billing'] = (int) $this->data['billing']['auto_billing'];
        return view('modules-settings::billing', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function billing_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'auto_billing' => 'required|numeric|in:0,1',
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        $action = strtolower($request->input('action'));
        try {
            if ($action === 'save_billing') {
                # update the business information
                $configuration = !empty($company->extra_data) ? $company->extra_data : [];
                if (empty($configuration['billing'])) {
                    $configuration['billing'] = [];
                }
                $configuration['billing']['auto_billing'] = (bool) intval($request->input('auto_billing'));
                $query = $sdk->createCompanyService()->addBodyParam('extra_data', $configuration)
                                                    ->send('post');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your billing preferences. Please try again.');
                }
                $message = ['Successfully updated your billing preferences'];
            }
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    


    public function banking_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $accounts = $this->getBankAccounts($sdk);
        if (!empty($accounts) && $accounts->count() > 0) {
            $this->data['account'] = $account = $accounts->first();
        } else {
            $this->data['default'] = [
                'account_number' => '',
                'account_name' => $request->user()->firstname . ' ' . $request->user()->lastname,
                'json_data' => [
                    'bank_code' => ''
                ]
            ];
        }
        $this->data['banks'] = collect(Banks::BANK_CODES)->sort()->map(function ($name, $code) {
            return ['name' => $name, 'code' => $code];
        })->values();
        return view('modules-settings::banking', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function banking_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'bank' => 'required|numeric|max:100',
            'account_number' => 'required|string|max:30',
            'account_name' => 'required|string|max:80'
        ]);
        # validate the request
        try {
            $bankName = Banks::BANK_CODES[$request->bank];
            # we get the name of the specific bank for submission
            $query = $sdk->createProfileService();
            # get the query object
            $payload = $request->only(['account_number', 'account_name']);
            foreach ($payload as $key => $value) {
                $query = $query->addBodyParam($key, $value);
            }
            $query = $query->addBodyParam('json_data', ['bank_code' => $request->bank, 'bank_name' => $bankName]);
            # set the json data for the bank account
            $accounts = $this->getBankAccounts($sdk);
            if (!empty($accounts) && $accounts->count() > 0) {
                $response = $query->send('PUT', ['bank-accounts', $accounts->first()->id]);
            } else {
                $response = $query->send('POST', ['bank-accounts']);
            }
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->getErrors()[0]['title'] ?: 'Failed while updating your bank information. Please try again.');
            }
            Cache::forget('user.bank-accounts.'.$request->user()->id);
            # clear the cache
            $message = ['Successfully updated bank account information.'];
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
    


    





}
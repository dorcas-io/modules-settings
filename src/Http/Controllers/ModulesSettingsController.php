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
use Carbon\Carbon;


class ModulesSettingsController extends Controller {

    public function __construct()
    {
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
        $this->data['page']['title'] .= ' &rsaquo; Business';
        $this->data['header']['title'] = 'Business Settings';
        $this->data['selectedSubMenu'] = 'settings-business';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        $location = ['address1' => '', 'address2' => '', 'state' => ['data' => ['id' => '']]];
        # the location information
        $locations = $this->getLocations($sdk);
        $location = !empty($locations) ? $locations->first() : $location;
        $this->data['states'] = $sts = Controller::getDorcasStates($sdk);
        # get the states
        $this->data['countries'] = $this->getCountries($sdk);
        $this->data['location'] = $location;
        $this->data['env'] = [
            "SETTINGS_COUNTRY" => env('SETTINGS_COUNTRY', 'NG')
        ];

        $company_data = $company->extra_data;

        if ( !empty($company_data['location']) ) {
            $this->data['company']['extra_data']['location'] = ['latitude' => 0, 'longitude' => 0];
        }
        
        return view('modules-settings::business', $this->data);
    }

    public function business_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required_if:action,update_business|string|max:100',
            'registration' => 'nullable|string|max:30',
            'phone' => 'required_if:action,update_business|string|max:30',
            'email' => 'required_if:action,update_business|email|max:80',
            'website' => 'nullable|string|max:80',
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

                // Update Geo Location in company meta data
                $company = $request->user()->company(true, true);
                
                $configuration = !empty($company->extra_data) ? $company->extra_data : [];

                if (empty($configuration['location'])) {
                    $configuration['location'] = [];
                }
                $configuration['location']['latitude'] = $request->input('latitude');
                $configuration['location']['longitude'] = $request->input('longitude');
                $queryL = $sdk->createCompanyService()->addBodyParam('extra_data', $configuration)
                                                    ->send('post');
                # send the request
                if (!$queryL->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your geo-location data. Please try again.');
                }


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
        $this->data['page']['title'] .= ' &rsaquo; Personal';
        $this->data['header']['title'] = 'Personal Settings';
        $this->data['selectedSubMenu'] = 'settings-personal';
        $this->data['submenuAction'] = '';
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
        $this->data['page']['title'] .= ' &rsaquo; Security';
        $this->data['header']['title'] = 'Security Settings';
        $this->data['selectedSubMenu'] = 'settings-security';
        $this->data['submenuAction'] = '';

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
            $response = (tabler_ui_html_response(['Successfully changed your password']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function customization_index(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Customization';
        $this->data['header']['title'] = 'Customization Settings';
        $this->data['selectedSubMenu'] = 'settings-customization';
        $this->data['submenuAction'] = '';

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
                    //throw new \RuntimeException($query->getErrors()[0]['title']);
                    //throw new \RuntimeException('Failed while updating your business logo. Please try again.');
                    throw new \RuntimeException('Failed while updating your business logo: '. $query->getErrors()[0]['title']);
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
        $this->data['page']['title'] .= ' &rsaquo; Billing';
        $this->data['header']['title'] = 'Billing Settings';
        $this->data['selectedSubMenu'] = 'settings-billing';
        $this->data['submenuAction'] = '';
        $this->middleware(['guest','edition_commercial_only']);

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


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function billing_coupon(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'coupon' => 'required_with:redeem_coupon|string|max:30'
        ]);
        # validate the request
        $response = null;
        try {
            
            if ($request->has('redeem_coupon')) {
                # to reserve a subdomain
                $response = $sdk->createCouponResource($request->input('coupon'))
                                ->addBodyParam('select_using', 'code')
                                ->send('post', ['redeem']);
                # send the request
                if (!$response->isSuccessful()) {
                    # it failed
                    $message = $response->errors[0]['title'] ?? '';
                    throw new \RuntimeException('Failed while redeeming the coupon. ' . $message);
                }
                $response = (tabler_ui_html_response(['Successfully performed upgrade/extension on plan.']))->setType(UiResponse::TYPE_SUCCESS);
            
            }
        } catch (ServerException $e) {
            $message = json_decode((string) $e->getResponse()->getBody(), true);
            $response = (tabler_ui_html_response([$message['message']]))->setType(UiResponse::TYPE_ERROR);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(route('settings-billing'))->with('UiResponse', $response);
    }    


    public function banking_index(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Banking';
        $this->data['header']['title'] = 'Banking Settings';
        $this->data['selectedSubMenu'] = 'settings-banking';
        $this->data['submenuAction'] = '';

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
    


    public function access_grants_index(Request $request)
    {
        $this->data['page']['title'] .= ' &rsaquo; Permissions';
        $this->data['header']['title'] = 'Permissions';
        $this->data['selectedSubMenu'] = 'settings-access-grants';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
        return view('modules-settings::access-grants', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function access_grants_post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'business_id' => 'required|string',
            'modules' => 'required|array',
            'modules.*' => 'required|string'
        ]);
        # validate the request
        try {
            $query = $sdk->createCompanyResource($request->input('business_id'));
            $data = $request->only(['modules']);
            foreach ($data as $key => $value) {
                $query->addBodyParam($key, $value);
            }
            $query = $query->send('post', ['access-grant-requests']);
            # send the request
            if (!$query->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while sending the request. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully sent the request.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function access_grants_search(Request $request, Sdk $sdk)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createCompanyService();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('statuses')) {
            $resource->addQueryArgument('statuses', $request->input('statuses'));
        }
        $response = $resource->send('get', ['access-grant-requests']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching requests.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function access_grants_delete(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createCompanyService();
        $response = $resource->send('delete', ['access-grant-requests/' . $id]);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find delete the requests.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByUser(Request $request, Sdk $sdk)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createProfileService();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('statuses')) {
            $resource->addQueryArgument('statuses', $request->input('statuses'));
        }
        $response = $resource->send('get', ['access-requests']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching requests.');
        }
        $json = json_decode($response->getRawResponse(), true);
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteRequestForUser(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createProfileService();
        $response = $resource->send('delete', ['access-requests/' . $id]);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find delete the requests.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }



    public function subscription(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Subscription';
        $this->data['header']['title'] = 'Subscription';
        $this->data['selectedSubMenu'] = 'settings-subscription';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $plans = config('dorcas.plans');
        # get the plans configuration
        $dorcasPlans = $this->getPricingPlans($sdk);
        # get the plans from Dorcas
        $pricingPlans = [];
        # the pricing plans
        foreach ($plans as $name => $plan) {
            /*later on we are going to add selectively,
            - ree option,
            - 1 or more paid and
            - 1 or more belonging to your partner id
            */
            $live = $dorcasPlans->where('name', $name)->first();
            # get the plan
            if (empty($live)) {
                continue;
            }
            $temp = array_merge($plan, ['name' => $name]);
            $temp['profile'] = $live;
            $pricingPlans[] = $temp;
        }
        $this->data['plans'] = collect($pricingPlans)->map(function ($plan) {
            return (object) $plan;
        });
        return view('modules-settings::subscription', $this->data);
    }
    

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscription_switch(Request $request, Sdk $sdk)
    {
        $plans = array_keys(config('dorcas.plans'));
        # the allowed keys
        $vdata = $this->validate($request, [
            'plan' => 'required|string|in:'.implode(',', $plans)
        ]);

        # validate the request
        $company = $request->user()->company(true, true);
        # get the company
        $upgradeQuery = $sdk->createCompanyResource($company->id)->addBodyParam('plan', $request->plan)
                                                                ->send('post', ['update-plan']);
        
        $request->session()->put('dorcas_transaction_purpose', 'subscription');
        $request->session()->put('dorcas_subscription_expiry', $request->expiry_date);
        
        if (!$upgradeQuery->isSuccessful()) {
            $message = $upgradeQuery->getErrors()[0]['title'] ?? 'Failed while trying to update your account plan.';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
        return response()->json($upgradeQuery->getData());
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscription_extend(Request $request, Sdk $sdk)
    {
        $plans = array_keys(config('dorcas.plans'));
        # the allowed keys
        /*$vdata = $this->validate($request, [
            'plan' => 'required|string|in:'.implode(',', $plans),
            'expiry_date'  => 'nullable|date|date_format:Y-m-d',
        ]);*/
        //dd($vdata);
        # validate the request
        $company = $request->user()->company(true, true);
        # get the company
        $upgradeQuery = $sdk->createCompanyResource($company->id)->addBodyParam('plan', $request->plan)
        ->addBodyParam('access_expires_at', $request->expiry_date)
                                                                ->send('post', ['update-plan']);
        if (!$upgradeQuery->isSuccessful()) {
            $message = $upgradeQuery->getErrors()[0]['title'] ?? 'Failed while trying to update your account plan.';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
        return response()->json($upgradeQuery->getData());
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function extend_subs(Request $request, Sdk $sdk, string $extend_details)
    {
            try {
            $company = $request->user()->company(true, true);
            # get the company information
                # update the business information
                $query = $sdk->createCompanyService()
                                ->addBodyParam('extend_details', "")
                                ->send('PUT');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating.');
                }
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed while updating.' . $e->getMessage());
        }

        # next up - we need to update the company information
        return response()->json($query->getData());
    }

    /**
     * @param Request $request
     * @param Sdk $sdk
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function marketplace_settings(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'enabled' => 'required'
        ]);
        # validate the request
        $response = null;
        # our request query
        switch ($request->input('name')) {
            case 'set_professional_status':
                $response = $sdk->createProfileService()->addBodyParam('is_professional', (int) $request->enabled)
                                                        ->send('PUT');
                break;
            case 'set_vendor_status':
                $response = $sdk->createProfileService()->addBodyParam('is_vendor', (int) $request->enabled)
                                                        ->send('PUT');
                break;
            default:
                break;
        }
        if (empty($response)) {
            throw new \RuntimeException('The request could not be sent, please try again.');
        }
        if (!$response->isSuccessful()) {
            throw new \RuntimeException($response->getErrors()[0]['title']);
        }
        return response()->json($response->getData());
    }

}
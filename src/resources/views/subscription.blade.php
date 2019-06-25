@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9" id="subscription-page">
		<div class="row">

		    <div class="row col-md-12 row-cards row-deck" id="subscription-statistics">
		    	<div class="col-sm-6 col-lg-3">
		    		<div class="card p-3">
		    			<div class="d-flex align-items-center">
		    				<span class="stamp stamp-md bg-blue mr-3">
		    					<i class="fe fe-grid"></i>
		    				</span>
		    				<div>
		    					<h4 class="m-0">@{{ business.plan.data.name.title_case() }}</h4>
		    					<small class="text-muted">Service Plan</small>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
		    	<div class="col-sm-6 col-lg-3">
		    		<div class="card p-3">
		    			<div class="d-flex align-items-center">
		    				<span class="stamp stamp-md bg-blue mr-3">
		    					<i class="fe fe-calendar"></i>
		    				</span>
		    				<div>
		    					<h4 class="m-0">@{{ moment(business.access_expires_at, 'DD MMM, YY') }}</h4>
		    					<small class="text-muted">Service Plan Expiry</small>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
		    	<div class="col-sm-6 col-lg-3">
		    		<div class="card p-3">
		    			<div class="d-flex align-items-center">
		    				<span class="stamp stamp-md bg-blue mr-3">
		    					<i class="fa fa-money"></i>
		    				</span>
		    				<div>
		    					<h4 class="m-0">NGN@{{ pricing.formatted }}</h4>
		    					<small class="text-muted">Subscription</small>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
		    	<div class="col-sm-6 col-lg-3">
		    		<div class="card p-3">
		    			<div class="d-flex align-items-center">
		    				<span class="stamp stamp-md bg-blue mr-3">
		    					<i class="fe fe-calendar"></i>
		    				</span>
		    				<div>
		    					<h4 class="m-0">@{{ nextAutoRenew.format('DD MMM, YY') }}</h4>
		    					<small class="text-muted">Renewal Date</small>
		    				</div>
		    			</div>
		    		</div>
		    	</div>
		    </div>

		</div>
		<div class="row">
		    <div class="row row-cards row-deck" id="subscription-plans">
                <plan-chooser v-for="(plan, index) in plans" :key="plan.profile.id"
                              :index="index"
                              :footnote="plan.footnote"
                              :name="plan.name"
                              :features="plan.features"
                              :short_description="plan.description.short"
                              :description="plan.description.long"
                              :profile="plan.profile"></plan-chooser>

            </div>
			
		</div>
	
	</div>

</div>

@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#subscription-page',
            data: {
                user: {!! json_encode($dorcasUser) !!},
                business: {!! json_encode($business) !!},
                plans: {!! json_encode($plans) !!}
            },
            computed: {
                pricing: function () {
                    if (this.business.plan_type === 'yearly') {
                        return this.business.plan.data.price_yearly;
                    }
                    return this.business.plan.data.price_monthly;
                },
                nextAutoRenew: function () {
                    return moment(this.business.access_expires_at).add(1, 'days');
                }
            },
            methods: {
                moment: function (dateString, format) {
                    return moment(dateString).format(format);
                }
            },
            mounted: function() {
            	console.log(this.plans)
            }
        })
    </script>
@endsection

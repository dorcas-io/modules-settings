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
		    					<h4 class="m-0">@{{ moment(business.access_expires_at, 'DD MMM, YYYY') }}</h4>
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
		    					<h4 class="m-0">@{{ nextAutoRenew.format('DD MMM, YYYY') }}</h4>
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
                              :name="plan.config.title"
                              :expiry_date="processExpiry(index)"
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
                },
                /*planName: function (name) {
                	console.log(name)
                	let pp = this.plans.find(pl => pl.name === name)
                	console.log(pp)
                    return pp.config.title;
                }*/
            },
            methods: {
                moment: function (dateString, format) {
                    return moment(dateString).format(format);
                },
                processExpiry: function (plan_index) {
                	let this_plan = typeof this.plans[plan_index] !== 'undefined' ? this.plans[plan_index] : null;
                	if (this_plan===null) {
                		return null;
                	}
                	if (this_plan.name !== 'Starter') {
	                	let this_expiry = typeof this_plan.config.duration !== 'undefined' && this_plan.config.duration !=='free' && this_plan.config.duration !== '' ? this_plan.config.duration : null;
	                	if (this_expiry !== null) {
				            let paths = this_expiry.split("-");
				            let expiry_length = paths[0];
				            let expiry_cycle = paths[1];
				            console.log(expiry_length + ' ' + expiry_cycle)
		                    return moment().add(paths[0], paths[1]).format("Y-M-D");	
	                	} else {
	                		return null
	                	}

	                } else {
	                	return null
	                }

                },
                activateSubscriptionOnPayment() {
                    //open Tab
                    var url = document.location.toString();
                    if (url.match('subscription_successful')) {
                        let activate_plan_index = url.split('__')[1];
                        // console.log(activate_plan_index);
                        let expiry_date = this.processExpiry(activate_plan_index)
                        //console.log(expiry_date)
                        //console.log(this.business.access_expires_at);

                        let this_plan = typeof this.plans[activate_plan_index] !== 'undefined' ? this.plans[activate_plan_index] : null;

                        //only atteempt to process the payment by re-setting expiry date if the server-side expiry date
                        /*if (moment(this.business.access_expires_at) < moment()) {
                        	console.log ('yes')
                        }*/
                        //console.log(this_plan.name)
                        //console.log(expiry_date)
                        if (typeof getCookie("ps_index") !== 'undefined' && getCookie("ps_index") === activate_plan_index) {

                            if (this_plan.name !== 'starter') {
                                swal("Plan Switch", 'Great! Your plan upgrade was successful', "success");
                                //window.location = '{{ url()->current() }}';
                            } else {
                                swal("Plan Switch", 'Great! Your plan downgrade was successful', "success");
                                //window.location = '{{ url()->current() }}';
                            }



		                    /*axios.post("/mse/settings-subscription-switch", {
		                        plan: this_plan.name,
		                        expiry_date: expiry_date
		                    }).then(function (response) {
		                            console.log(response);
		                            if (this_plan.name !== 'starter') {
		                                swal("Plan Switch", 'Great! Your plan upgrade was successful', "success");
		                                window.location = '{{ url()->current() }}';
		                            } else {
		                                swal("Plan Switch", 'Great! Your plan downgrade was successful', "success");
		                                window.location = '{{ url()->current() }}';
		                            }
		                            
		                        })
		                        .catch(function (error) {
		                            var message = '';
		                            if (error.response) {
		                                // The request was made and the server responded with a status code
		                                // that falls out of the range of 2xx
		                                //var e = error.response.data.errors[0];
		                                //message = e.title;
		                                var e = error.response;
		                                message = e.data.message;
		                            } else if (error.request) {
		                                // The request was made but no response was received
		                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
		                                // http.ClientRequest in node.js
		                                message = 'The request was made but no response was received';
		                            } else {
		                                // Something happened in setting up the request that triggered an Error
		                                message = error.message;
		                            }
		                            return swal("Action Failed", message, "warning");
		                        });*/
                        }

                    }
                }, 
            },
            mounted: function() {
            	//console.log(this.plans);
            	//console.log(this.business)
                this.activateSubscriptionOnPayment();
            }
        })

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function checkCookie() {
  var user = getCookie("username");
  if (user != "") {
    alert("Welcome again " + user);
  } else {
    user = prompt("Please enter your name:", "");
    if (user != "" && user != null) {
      setCookie("username", user, 365);
    }
  }
}



    </script>
@endsection

@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')
<div class="row" id="business-profile">

	@include('layouts.blocks.tabler.sub-menu')

	<div class="col-md-9">
		<div class="row">

			<div class="col-md-6">
              <form class="card" action="" method="post">
              	{{ csrf_field() }}
                <div class="card-body">
                  <h3 class="card-title">Business Information</h3>
                                      
                  <div class="row">

                  	<div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="name"  @if ($errors->has('name')) data-error="{{ $errors->first('name') }}" @endif>Business Name</label>
                        <input id="name" type="text" name="name" maxlength="80" v-model="company.name"
                                           required class="form-control {{ $errors->has('name') ? ' invalid' : '' }}" >
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="registration" @if ($errors->has('registration')) data-error="{{ $errors->first('registration') }}" @endif>Registration Number</label>
                        <input id="registration" type="text" name="registration" v-model="company.registration"
                                           maxlength="30" class="form-control {{ $errors->has('registration') ? ' invalid' : '' }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Contact Phone</label>
                        <input id="phone" type="text" name="phone" v-model="company.phone" maxlength="30"
                                           class="form-control {{ $errors->has('phone') ? ' invalid' : '' }}">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="email" class="active"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Email Address</label>
                        <input id="email" type="email" name="email" v-model="company.email" maxlength="80"
                                           class="form-control {{ $errors->has('email') ? ' invalid' : '' }}">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="website" class="active"  @if ($errors->has('website')) data-error="{{ $errors->first('website') }}" @endif>Website</label>
                        <input id="website" type="text" name="website" v-model="company.website" maxlength="80"
                                           class="form-control {{ $errors->has('website') ? ' invalid' : '' }}">
                      </div>
                    </div>                    
                    
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="update_business" class="btn btn-primary">Update Profile</button>
                </div>
              </form>
         
            </div>

        	<div class="col-md-6">
        		<div class="row">
        			<div class="col-lg-12">
                      <form class="card" action="" method="post">
                      	{{ csrf_field() }}
                        <div class="card-body">
                          <h3 class="card-title">Address Information</h3>
                          <div class="row">

                          	<div class="col-md-12">
                              <div class="form-group">
                                <label class="form-label" for="address1"  @if ($errors->has('address1')) data-error="{{ $errors->first('address1') }}" @endif>Address Line 1</label>
                                <input id="address1" type="text" name="address1" v-model="location.address1"
                                                   maxlength="100" required class="form-control {{ $errors->has('address1') ? ' invalid' : '' }}">
                              </div>
                            </div>

                            <div class="col-md-12">
                              <div class="form-group">
                                <label class="form-label" for="address2"  @if ($errors->has('address2')) data-error="{{ $errors->first('address2') }}" @endif>Address Line 2</label>
                                <input id="address2" type="text" name="address2" v-model="location.address2"
                                                   maxlength="100" class="form-control {{ $errors->has('address2') ? ' invalid' : '' }}">
                              </div>
                            </div>
                            
                            <div class="col-sm-6 col-md-6">
                              <div class="form-group">
                                <label class="form-label" for="city"  @if ($errors->has('city')) data-error="{{ $errors->first('city') }}" @endif>City</label>
                                <input id="city" type="text" name="city" maxlength="100" v-model="location.city"
                                                   required class="form-control {{ $errors->has('city') ? ' invalid' : '' }}">
                              </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                              <div class="form-group">
                                <label class="form-label" for="state"  @if ($errors->has('state')) data-error="{{ $errors->first('state') }}" @endif>State</label>
                                <select name="state" id="state" v-model="location.state.data.id"
                                                    class="form-control {{ $errors->has('state') ? ' invalid' : '' }}">
                                                <option v-for="state in states" :value="state.id" :key="state.id">@{{ state.name }}</option>
                                            </select>
                              </div>
                            </div>
                            
                            
                          </div>
                        </div>
                        <div class="card-footer text-right">
                          <button type="submit" name="action" value="update_location" class="btn btn-primary">Update Address</button>
                        </div>
                      </form>
                 
                    </div>
		</div>
	</div>


</div>


@endsection
@section('body_js')

<script type="text/javascript">
    new Vue({
        el: '#business-profile',
        data: {
            company: {!! json_encode($company) !!},
            location: {!! json_encode($location) !!},
            states: {!! json_encode($states) !!}
        }
    })
</script>
    
@endsection

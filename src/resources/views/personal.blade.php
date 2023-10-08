@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')
<div class="row" id="personal-profile">

	@include('layouts.blocks.tabler.sub-menu')

	<div class="col-md-9">
		<div class="row">

			<div class="col-md-12">
              <form class="card" action="" method="post">
              	{{ csrf_field() }}
                <div class="card-body">
                  <h3 class="card-title">Personal Information</h3>
                                      
                  <div class="row">
                    
                    <div class="col-sm-6 col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>First Name</label>
                        <input id="firstname" type="text" name="firstname" maxlength="30" v-model="user.firstname"
                                           required class="form-control {{ $errors->has('firstname') ? ' invalid' : '' }}">
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Last Name</label>
                        <input id="lastname" type="text" name="lastname" maxlength="30" v-model="user.lastname"
                                           required class="form-control {{ $errors->has('lastname') ? ' invalid' : '' }}">
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Account Email</label>
                        <input id="email" type="email" name="email" v-model="user.email" maxlength="80"
                                           required class="form-control {{ $errors->has('email') ? ' invalid' : '' }}">
                      </div>
                    </div>


                    <div class="col-sm-6 col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="phone" class="active" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone</label>
                        <input id="phone" type="text" name="phone" v-model="user.phone" maxlength="30"
                                           class="form-control {{ $errors->has('phone') ? ' invalid' : '' }}">
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-6">
                      <div class="form-group">
                        <label class="form-label" for="gender" @if ($errors->has('gender')) data-error="{{ $errors->first('gender') }}" @endif>Gender</label>
                        <select name="gender" id="gender" v-model="user.gender" class="form-control {{ $errors->has('gender') ? ' invalid' : '' }}">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                    </select>
                      </div>
                    </div>

                    
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="update_business" class="btn btn-primary">Update Profile</button>
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
            el: '#personal-profile',
            data: {
                user: {!! json_encode($dorcasUser) !!}
            }
        })
    </script>
    
@endsection

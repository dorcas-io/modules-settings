@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')
<div class="row" id="personal-profile">

	@include('layouts.blocks.tabler.sub-menu')

	<div class="col-md-7" >
		<div class="row">

			<div class="col-lg-10">
              <form class="card" action="" method="post">
              	{{ csrf_field() }}
                <div class="card-body">
                  <h3 class="card-title">Change Password</h3>
                                      
                  <div class="row">
                                        
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="password"  @if ($errors->has('password')) data-error="{{ $errors->first('password') }}" @endif>New Password</label>
                        <input id="password" type="password" name="password"
                                           required class="form-control {{ $errors->has('password') ? ' invalid' : '' }}">
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="password_confirmation"  @if ($errors->has('password_confirmation')) data-error="{{ $errors->first('password_confirmation') }}" @endif>Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                                           required class="form-control {{ $errors->has('password_confirmation') ? ' invalid' : '' }}">
                                    
                      </div>
                    </div>




                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="update_password" class="btn btn-primary">Change Password</button>
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

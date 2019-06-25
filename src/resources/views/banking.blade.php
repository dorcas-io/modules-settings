@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')
<div class="row" id="bank-setup">

	@include('layouts.blocks.tabler.sub-menu')

	<div class="col-md-9">
		<div class="row">

			<div class="col-md-12">
              <form class="card" action="" method="post">
              	{{ csrf_field() }}
                <div class="card-body">
                  <h3 class="card-title">Bank Account Information</h3>
                                      
                  <div class="row">
                                        
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="bank" @if ($errors->has('bank')) data-error="{{ $errors->first('bank') }}" @endif>Bank</label>
                        <select class="form-control" name="bank" id="bank" v-model="account.json_data.bank_code" required>
                                        <option v-for="bank in banks" :key="bank.code" v-bind:value="bank.code">@{{ bank.name }}</option>
                                    </select>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="account_number"  @if ($errors->has('account_number')) data-error="{{ $errors->first('account_number') }}" @endif>Account Number</label>
                        <input id="account_number" type="text" name="account_number" maxlength="30" v-model="account.account_number"
                                           required class="form-control {{ $errors->has('account_number') ? ' invalid' : '' }}">
                                    
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="account_name"  @if ($errors->has('account_name')) data-error="{{ $errors->first('account_name') }}" @endif>Account Name</label>
                        <input id="account_name" type="text" name="account_name" v-model="account.account_name" maxlength="80"
                                           required class="form-control {{ $errors->has('account_name') ? ' invalid' : '' }}">
                                    
                      </div>
                    </div>





                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="update_password" class="btn btn-primary">Save Account</button>
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
            el: '#bank-setup',
            data: {
                account: {!! json_encode(!empty($account) ? $account : $default) !!},
                banks: {!! json_encode($banks) !!},
                user: {!! json_encode($dorcasUser) !!}
            }
        })
    </script>
    
@endsection

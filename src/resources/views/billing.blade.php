@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')


<div class="row" id="billing-settings">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9">

    <div class="row">

      <div class="col-md-6">
              <form class="card" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">
                  <h5>Billing Settings</h5>
                                      
                  <div class="row">
                                        
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="auto_billing" @if ($errors->has('auto_billing')) data-error="{{ $errors->first('auto_billing') }}" @endif>Would you like to be billed automatically during renewals?</label>
                        <select name="auto_billing" id="auto_billing" v-model="billing.auto_billing"
                                        class="form-control custom-select {{ $errors->has('auto_billing') ? ' invalid' : '' }}">
                                    <option value="0">Turned Off</option>
                                    <option value="1">Turned On (Recommended)</option>
                                </select>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="save_billing" class="btn btn-primary btn-block">Save Preference</button>
                </div>
              </form>
         
            </div>

                    <div class="col-md-6">
                        <form class="card" method="post" action="{{ route('settings-billing-coupon') }}">
                <div class="card-body">
                  <h5>Redeem Coupon</h5>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input name="coupon" id="coupon" type="text" maxlength="30" class="form-control">
                                    <label for="coupon" class="form-label">Coupon Code</label>
                                </div>
                            </div>
                          </div>
                            <div class="card-footer text-right">
                                    <button type="submit" name="redeem_coupon" value="1" class="btn btn-primary btn-block">Redeem Upgrade Coupon</button>
                                </div>
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
        el: '#billing-settings',
        data: {
            billing: {!! json_encode($billing) !!}
        }
    })
</script> 
@endsection

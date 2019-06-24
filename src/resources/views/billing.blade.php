@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')


<div class="row" id="billing-settings">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-7" >

    <div class="row">

      <div class="col-lg-10">
              <form class="card" action="" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="card-body">
                  <h5>Billing Settings</h5>
                                      
                  <div class="row">
                                        
                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label" for="auto_billing" @if ($errors->has('auto_billing')) data-error="{{ $errors->first('auto_billing') }}" @endif>Would you like to be billed automatically?</label>
                        <select name="auto_billing" id="auto_billing" v-model="billing.auto_billing"
                                        class="form-control custom-select {{ $errors->has('auto_billing') ? ' invalid' : '' }}">
                                    <option value="0">Turned Off</option>
                                    <option value="1">Turned On</option>
                                </select>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="save_billing" class="btn btn-primary">Save Preference</button>
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

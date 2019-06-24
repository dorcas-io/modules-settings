@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')


<div class="row" id="business-profile">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-7" >

		<div class="row">

			<div class="col-lg-10">
              <form class="card" action="" method="post" enctype="multipart/form-data">
              	{{ csrf_field() }}
                <div class="card-body">
                  <h5>Branding</h5>
	                @if (!empty($company->logo))
	                    <img src="{{ $company->logo }}" alt="img12" class="responsive-img mb-4">
	                @endif
                                      
                  <div class="row">
                                        
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="form-label">Business Logo</div>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" name="logo" accept="image/*">
                          <label class="custom-file-label">Select Business Logo</label>
                          <small>We recommend a <strong>126x100</strong> logo, or similar</small>
                        </div>
                      </div>
                    </div>





                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" name="action" value="customise_logo" class="btn btn-primary">Update Logo</button>
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
                business: {!! json_encode($business) !!}
            }
        })
    </script>
    
@endsection

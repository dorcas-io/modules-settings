@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')


<div class="row" id="customization-profile">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9">

		<div class="row">

			<div class="col-md-12">
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
                          <input type="file" ref="logo" id="logo" class="custom-file-input" v-on:change="fileCheck('file_label')" name="logo" accept="image/*">
                          <label id="file_label" class="custom-file-label">Select Business Logo</label>
                          <small id="file_check">We recommend a <strong>126x100</strong> logo, or similar.  Maximum size 100KB</small>
                        </div>
                      </div>
                    </div>





                  </div>
                </div>
                <div class="card-footer text-right">
                  <button id="file_submit" type="submit" name="action" value="customise_logo" class="btn btn-primary">Update Logo</button>
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
            el: '#customization-profile',
            data: {
                business: {!! json_encode($business) !!},
                fileUpload: { file : '' }
            },
            methods: {
            fileCheck: function(label_id) {
                this.fileUpload.file = this.$refs.logo.files[0];
                //console.log(this.fileUpload.file)
                $("#file_label").html(this.fileUpload.file.name);
                if (this.fileUpload.file.size > (1024 * 100)) {
                    $("#file_submit").attr('disabled', true );
                    $("#file_check").html('Selected file size > 100KB. Choose another');
                    $("#file_check").css('color', 'red');
                } else {
                    $("#file_submit").attr('disabled', false );
                    $("#file_check").html('Selected file OK');
                    $("#file_check").css('color', 'green');
                }
            },
            }
        })
    </script>
    
@endsection

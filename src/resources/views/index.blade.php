@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9">
		<div class="row">

			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-green"></div>
					<div class="card-header">
						<h3 class="card-title">Security Settings</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage settings such as <strong>Login Passwords</strong>
					</div>
				</div>
			</div>

			@if (empty($viewMode) || $viewMode === 'business')
			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-orange"></div>
					<div class="card-header">
						<h3 class="card-title">Subscriptions &amp; Billing</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage settings such as <strong>Subscription  and Downgrades</strong>
					</div>
				</div>
			</div>
			@endif

			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-red"></div>
					<div class="card-header">
						<h3 class="card-title">Personal Settings</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage Personal settings such as <strong>Bio Profile</strong> &amp; <strong>Contact Information</strong>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-yellow"></div>
					<div class="card-header">
						<h3 class="card-title">Business Settings</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage Company settings such as <strong>Name</strong>, <strong>Address</strong>, <strong>Location</strong> &amp; <strong>Contact Information</strong>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-teal"></div>
					<div class="card-header">
						<h3 class="card-title">Customization Settings</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage settings such as <strong>Brand</strong> &amp; <strong>Logos</strong>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-purple"></div>
					<div class="card-header">
						<h3 class="card-title">Access Grants</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage settings such as <strong>Access Requests</strong> from Professionals
					</div>
				</div>
			</div>

			<div class="col-md-6 col-xl-4">
				<div class="card">
					<div class="card-status bg-teal"></div>
					<div class="card-header">
						<h3 class="card-title">Banking Settings</h3>
						<div class="card-options">
							<a href="#" class="btn btn-primary btn-sm">Edit</a>
						</div>
					</div>
					<div class="card-body">
						Manage settings such as <strong>Bank Name</strong> &amp; <strong>Account Number</strong>
					</div>
				</div>
			</div>
			
		</div>
	
	</div>

</div>

@endsection
@section('body_js')
    
@endsection

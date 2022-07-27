@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Soy un inquilino!!!</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Vista de inquilinos!!!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

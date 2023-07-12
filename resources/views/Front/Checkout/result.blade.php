@extends('Front.layout.master')

@section('title', 'Result')

@section('body')

    <!--Shopping Cart Section Begin-->
    <div class="checkout-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h4>
                        {{ $notification }}
                    </h4>

                    <a href="./" class="primary-btn mt-5">Continue shopping</a>
                </div>
            </div>
        </div>
    </div>
@endsection

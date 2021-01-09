@extends('layouts.master')
@section('content')
    @php
        $currency=localesymbol($code??'USD')
    @endphp
    
    {{--
    @component('partials.widgets.breadcrumb')
        <li class="breadcrumb-item">
            <a href="{{ URL::to('/') }}">{{trans('icommerce::common.home.title')}}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            {{trans('icommerce::orders.breadcrumb.title')}}
        </li>
    @endcomponent
    --}}

    <div id="content_index_commerce" class="icommerce-page page mt-3">
        <x-isite::breadcrumb>
            <li class="breadcrumb-item active" aria-current="page">{{trans('icommerce::orders.breadcrumb.title')}}</li>
        </x-isite::breadcrumb>
       
        <div class="container container-page pt-5">
            
           
            <div class="cart-content" v-show="items.length > 0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class=" bg-secondary text-white">
                        <tr>
                            <th>{{trans('icommerce::orders.table.id')}}</th>
                            <th>{{trans('icommerce::orders.table.email')}}</th>
                            <th>{{trans('icommerce::orders.table.total')}}</th>
                            <th>{{trans('icommerce::orders.table.status')}}</th>
                            <th>{{trans('core::core.table.created at')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr class='clickable-row cursor-pointer' data-href="{{ url('/orders').'/'.$order->id }}">
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->email }}</td>
                            <td>{{$currency->symbol_left}} {{formatMoney($order->total) }}{{$currency->symbol_right}} </td>
                            <td>{{$order->status->title}}</td>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!--End of Shopping cart items-->
                <hr class="my-4 hr-lg">
                <div class="cart-content-footer">
                    <div class="row">
                        {{$orders->links()}}
                    </div>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-8 text-right mt-3 mt-md-0">
                            <div class="cart-content-totals"> </div>
                            <!-- Proceed to checkout -->
                            <a href="{{ url('/account') }}" class="btn btn-outline-primary btn-rounded btn-lg my-2">
                                {{trans('icommerce::orders.button.Back_to_profile')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Extra Footer End Page --}}
        @include('icommerce::frontend.partials.extra-footer')

    </div>

@stop
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $("table .clickable-row").click(function() {
                window.location = $(this).data("href");
            });
        });
    </script>
@stop

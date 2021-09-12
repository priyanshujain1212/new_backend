@extends('frontend.layouts.default')
@section('frontend.content')
<article class="card mb-3">
    <div class="card-body">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status')}}
            </div>
        @endif
            <div>
                @if(!blank($transactions))
                    <table class="table">
                        <thead style="background-color:#3167eb">
                        <tr style="color:white">
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                        </thead>
                        @php $i = 0 @endphp
                        @foreach ($transactions as $transaction)
                            @php $i++ @endphp
                            <tbody>
                            <tr>
                                <td>{{ $i }}</td>
                                <td> {{ trans('transaction_types.'.$transaction->type) }}</td>
                                <td>{{ food_date_format_with_day($transaction->created_at) }}</td>
                                <td>{{ transactionCurrencyFormat($transaction) }}</td>
                            </tr>
                            </tbody>
                        @endforeach
                    </table>
                @else
                    <h5 class="mb-0">{{ __('You doesn\'t have any transaction yet.') }}</h5>
                @endif
            </div>
    </div> <!-- card-body .// -->
</article> <!-- card.// -->
@endsection

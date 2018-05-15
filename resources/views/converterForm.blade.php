@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <p>You are now logged in</p>
        <div class="col-md-6 col-md-offset-3">
            <div class="card card-outline-secondary">
                <div class="card-header">
                    <h3 class="mb-0">Convert</h3>
                </div>
                <div class="card-body" style="background-color:#00008b; color:#eee;">
                    <form class="form" role="form" autocomplete="off" id="convertorForm" novalidate="">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="baseCurrency">Currency I have</label>
                                <select class="form-control" name="baseCurrency" id="baseCurrency">
                                    <option value="">Select currency</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}" {{  (!empty($baseCurrency) && $baseCurrency === $currency)?'selected':''}}>{{$currency}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="form-group col-md-6">
                                <label for="targetCurrency">Currency I want</label>
                                <select class="form-control" name="targetCurrency" id="targetCurrency">
                                    <option value="">Select currency</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}" {{  (!empty($targetCurrency) && $targetCurrency === $currency)?'selected':''}}>{{$currency}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="amount">Amount I have</label>
                                <input name="amount" type="text" value="{{ (!empty($amount))?$amount:''  }}" class="form-control" id="amount" placeholder="Amount">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount2">You get</label>
                                <input type="text" class="form-control" id="amount2" value="{{ ($amountResulted)??''  }}" placeholder="You get">
                            </div>
                        </div>
                        <button type="submit" name="submit" value="submit" class="btn btn-primary btn-submit">Convert</button>
                        <a type="button" name="button"  class="btn btn-primary" href="{{ route('home') }}">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

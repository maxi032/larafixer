<?php

namespace App\Http\Controllers;

use Exchanger\ExchangeRateQuery;
use Exchanger\CurrencyPair;
use App\Rate;
use Illuminate\Http\Request;
use Exchanger\Exception\ChainException;
use Exchanger\Exception\Exception as RateException;

class HomeController extends Controller
{
    protected $app;
    protected $service;
    protected $allowedPairs;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->app = app();
        $this->service = $this->app['swap.chain']; // the fixer.io service

        $this->allowedPairs = config('app.allowed_currency_pairs');
    }

    /**
     * Show the form.
     * @param Request $request
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        // get all available currencies for dropdowns
        $currencies = Rate::whereStatus(1)->take(100)->pluck('currency');
        return view('converterForm',['currencies'=>$currencies]);
    }

    /**
     * Perform conversion
     * @param int $amount A number
     * @param string $from String representing base currency. Defaults to GBP/USD
     * @param string $to String representiung target currency
     *
     * @return double result Converted amount
     * */
    private function doConversion($amount=1,$from = "GBP",$to = "USD") {
        $exchangeRate = Rate::whereStatus(1)->whereCurrency($from)->orWhere('currency',$to)->take(100)->get();

        //arrange collection by preffered key to be able to get the corresponding currency
        $exchangeRate = $exchangeRate->keyBy('currency')->all();

        return number_format($amount/$exchangeRate[$from]->rate * $exchangeRate[$to]->rate, 2);
    }

    /**
     * Check if a pair is valid as not all pairs are supported on the free plan
     * @param string $pair
     * @return boolean
     */
    public function isPairValid(string $pair){
        $service = $this->service;
        return  $service->supportQuery(new ExchangeRateQuery(CurrencyPair::createFromString($pair)));
    }

    /**
     * Insert rates in database so that we will not have to interogate API all the time
     * It does not work as it should, due to the fact that fixer is banning the API calls for free accounts when trying to get a rate with Swap::latest('GBP/USD')
     * 'The chain resulted in 1 exception(s): Exchanger\Exception\Exception: The current subscription plan does not support this API endpoint.'
     */

    public function seedRates() {
        $cnt = 0;
        foreach($this->allowedPairs as $k => $v) {
            $vToInsert = substr($v, 4); // replace base currency and insert only the target currency
            if($this->isPairValid($v)) { // not all rates are allowed for free accounts
                try {
                    $rate = \Swap::latest($v);
                    \DB::table('rates')->insert(
                        ['currency' => $vToInsert, 'rate' => $rate->getValue(), 'status'=>1]
                    );
                    $cnt++;
                    if(!$rate) {
                        throw new RateException;
                    }
                } catch (RateException $e) {
                    echo "<br>".$e->getMessage()." for ".$v;
                }
            }
        }
        echo "<br> ".$cnt." currencies inserted";
    }

    /**
     * Process the submitted form
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     * */
    public function processForm(Request $request){
        $baseCurrency = $request->input('baseCurrency');
        $targetCurrency = $request->input('targetCurrency');
        $amount = $request->input('amount');

        // get all available currencies for dropdowns
        $currencies = Rate::whereStatus(1)->take(100)->pluck('currency');
        $amountResulted = $this->doConversion($amount, $baseCurrency, $targetCurrency);

        return view('converterForm',
            ['currencies'=>$currencies,
             'baseCurrency'=>$baseCurrency,
             'targetCurrency'=>$targetCurrency,
             'amount'=>$amount,
             'amountResulted'=>$amountResulted
            ]);
    }

}

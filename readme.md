## Description

I've used the following package: <code>florianv/laravel-swap</code> as a fixer.io wrapper on a Laravel 5.5
So first do a <pre>$ composer require florianv/laravel-swap php-http/message php-http/guzzle6-adapter</pre>

More details: * <a href="https://github.com/florianv/laravel-swap" target="_blank">here</a>

You will also need to create an account on fixer.io and add the api key on swap.php config file. 

Next generate auth scafolding with <pre>php artisan make:auth</pre>
Migrate db and seed: <pre>php artisan migrate --seed</pre>

Login with the seeded user.
Access <code>/seed-rates</code> from your browser to populate a few rates from fixer.io (Please note that only a few of them are available on the free plan)

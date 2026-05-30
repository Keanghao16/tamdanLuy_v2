<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $globalAccounts = Account::where('user_id', Auth::id())->get();
                $activeAccountId = session('active_account_id');
                // Set the default if none selected and accounts exist
                if (!$activeAccountId && $globalAccounts->count() > 0) {
                    $activeAccountId = $globalAccounts->first()->id;
                    session(['active_account_id' => $activeAccountId]);
                }
                $view->with(compact('globalAccounts', 'activeAccountId'));
            }
        });
    }
}

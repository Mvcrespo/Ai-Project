<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;


    public function edit()
    {
        $this->authorize('view', Auth::user());

        $config = Cache::remember('config', 60, function () {
            return DB::table('configuration')->first();
        });

        return response()->json($config);
    }


    public function update(Request $request)
    {
        $configuration = Configuration::first();

        $validated = $request->validate([
            'ticket_price' => 'required|numeric',
            'registered_customer_ticket_discount' => 'required|numeric',
        ]);

        $configuration->update($validated);

        return redirect()->back()->with('alert-type', 'success')->with('alert-msg', 'Configuration updated successfully.');
    }


}

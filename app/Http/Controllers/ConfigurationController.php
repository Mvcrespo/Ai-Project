<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ConfigurationFormRequest;

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


    public function update(ConfigurationFormRequest $request)
    {
        $configuration = Configuration::first();

        $configuration->update($request->validated());

        return redirect()->back()->with('alert-type', 'success')->with('alert-msg', 'Configuration updated successfully.');
    }


}

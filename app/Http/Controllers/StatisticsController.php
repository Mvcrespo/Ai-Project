<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StatisticsController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function show()
    {
        $this->authorize('view', Auth::user());

        $configuration = Configuration::first();

        return view('statistics.show', compact('configuration'));
    }


    public function salesByYear()
    {
        $this->authorize('view', Auth::user());

        $sales = Cache::remember('sales_by_year', 60, function () {
            return DB::table('purchases')
                ->selectRaw('YEAR(purchases.created_at) AS year, COUNT(tickets.id) AS total_tickets, SUM(purchases.total_price) AS total_revenue')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->whereNotNull('purchases.created_at')
                ->groupByRaw('YEAR(purchases.created_at)')
                ->orderByRaw('YEAR(purchases.created_at)')
                ->get();
        });

        return response()->json($sales);
    }

    public function overallStats()
    {
        $this->authorize('view', Auth::user());

        $stats = Cache::remember('overall_stats', 60, function () {
            return DB::table('purchases')
                ->selectRaw('COUNT(tickets.id) AS total_tickets, SUM(purchases.total_price) AS total_revenue, AVG(purchases.total_price) AS average_revenue')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->first();
        });

        return response()->json($stats);
    }

    public function topMoviesLastYear()
    {
        $this->authorize('view', Auth::user());

        $lastYear = Carbon::now()->subYear()->year;

        $movies = Cache::remember('top_movies_last_year', 60, function () use ($lastYear) {
            return DB::table('purchases')
                ->selectRaw('movies.title AS movie_title, COUNT(tickets.id) AS total_tickets')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->leftJoin('screenings', 'tickets.screening_id', '=', 'screenings.id')
                ->leftJoin('movies', 'screenings.movie_id', '=', 'movies.id')
                ->whereYear('purchases.created_at', $lastYear)
                ->groupBy('movies.title')
                ->orderByDesc('total_tickets')
                ->limit(5)
                ->get();
        });

        return response()->json($movies);
    }

    public function topMoviesThisYear()
    {
        $this->authorize('view', Auth::user());

        $currentYear = Carbon::now()->year;

        $movies = Cache::remember('top_movies_this_year', 60, function () use ($currentYear) {
            return DB::table('purchases')
                ->selectRaw('movies.title AS movie_title, COUNT(tickets.id) AS total_tickets')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->leftJoin('screenings', 'tickets.screening_id', '=', 'screenings.id')
                ->leftJoin('movies', 'screenings.movie_id', '=', 'movies.id')
                ->whereYear('purchases.created_at', $currentYear)
                ->groupBy('movies.title')
                ->orderByDesc('total_tickets')
                ->limit(5)
                ->get();
        });

        return response()->json($movies);
    }

    public function topGenres()
    {
        $this->authorize('view', Auth::user());

        $genres = Cache::remember('top_genres', 60, function () {
            return DB::table('purchases')
                ->selectRaw('genres.name AS genre, COUNT(tickets.id) AS total_tickets')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->leftJoin('screenings', 'tickets.screening_id', '=', 'screenings.id')
                ->leftJoin('movies', 'screenings.movie_id', '=', 'movies.id')
                ->leftJoin('genres', 'movies.genre_code', '=', 'genres.code')
                ->whereNotNull('purchases.created_at')
                ->groupBy('genres.name')
                ->orderByDesc('total_tickets')
                ->limit(5)
                ->get();
        });

        return response()->json($genres);
    }

    public function topTheaters()
    {
        $this->authorize('view', Auth::user());

        $theaters = Cache::remember('top_theaters', 60, function () {
            return DB::table('purchases')
                ->selectRaw('theaters.name AS theater, COUNT(tickets.id) AS total_tickets')
                ->leftJoin('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                ->leftJoin('screenings', 'tickets.screening_id', '=', 'screenings.id')
                ->leftJoin('theaters', 'screenings.theater_id', '=', 'theaters.id')
                ->groupBy('theaters.name')
                ->orderByDesc('total_tickets')
                ->limit(5)
                ->get();
        });

        return response()->json($theaters);
    }
}

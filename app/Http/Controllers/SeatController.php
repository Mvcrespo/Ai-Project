<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Theater;
use App\Models\Screening;
use Illuminate\Http\Request;
use App\Models\Ticket;

use Illuminate\Support\Facades\Log;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Exemplo de como listar todos os assentos
        $seats = Seat::all();
        return view('seats.index', compact('seats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater, Screening $screening)
    {
        $seats = Seat::where('theater_id', $theater->id)
            ->with(['tickets' => function ($query) use ($screening) {
                $query->where('screening_id', $screening->id);
            }])
            ->get();

        $movieTitle = $screening->movie->title; // Assumindo que a relação está configurada corretamente

        return view('seats.show', compact('theater', 'seats', 'screening', 'movieTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seat $seat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seat $seat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        //
    }

    /**
     * Get ticket details for a specific seat.
     */
    public function ticketDetails(Request $request, $seatId)
    {
        $screeningId = $request->query('screening_id');

        // Executar a consulta manualmente para depuração
        $query = Ticket::where('seat_id', $seatId)
                       ->where('screening_id', $screeningId)
                       ->where('status', 'valid');

        $ticket = $query->first();

        if ($ticket) {
            return response()->json([
                'id' => $ticket->id,
                'seat_id' => $ticket->seat_id,
                'price' => $ticket->price,
                'status' => $ticket->status,
                'purchase_id' => $ticket->purchase_id,
            ]);
        } else {
            return response()->json([
                'error' => 'No ticket available for this seat',
                'seat_id' => $seatId,
                'screening_id' => $screeningId,
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ], 404);
        }
    }



}

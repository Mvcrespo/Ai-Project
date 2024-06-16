<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Theater;
use App\Models\Screening;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Configuration;
use Illuminate\Support\Facades\Log;

class SeatController extends Controller
{

    public function index()
    {
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

        $occupiedSeats = Ticket::where('screening_id', $screening->id)->pluck('seat_id');
        $configuration = Configuration::first();
        $ticketPrice = $configuration->ticket_price;

        return view('seats.show', compact('theater', 'seats', 'screening', 'occupiedSeats', 'ticketPrice'));
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
        $configuration = Configuration::first();
        $ticketPrice = $configuration ? $configuration->ticket_price : 0;

        $ticket = Ticket::where('seat_id', $seatId)
                       ->where('screening_id', $screeningId)
                       ->where('status', 'valid')
                       ->whereNull('purchase_id')
                       ->first();

        if ($ticket) {

            return response()->json([
                'id' => $ticket->id,
                'seat_id' => $ticket->seat_id,
                'price' => $ticketPrice,
                'status' => $ticket->status === 'valid' && $ticket->purchase_id === null,
                'purchase_id' => $ticket->purchase_id,
            ]);
        } else {
            return response()->json([
                'id' => null,
                'seat_id' => $seatId,
                'price' => $ticketPrice,
                'status' => true,
                'purchase_id' => null,
            ]);
        }
    }


    public function checkAvailability($screeningId)
    {
        $occupiedSeats = Ticket::where('screening_id', $screeningId)->pluck('seat_id');
        $availableSeats = Seat::where('theater_id', function($query) use ($screeningId) {
            $query->select('theater_id')->from('screenings')->where('id', $screeningId);
        })->whereNotIn('id', $occupiedSeats)->get();

        return response()->json($availableSeats);
    }
}

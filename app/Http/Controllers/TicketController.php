<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class TicketController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Ticket::class, 'ticket');
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['screening.movie', 'screening.theater', 'seat', 'purchase.customer']);

        $this->authorize('view', $ticket);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        //
    }

    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    public function destroy(Ticket $ticket)
    {
        //
    }

    public function download(Ticket $ticket)
    {
        $this->authorize('download', $ticket);

        $purchase = $ticket->purchase;

        $pdf = PDF::loadView('tickets.pdf', compact('ticket', 'purchase'));
        return $pdf->download('ticket_' . $ticket->id . '.pdf');
    }

    public function validateByQrCode(Request $request, $qrcode_url)
    {
        $ticket = Ticket::where('qrcode_url', 'like', '%' . $qrcode_url)->first();

        if (!$ticket) {
            return redirect()->route('movies.high')->with('alert-type', 'error')->with('alert-msg', 'Invalid ticket.');
        }

        $this->authorize('validateByQrCode', $ticket);

        $screening = $ticket->screening;

        if ($request->isMethod('post')) {
            if ($request->action == 'validate') {
                $ticket->update(['status' => 'invalid']);
                Session::flash('alert-type', 'success');
                Session::flash('alert-msg', 'Ticket validated successfully.');
            } elseif ($request->action == 'invalidate') {
                Session::flash('alert-type', 'success');
                Session::flash('alert-msg', 'Ticket invalidated successfully.');
            }

            return redirect()->route('session.control');
        } else {
            if ($ticket->status != 'valid') {
                Session::flash('alert-type', 'error');
                Session::flash('alert-msg', 'This ticket is invalid and cannot be used.');
            } else {
                Session::flash('alert-type', 'success');
                Session::flash('alert-msg', 'Ticket is valid.');
            }
        }

        return view('tickets.show', compact('ticket', 'screening'))->with('isValidation', true)->with('isValidationEmploy', true);
    }




}

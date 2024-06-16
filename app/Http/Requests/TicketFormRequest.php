<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use App\Models\Screening;

class TicketFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('validateTicket', Screening::class);
    }

    public function rules(): array
    {
        return [
            'screening_id' => 'required|exists:screenings,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'qrcode_url' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            'screening_id.required' => 'The screening ID is required.',
            'screening_id.exists' => 'The screening ID must exist in the database.',
            'ticket_id.exists' => 'The ticket ID must exist in the database.',
            'qrcode_url.url' => 'The QR code URL must be a valid URL.',
        ];
    }

    public function getTicket()
    {
        $screening = Screening::find($this->screening_id);

        $ticket = null;
        if ($this->ticket_id) {
            $ticket = Ticket::find($this->ticket_id);
        } elseif ($this->qrcode_url) {
            $ticket = Ticket::where('qrcode_url', $this->qrcode_url)->first();
        }

        if (!$ticket || $ticket->screening_id != $screening->id || $ticket->status != 'valid') {
            return null;
        }

        return $ticket;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Screening;

class ScreeningFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'movie_id' => 'required|exists:movies,id',
            'theater_id' => 'required|exists:theaters,id',
        ];

        if ($this->isMethod('post')) {
            $rules['screenings.*.date'] = 'required|date|before_or_equal:2025-12-31';
            $rules['screenings.*.start_time'] = 'required|date_format:H:i';
            $rules['screenings.*'] = function ($attribute, $value, $fail) {
                $exists = Screening::where('movie_id', $this->movie_id)
                    ->where('theater_id', $this->theater_id)
                    ->where('date', $value['date'])
                    ->where('start_time', $value['start_time'])
                    ->exists();

                if ($exists) {
                    $fail('A screening with the same movie, theater, date, and start time already exists.');
                }
            };
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            $screeningIds = $this->input('modified_ids', '');
            if ($screeningIds) {
                $screeningIds = explode(',', $screeningIds);
                foreach ($screeningIds as $id) {
                    $screening = Screening::find($id);
                    if ($screening) {
                        if ($this->input("screenings.$id.date") !== $screening->date) {
                            $rules["screenings.$id.date"] = 'required|date|before_or_equal:2025-12-31';
                        }
                        if ($this->input("screenings.$id.start_time") !== $screening->start_time) {
                            $rules["screenings.$id.start_time"] = 'required|date_format:H:i';
                        }
                        $rules["screenings.$id"] = function ($attribute, $value, $fail) use ($id) {
                            $exists = Screening::where('movie_id', $this->movie_id)
                                ->where('theater_id', $this->theater_id)
                                ->where('date', $value['date'])
                                ->where('start_time', $value['start_time'])
                                ->where('id', '!=', $id)
                                ->exists();

                            if ($exists) {
                                $fail('A screening with the same movie, theater, date, and start time already exists.');
                            }
                        };
                    }
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'screenings.*.date.required' => 'The date for screening is required.',
            'screenings.*.date.date' => 'The date for screening must be a valid date.',
            'screenings.*.date.before_or_equal' => 'The date for screening must be on or before 2025-12-31.',
            'screenings.*.start_time.required' => 'The start time for screening is required.',
            'screenings.*.start_time.date_format' => 'The start time for screening must be in the format H:i.',
            'screenings.*' => 'A screening with the same movie, theater, date, and start time already exists.',
        ];
    }
}


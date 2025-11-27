<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_number' => ['required', 'string', 'max:255'],
            'beds' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'room_type' => ['required', 'string', 'in:Standard,Deluxe,Suite,Executive'],
            'bed_type' => ['required', 'string', 'in:Single,Double,Queen,King,Twin'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}

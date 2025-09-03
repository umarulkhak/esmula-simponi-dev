<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagihanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'biaya_id'            => 'required|array',
            'biaya_id.*'          => 'exists:biayas,id',
            'angkatan'            => 'nullable|numeric',
            'kelas'               => 'nullable|string|in:VII,VIII,IX',
            'tanggal_tagihan'     => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_tagihan',
            'keterangan'          => 'nullable|string|max:255',
        ];
    }

}

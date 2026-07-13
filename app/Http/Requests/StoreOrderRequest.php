<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_name' => 'required|string|max:255',
            'contact_number' => 'required|numeric|digits:10',
            'table_id' => 'nullable|exists:tables,id',
            'append_to_order_id' => 'nullable|exists:orders,id',
            'special_instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.modifiers' => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'customer name',
            'contact_number' => 'contact number',
            'items' => 'ordered items',
            'items.*.food_item_id' => 'food item',
            'items.*.quantity' => 'quantity',
        ];
    }
}

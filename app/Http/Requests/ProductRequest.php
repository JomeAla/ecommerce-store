<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id') ?? $this->route('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'category' => ['nullable', 'string'],
            'description' => ['nullable'],
            'image' => ['nullable'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a valid text.',
            'name.max' => 'The product name cannot exceed 255 characters.',
            'slug.string' => 'The slug must be a valid text.',
            'slug.max' => 'The slug cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken by another product.',
            'price.required' => 'Please enter the product price.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be less than 0.',
            'sale_price.numeric' => 'The sale price must be a valid number.',
            'sale_price.min' => 'The sale price cannot be less than 0.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'category.string' => 'The category must be a valid text.',
            'is_active.boolean' => 'Invalid value for active status.',
            'is_featured.boolean' => 'Invalid value for featured status.',
        ];
    }
}
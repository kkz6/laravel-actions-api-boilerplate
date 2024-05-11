<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ModelRequest extends FormRequest
{
    /**
     * The model the request should be using
     *
     * @var class-string
     */
    public string $model;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->authorizeModel($this->model);
    }
}

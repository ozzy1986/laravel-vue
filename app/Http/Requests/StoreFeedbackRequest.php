<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Feedback\Data\FeedbackData;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'message' => 'message',
        ];
    }

    public function toData(): FeedbackData
    {
        /** @var array{name: string, message: string} $validated */
        $validated = $this->validated();

        return FeedbackData::fromArray($validated);
    }
}

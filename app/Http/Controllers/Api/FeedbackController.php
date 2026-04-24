<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Services\Feedback\FeedbackSaverFactory;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends Controller
{
    public function __construct(private readonly FeedbackSaverFactory $saver) {}

    public function store(StoreFeedbackRequest $request): JsonResponse
    {
        $data = $request->toData();

        $saved = $this->saver->save($data);

        if (! $saved) {
            return response()->json([
                'message' => 'Unable to save feedback at the moment. Please try again later.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json([
            'data' => [
                'name' => $data->name,
                'message' => $data->message,
                'driver' => $this->saver->driver(),
                'accepted_at' => now()->toIso8601String(),
            ],
        ], Response::HTTP_CREATED);
    }
}

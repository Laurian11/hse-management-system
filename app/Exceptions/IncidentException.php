<?php

namespace App\Exceptions;

use Exception;

class IncidentException extends Exception
{
    /**
     * Create a new incident exception instance.
     */
    public function __construct(string $message = 'Incident operation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        \Log::error('Incident Exception: ' . $this->getMessage(), [
            'code' => $this->getCode(),
            'trace' => $this->getTraceAsString(),
        ]);

        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Incident Error',
                'message' => $this->getMessage(),
            ], 400);
        }

        return redirect()->back()
            ->with('error', $this->getMessage());
    }
}


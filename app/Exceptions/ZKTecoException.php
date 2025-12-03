<?php

namespace App\Exceptions;

use Exception;

class ZKTecoException extends Exception
{
    /**
     * Create a new ZKTeco exception instance.
     */
    public function __construct(string $message = 'ZKTeco device error', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        // Log to a specific channel
        \Log::channel('zkteco')->error($this->getMessage(), [
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
                'error' => 'ZKTeco Device Error',
                'message' => $this->getMessage(),
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Biometric device error: ' . $this->getMessage());
    }
}


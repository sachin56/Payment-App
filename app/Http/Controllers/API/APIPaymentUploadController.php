<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ProcessUploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PaymentUploadRequest;

class APIPaymentUploadController extends Controller
{
    public function storeFile(PaymentUploadRequest $request): JsonResponse
    {
        try {

            $file = $request->file('file');
            $filename = 'payments/'.date('Y/m/d').'/'.Str::random(12).'_'.$file->getClientOriginalName();

            $stream = fopen($file->getRealPath(), 'r+');
            Storage::disk('s3')->put($filename, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            ProcessUploadedFile::dispatch($filename, $request->user()?->id ?? null);

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::DATAFETCHEDFAILED,
            ], 500);
        }
    }
}

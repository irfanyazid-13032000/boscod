<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Tambahkan ini untuk memanfaatkan Carbon



class TransferController extends Controller
{
    public function createTransfer(Request $request) 
    {
        $validatedData = $request->validate([
            'nilai_transfer' => 'required',
            'bank_tujuan' => 'required',
            'rekening_tujuan' => 'required',
            'atasnama_tujuan' => 'required',
            'bank_pengirim' => 'required',
            'rekening_pengirim' => 'required',
        ]);

            // Generate a unique transaction ID with format TF{YYMMDD}{counter}
        $transactionCount = Transaction::whereDate('created_at', now()->toDateString())->count() + 1;
        $transactionId = 'TF' . now()->format('ymd') . str_pad($transactionCount, 5, '0', STR_PAD_LEFT);

        // Generate a unique 3-digit code
        $uniqueCode = rand(100, 999);

        // Calculate total transfer (nilai_transfer + kode_unik)
        $totalTransfer = $validatedData['nilai_transfer'] + $uniqueCode;

    
      
        Transaction::create([
            'id_transaksi'      => $transactionId,
            'bank_pengirim'  => $validatedData['bank_pengirim'],
            'bank_tujuan'    => $validatedData['bank_tujuan'],
            'rekening_tujuan'   => $validatedData['rekening_tujuan'],
            'atasnama_tujuan'   => $validatedData['atasnama_tujuan'],
            'nilai_transfer'    => (int) $validatedData['nilai_transfer'],
            'kode_unik'         => $uniqueCode,
            'total_transfer'    => $totalTransfer,
            'rekening_pengirim'    => $validatedData['rekening_pengirim'],
        ]);

        $validatedData['total_transfer'] = $totalTransfer;
    

        $banks = DB::table('bank')
        ->where('nama', 'like', '%' . $validatedData['bank_pengirim'] . '%')
        ->first();

        $rekening_admin = DB::table('rekening_admin')->where('bank_id',$banks->id)->first();

        $berlakuHingga = Carbon::now()->addDays(1)->format('Y-m-d\TH:i:sP');  // Set expiration to 1 day from now

    

        return response()->json([
            'id_transaksi' => $transactionId,
            'nilai_transfer'=>(int) $validatedData['nilai_transfer'],
            'kode_unik' => $uniqueCode,
            'biaya_admin' => 0,
            'total_transfer'=>$totalTransfer,
            'bank_perantara' => $validatedData['bank_pengirim'],
            'rekening_perantara' => $rekening_admin->no_rekening,
            'berlaku_hingga' => $berlakuHingga
        ]);

    }
    
}

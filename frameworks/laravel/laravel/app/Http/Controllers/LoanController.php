<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Loan::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrower' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
        ]);

        $loan = Loan::create($validated);

        return response()->json($loan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        return response()->json($loan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $validated = $request->validate([
            'borrower' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'interest_rate' => 'sometimes|required|numeric',
        ]);

        $loan->update($validated);

        return response()->json($loan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $loan->delete();

        return response()->json(['message' => 'Loan deleted']);
    }
}

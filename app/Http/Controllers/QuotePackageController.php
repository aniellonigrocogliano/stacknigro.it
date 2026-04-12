<?php

namespace App\Http\Controllers;

use App\Models\QuotePackage;
use App\Models\QuoteLevel;
use App\Models\QuoteOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotePackageController extends Controller
{
    public function create()
    {
        $levels = QuoteLevel::with(['options' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }])->orderBy('level')->get();

        return view('admin.quotes.packages.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'icon'                => ['nullable', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'discount_type'       => ['required', 'in:price,percentage'],
            'promo_price'         => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active'           => ['nullable'],
            'sort_order'          => ['nullable', 'integer'],
            'options'             => ['required', 'array'],
            'options.*'           => ['integer', 'exists:quote_options,id'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $realValue = QuoteOption::whereIn('id', $data['options'])->sum('price');
            $promoPrice = $data['promo_price'] ?? 0;
            $discountPercent = $data['discount_percentage'] ?? 0;

            if ($data['discount_type'] === 'percentage') {
                $promoPrice = $realValue - ($realValue * ($discountPercent / 100));
            } else {
                $discountPercent = $realValue > 0 ? (($realValue - $promoPrice) / $realValue * 100) : 0;
            }

            QuotePackage::create([
                'name'                => $data['name'],
                'icon'                => $data['icon'] ?? 'fa-solid fa-box',
                'description'         => $data['description'],
                'promo_price'         => $promoPrice,
                'discount_percentage' => round($discountPercent, 2),
                'discount_type'       => $data['discount_type'],
                'is_active'           => $request->has('is_active') ? 1 : 0,
                'sort_order'          => (int)($data['sort_order'] ?? 0),
            ])->options()->sync($data['options']);
        });

        return redirect()->route('admin.quotes.index', ['tab' => 'packages'])
                         ->with('success', 'Pacchetto creato con successo.');
    }

    /**
     * ✅ METODO AGGIUNTO: Carica i dati per la modifica
     */
    public function edit(QuotePackage $quotePackage)
    {
        // Carichiamo le opzioni già associate al pacchetto
        $selectedOptions = $quotePackage->options->pluck('id')->toArray();

        // Carichiamo tutti i livelli per le checkbox del Blade
        $levels = QuoteLevel::with(['options' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }])->orderBy('level')->get();

        return view('admin.quotes.packages.edit', compact('quotePackage', 'levels', 'selectedOptions'));
    }

    public function update(Request $request, QuotePackage $quotePackage)
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'icon'                => ['nullable', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'discount_type'       => ['required', 'in:price,percentage'],
            'promo_price'         => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active'           => ['nullable'],
            'sort_order'          => ['nullable', 'integer'],
            'options'             => ['required', 'array'],
        ]);

        DB::transaction(function () use ($data, $request, $quotePackage) {
            $realValue = QuoteOption::whereIn('id', $data['options'])->sum('price');
            $promoPrice = $data['promo_price'] ?? 0;
            $discountPercent = $data['discount_percentage'] ?? 0;

            if ($data['discount_type'] === 'percentage') {
                $promoPrice = $realValue - ($realValue * ($discountPercent / 100));
            } else {
                $discountPercent = $realValue > 0 ? (($realValue - $promoPrice) / $realValue * 100) : 0;
            }

            $quotePackage->update([
                'name'                => $data['name'],
                'icon'                => $data['icon'] ?? 'fa-solid fa-box',
                'description'         => $data['description'],
                'promo_price'         => $promoPrice,
                'discount_percentage' => round($discountPercent, 2),
                'discount_type'       => $data['discount_type'],
                'is_active'           => $request->has('is_active') ? 1 : 0,
                'sort_order'          => (int)($data['sort_order'] ?? 0),
            ]);

            $quotePackage->options()->sync($data['options']);
        });

        return redirect()->route('admin.quotes.index', ['tab' => 'packages'])
                         ->with('success', 'Pacchetto aggiornato.');
    }

    /**
     * ✅ METODO AGGIUNTO: Elimina il pacchetto
     */
    public function destroy(QuotePackage $quotePackage)
    {
        $quotePackage->delete();
        return redirect()->route('admin.quotes.index', ['tab' => 'packages'])
                         ->with('success', 'Pacchetto eliminato.');
    }
}

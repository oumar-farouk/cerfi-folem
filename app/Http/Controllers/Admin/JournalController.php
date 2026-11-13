<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class JournalController extends Controller
{
    /**
     * Journal d'activité du back-office (réservé au super-admin par la route).
     * S'appuie sur spatie/laravel-activitylog ; si le paquet n'est pas encore
     * installé, la vue explique comment l'installer au lieu de planter.
     */
    public function index(Request $request): View
    {
        if (! class_exists(Activity::class)) {
            return view('admin.journal.index', ['installe' => false]);
        }

        $request->validate([
            'utilisateur' => ['nullable', 'integer'],
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date'],
        ]);

        $recherche = $request->string('q')->trim()->toString();

        $activites = Activity::query()
            ->with(['causer', 'subject'])
            ->when($request->filled('utilisateur'), fn ($q) => $q
                ->where('causer_type', User::class)
                ->where('causer_id', $request->integer('utilisateur')))
            ->when($recherche !== '', fn ($q) => $q->where('description', 'like', "%{$recherche}%"))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('created_at', '>=', $request->string('du')->toString()))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('created_at', '<=', $request->string('au')->toString()))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $utilisateurs = User::query()
            ->whereIn('id', Activity::query()->where('causer_type', User::class)->distinct()->pluck('causer_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.journal.index', [
            'installe' => true,
            'activites' => $activites,
            'utilisateurs' => $utilisateurs,
        ]);
    }
}

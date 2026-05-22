<?php

namespace App\Http\Controllers;

use App\Models\Arritje;
use App\Models\Blerje;
use App\Models\Fond;
use App\Models\Ipo;
use App\Models\Investim;
use App\Models\Kompania;
use App\Models\Marredhenie;
use App\Models\Personi;
use App\Models\RaundiFinancimit;
use App\Models\User;
use App\Services\VerifikimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifikuesController extends Controller
{
    protected VerifikimService $verifikimService;

    protected array $modelMap = [
        'user' => User::class,
        'personi' => Personi::class,
        'kompania' => Kompania::class,
        'marredhenia' => Marredhenie::class,
        'raundi' => RaundiFinancimit::class,
        'investim' => Investim::class,
        'fond' => Fond::class,
        'blerja' => Blerje::class,
        'arritja' => Arritje::class,
        'ipo' => Ipo::class,
    ];

    public function __construct(VerifikimService $verifikimService)
    {
        $this->verifikimService = $verifikimService;
    }

    public function index()
    {
        // Use pagination to avoid loading all pending records into memory
        $perPage = 25;

        $filterType = request('filter_type');
        $status = request('status', 'pending');
        $q = request('q');

        // Users pending verification
        $users_pending = User::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })
            ->when($q && ($filterType === 'user' || $filterType === null), function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'user_page')
            ->withQueryString();

        // Personat pending verification
        $personat_pending = Personi::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })
            ->when($q && ($filterType === 'personi' || $filterType === null), function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%");
            })
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'personi_page')
            ->withQueryString();

        $kompanite_pending = Kompania::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })
            ->when($q && ($filterType === 'kompania' || $filterType === null), function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'kompania_page')
            ->withQueryString();

        // Lightweight paginators for other entities to avoid undefined variables in the view
        $marredheniet_pending = Marredhenie::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'marredheniet_page')->withQueryString();

        $raundet_pending = RaundiFinancimit::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'raundet_page')->withQueryString();

        $investimet_pending = Investim::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'investimet_page')->withQueryString();

        $fondet_pending = Fond::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'fondet_page')->withQueryString();

        $blerjet_pending = Blerje::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'blerjet_page')->withQueryString();

        $arritjet_pending = Arritje::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'arritjet_page')->withQueryString();

        $ipos_pending = Ipo::when($status !== 'all', function ($query) use ($status) {
                $query->where('verification_status', $status);
            })->orderBy('created_at')->paginate($perPage, ['*'], 'ipos_page')->withQueryString();

        return view('verifikues.index', compact(
            'users_pending',
            'personat_pending',
            'kompanite_pending',
            'marredheniet_pending',
            'raundet_pending',
            'investimet_pending',
            'fondet_pending',
            'blerjet_pending',
            'arritjet_pending',
            'ipos_pending'
        ));
    }

    public function approve(Request $request, string $type, int $id)
    {
        return $this->handleAction($request, $type, $id, 'verified', 'approved', 'Kryer: verifikimi u pranuar.');
    }

    public function reject(Request $request, string $type, int $id)
    {
        return $this->handleAction($request, $type, $id, 'rejected', 'rejected', 'Kryer: verifikimi u refuzua.');
    }

    public function flag(Request $request, string $type, int $id)
    {
        return $this->handleAction($request, $type, $id, 'flagged', 'flagged', 'Kryer: e dyshimtë.');
    }

    protected function handleAction(Request $request, string $type, int $id, string $status, string $action, string $toast)
    {
        $modelClass = $this->resolveVerifiableModel($type);
        $record = $modelClass::findOrFail($id);

        $oldStatus = $record->verification_status ?? null;
        $record->verification_status = $status;
        $record->verified_by = Auth::id();
        $record->verified_at = now();
        $record->verification_note = $request->input('notes');
        $record->save();

        $this->verifikimService->logAction(
            $type,
            $record->id,
            $action,
            $request->input('notes'),
            Auth::id()
        );

        return redirect()->back()->with('success', $toast);
    }

    protected function resolveVerifiableModel(string $type): string
    {
        if (! isset($this->modelMap[$type])) {
            abort(404);
        }

        return $this->modelMap[$type];
    }
}

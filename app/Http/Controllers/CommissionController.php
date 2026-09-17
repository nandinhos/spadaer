<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Commission;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    /**
     * Exibe a lista de comissões.
     */
    public function index()
    {
        $commissions = Commission::orderBy('id')->paginate(10);

        return view('commissions.index', compact('commissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::orderBy('id', 'asc')->get();

        return view('commissions.create', compact('users'));
    }

    /**
     * Armazena uma nova comissão.
     */
    public function store(StoreCommissionRequest $request): RedirectResponse
    {
        $file = $request->hasFile('ordinance_file') ? $request->file('ordinance_file') : null;

        $this->commissionService->create($request->validated(), $file);

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commission $commission): View
    {
        $commission->load(['members' => function ($query) {
            $query->orderBy('user_id', 'asc')->with('user');
        }]);

        return view('commissions.show', compact('commission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commission $commission): View
    {
        $users = User::orderBy('id', 'asc')->get();
        $commission->load('members');

        return view('commissions.edit', compact('commission', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommissionRequest $request, Commission $commission): RedirectResponse
    {
        try {
            $file = $request->hasFile('ordinance_file') ? $request->file('ordinance_file') : null;
            $this->commissionService->update($commission, $request->validated(), $file);

            return redirect()->route('commissions.index')
                ->with('success', 'Comissão atualizada com sucesso.');
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar comissão: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar dados da comissão. Verifique os logs.')->withInput();
        }
    }

    /**
     * Remove uma comissão específica.
     */
    public function destroy(Commission $commission): RedirectResponse
    {
        $this->commissionService->delete($commission);

        return redirect()->route('commissions.index')
            ->with('success', 'Comissão removida com sucesso.');
    }
}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Balance General</h1>
            <p class="text-muted small">Estado de situación financiera a la fecha.</p>
        </div>
        <div class="col-auto">
            <button onclick="window.print()" class="btn btn-sm btn-secondary">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Activos -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">ACTIVOS</h6>
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless table-hover">
                            <tbody>
                                @foreach($assets as $account)
                                <tr class="{{ !$account->is_movement ? 'fw-bold bg-light' : '' }}">
                                    <td style="padding-left: {{ ($account->level - 1) * 20 }}px;">
                                        <small class="text-muted me-2">{{ $account->code }}</small>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($account->current_balance, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="fw-bold fs-5">
                                    <td>TOTAL ACTIVOS</td>
                                    <td class="text-end">{{ number_format($assets->where('level', 1)->sum('current_balance'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pasivos y Patrimonio -->
        <div class="col-md-6 mb-4">
            <!-- Pasivos -->
            <div class="card shadow mb-4 border-left-danger">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">PASIVOS</h6>
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless table-hover">
                            <tbody>
                                @foreach($liabilities as $account)
                                <tr class="{{ !$account->is_movement ? 'fw-bold bg-light' : '' }}">
                                    <td style="padding-left: {{ ($account->level - 1) * 20 }}px;">
                                        <small class="text-muted me-2">{{ $account->code }}</small>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($account->current_balance, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="fw-bold">
                                    <td>TOTAL PASIVOS</td>
                                    <td class="text-end">{{ number_format($liabilities->where('level', 1)->sum('current_balance'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Patrimonio -->
            <div class="card shadow border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-success text-white">
                    <h6 class="m-0 font-weight-bold">PATRIMONIO</h6>
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless table-hover">
                            <tbody>
                                @foreach($equity as $account)
                                <tr class="{{ !$account->is_movement ? 'fw-bold bg-light' : '' }}">
                                    <td style="padding-left: {{ ($account->level - 1) * 20 }}px;">
                                        <small class="text-muted me-2">{{ $account->code }}</small>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($account->current_balance, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="fw-bold">
                                    <td>TOTAL PATRIMONIO</td>
                                    <td class="text-end">{{ number_format($equity->where('level', 1)->sum('current_balance'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ecuación Contable -->
            <div class="alert alert-info mt-3 d-flex justify-content-between align-items-center">
                <span>Total Pasivo + Patrimonio:</span>
                <span class="fw-bold fs-5">
                    {{ number_format($liabilities->where('level', 1)->sum('current_balance') + $equity->where('level', 1)->sum('current_balance'), 2) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

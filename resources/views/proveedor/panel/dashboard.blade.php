@extends('layouts.proveedor')

@section('content')
    @php
        // If legacy variable $ocs exists, split into pendientes/confirmadas
        if (! isset($ocsPendientes) && isset($ocs)) {
            $ocsPendientes = $ocs->where('status', 'Pendiente');
        }
        if (! isset($ocsConfirmadas) && isset($ocs)) {
            $ocsConfirmadas = $ocs->where('status', 'Confirmada');
        }
    @endphp

    @include('proveedor._dashboard_content')
@endsection

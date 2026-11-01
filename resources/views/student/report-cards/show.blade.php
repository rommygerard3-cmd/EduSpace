@extends('layouts.app')

@section('title', 'Bulletin')

@section('content')
    @include('pdf.report-card')
    <div class="mt-4">
        <a href="{{ route('student.report-cards.pdf', $data['term']) }}" class="btn-primary">Télécharger le PDF</a>
    </div>
@endsection

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Recu - {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: bold; color: #1e3a8a; }
        .muted { color: #555; font-size: 10px; }
        .meta { margin: 16px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">EduSpace</div>
        <div class="muted">{{ $school?->name }}</div>
        <div class="muted">{{ $school?->address }} - {{ $school?->phone }} - {{ $school?->email }}</div>
    </div>

    <div class="meta">
        <p><strong>Numero de recu :</strong> {{ $payment->id }}</p>
        <p><strong>Eleve :</strong> {{ $invoice->student?->fullName() }} ({{ $invoice->student?->matricule }})</p>
        <p><strong>Facture :</strong> {{ $invoice->number }}</p>
        <p><strong>Paiement :</strong> {{ number_format((float) $payment->amount, 2, ',', ' ') }}</p>
        <p><strong>Methode :</strong> {{ $payment->method }}</p>
        <p><strong>Date :</strong> {{ $payment->paid_at?->format('d/m/Y H:i') }}</p>
        <p><strong>Enregistre par :</strong> {{ $payment->recordedBy?->name }}</p>
    </div>

    <p>Merci de votre paiement.</p>
</body>
</html>

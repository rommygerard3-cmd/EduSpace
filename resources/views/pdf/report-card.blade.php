<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletin - {{ $student->fullName() }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: bold; color: #1e3a8a; }
        .muted { color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f1f5f9; }
        .footer { margin-top: 32px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 45%; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">EduSpace</div>
        <div class="muted">{{ $school->name }}</div>
        <div class="muted">{{ $school->address }} - {{ $school->phone }} - {{ $school->email }}</div>
        <div class="muted">Annee scolaire : {{ optional($academic_year)->name }}</div>
    </div>

    <table>
        <tr>
            <th style="width: 50%">Identite</th>
            <th style="width: 50%">Parcours</th>
        </tr>
        <tr>
            <td>
                <strong>{{ $student->fullName() }}</strong><br>
                <span class="muted">Matricule : {{ $student->matricule }}</span><br>
                {{ optional($school_class)->displayName() }}<br>
                {{ optional($term)->name }}
            </td>
            <td>
                Moyenne generale : <strong>{{ $average !== null ? number_format($average, 2, ',', ' ') : 'Non calculable' }}</strong><br>
                Mention : {{ $mention }}<br>
                Decision : {{ $decision }}<br>
                Rang : {{ $rank !== null ? $rank : 'N/A' }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Matiere</th><th>Coefficient</th><th>Note /20</th><th>Moyenne</th></tr>
        </thead>
        <tbody>
            @forelse($subjects as $subject)
                <tr>
                    <td>{{ $subject['subject']->name }}</td>
                    <td>{{ $subject['coefficient'] }}</td>
                    <td>{{ $subject['has_grades'] ? number_format($subject['average'], 2, ',', ' ') : 'N/A' }}</td>
                    <td>{{ number_format($subject['weighted_average'], 2, ',', ' ') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Aucune matiere.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">Le directeur</div>
        <div class="signature">Le professeur principal</div>
        <div class="signature">La direction</div>
    </div>
</body>
</html>

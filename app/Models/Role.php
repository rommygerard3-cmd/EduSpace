<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const ELEVE = 'eleve';

    public const ENSEIGNANT = 'enseignant';

    public const PREFET = 'prefet';

    public const DIRECTEUR_DISCIPLINE = 'directeur_discipline';

    public const DIRECTION = 'direction';

    public const ADMINISTRATEUR_GENERAL = 'administrateur_general';

    public const SECRETARIAT = 'secretariat';

    public const COMPTABILITE = 'comptabilite';

    protected $fillable = ['name', 'label', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public static function labels(): array
    {
        return [
            self::ELEVE => 'Élève',
            self::ENSEIGNANT => 'Enseignant',
            self::PREFET => 'Préfet',
            self::DIRECTEUR_DISCIPLINE => 'Directeur de discipline',
            self::DIRECTION => 'Direction',
            self::ADMINISTRATEUR_GENERAL => 'Administrateur général',
            self::SECRETARIAT => 'Secrétariat',
            self::COMPTABILITE => 'Comptabilité',
        ];
    }
}

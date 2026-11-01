Tu es un ingénieur senior spécialisé en cybersécurité applicative, architecture Laravel, tests logiciels, bases de données MySQL et ingénierie de la qualité.

Ta mission consiste à réaliser un audit technique approfondi de l'application EduSpace, puis à corriger les vulnérabilités et les défauts de fiabilité identifiés.

IMPORTANT : Il s'agit d'un projet existant et fonctionnel. Tu dois préserver son architecture, ses fonctionnalités, ses fichiers et toutes ses données.

PHASE 1 — Audit de sécurité

Analyse l'ensemble du projet :

Authentification et gestion des sessions.
Gestion des rôles et permissions.
Middleware et protection des routes.
Contrôleurs, modèles et politiques d'autorisation.
Validation des données.
Protection contre les injections SQL.
Protection contre les attaques XSS et CSRF.
Gestion des fichiers téléchargés.
Gestion des mots de passe.
Gestion des erreurs et des informations sensibles.
Configuration de production.
Dépendances PHP et JavaScript.

Recherche les vulnérabilités OWASP pertinentes.

PHASE 2 — Vérification des permissions

Teste rigoureusement chaque rôle :

Directeur.
Secrétaire.
Comptable.
Enseignant.
Étudiant.

Vérifie qu'aucun utilisateur ne peut contourner ses permissions en modifiant une URL, un identifiant, un paramètre HTTP ou une requête.

Vérifie particulièrement :

L'isolation des données entre étudiants.
L'accès des enseignants uniquement à leurs cours et aux données autorisées.
La confidentialité des notes et des bulletins.
La protection des informations financières.
L'impossibilité pour un étudiant de modifier les informations de son profil qui sont en lecture seule.
La protection de toutes les opérations administratives.

Les autorisations doivent être vérifiées côté serveur.

PHASE 3 — Tests de fiabilité

Vérifie :

La cohérence des relations entre les tables.
Les contraintes d'unicité.
Les clés étrangères.
Les transactions SQL.
Les risques de doublons.
Les erreurs de concurrence.
La gestion des exceptions.
La validation des formulaires.
La stabilité des fonctionnalités principales.

Exécute les tests automatisés existants et identifie les fonctionnalités insuffisamment couvertes.

Ajoute des tests automatisés pour les scénarios critiques.

PHASE 4 — Tests de performance

Évalue :

Les temps de réponse.
Les requêtes SQL lentes.
Les problèmes N+1.
La pagination.
La consommation mémoire.
Le comportement avec un volume important de données.
La capacité à gérer plusieurs utilisateurs simultanément.

Ne prétends pas avoir réalisé un test de charge réel si tu ne l'as pas effectivement exécuté.

PHASE 5 — Corrections

Pour chaque vulnérabilité :

Explique le problème.
Indique son niveau de gravité : critique, élevé, moyen ou faible.
Identifie le fichier concerné.
Corrige le problème.
Ajoute un test qui reproduit le problème.
Vérifie que la correction fonctionne.
Vérifie qu'elle n'a pas cassé les fonctionnalités existantes.

Priorise les vulnérabilités critiques et élevées.

RÈGLES ABSOLUES
Ne supprime aucun fichier sans autorisation.
Ne supprime aucune donnée.
N'exécute jamais migrate, migrate, db ou toute commande destructive.
Ne réinitialise jamais la base de données existante.
Ne lance aucune migration sur la base réelle sans mon autorisation explicite.
Ne modifie pas les données réelles pour effectuer des tests.
Utilise une base de données de test isolée.
Ne révèle jamais les secrets contenus dans .env.
Ne désactive aucune protection pour faire passer un test.
Conserve les fonctionnalités et le comportement métier existants.

Si une action présente un risque pour les données ou l'environnement de production, arrête cette action et demande mon autorisation.

RAPPORT FINAL

Crée un document SECURITY_AUDIT_REPORT.md contenant :

Résumé exécutif.
Architecture examinée.
Vulnérabilités découvertes.
Niveau de gravité de chaque vulnérabilité.
Corrections appliquées.
Résultats des tests automatisés.
Résultats des tests de permissions.
Résultats des tests de performance réellement exécutés.
Risques résiduels.
Recommandations avant mise en production.

Ne déclare jamais EduSpace sécurisé sans preuves vérifiables.

Ta mission ne s'arrête pas à l'analyse : corrige les problèmes identifiés, ajoute les tests nécessaires et vérifie les corrections jusqu'à obtenir un résultat satisfaisant.
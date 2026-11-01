MISSION GLOBALE OPENCODE
EDUSPACE — CONCEPTION, AUDIT, DÉVELOPPEMENT ET FINALISATION D'UNE PLATEFORME INTÉGRÉE DE GESTION SCOLAIRE
1. Présentation générale du projet

Tu es chargé de reprendre intégralement le projet EduSpace, une application professionnelle de gestion scolaire développée avec Laravel et MySQL.

EduSpace est une plateforme numérique destinée à informatiser, centraliser et automatiser les activités administratives, pédagogiques, financières et disciplinaires d'un établissement scolaire.

Ce projet doit permettre à un établissement de gérer l'ensemble de son fonctionnement quotidien depuis une seule application.

Il ne s'agit pas simplement d'un site web présentant des informations scolaires.

Il s'agit d'un véritable système d'information scolaire (SIS — Student Information System) avec plusieurs espaces utilisateurs, des permissions précises, des données centralisées, des historiques et des processus automatisés.

Vision du projet

EduSpace doit devenir une plateforme dans laquelle :

La Direction administre l'établissement.
L'administration gère les élèves et les personnels.
Les enseignants organisent les cours et les évaluations.
Les élèves consultent leurs résultats et leur parcours scolaire.
Le préfet supervise les activités pédagogiques.
Le directeur de discipline assure le suivi disciplinaire.
Le service financier gère les frais scolaires.
L'administrateur général contrôle la configuration technique et les permissions.

Toutes ces activités doivent être interconnectées.

Une information enregistrée dans un espace doit être disponible dans les autres espaces concernés, conformément aux permissions.

Exemple :

Lorsqu'un enseignant publie les résultats d'une interrogation, les élèves concernés doivent pouvoir consulter leurs notes. Ces résultats doivent également alimenter les moyennes, les statistiques pédagogiques et les bulletins, selon les règles de calcul de l'établissement.

Autre exemple :

Lorsqu'un enseignant enregistre une absence, celle-ci doit apparaître dans l'historique de présence de l'élève et être accessible au service de discipline. Une notification peut également être créée pour les responsables concernés.

C'est cette interconnexion qui constitue le cœur d'EduSpace.

2. Architecture fonctionnelle générale

EduSpace doit être organisé autour de plusieurs grands modules.

Module	Fonction principale
Authentification	Connexion, activation et récupération des comptes
Utilisateurs	Gestion des élèves et personnels
Matricules	Attribution et contrôle des identifiants
Rôles et permissions	Contrôle des accès
Années scolaires	Gestion des années et périodes
Classes	Organisation des élèves
Sections et options	Organisation pédagogique
Matières	Gestion des disciplines scolaires
Enseignants	Gestion du personnel enseignant
Affectations	Attribution des classes et matières
Horaires	Organisation des cours
Présences	Suivi des présences et absences
Évaluations	Création des interrogations et examens
Notes	Saisie et calcul des résultats
Bulletins	Génération et validation des bulletins
Préparations	Gestion des fiches pédagogiques
Discipline	Gestion des incidents et convocations
Finance	Gestion des frais et paiements
Notifications	Communication interne
Rapports	Statistiques et analyses
Audit	Historique des opérations
Paramètres	Configuration générale

Ces modules ne doivent pas fonctionner indépendamment.

Ils doivent partager une architecture de données cohérente.

3. Les utilisateurs et leurs responsabilités

EduSpace doit gérer six grands profils d'utilisateurs.

3.1. Élève

L'élève est un utilisateur qui possède un espace personnel.

Son espace est essentiellement consultatif.

Il peut consulter :

Son identité.
Son matricule.
Sa photo.
Sa classe.
Son option et sa section.
Son année scolaire.
Son emploi du temps.
Ses matières.
Ses enseignants.
Ses évaluations.
Ses notes publiées.
Ses moyennes.
Ses bulletins.
Ses absences.
Ses retards.
Ses notifications.
Ses paiements.
Ses reçus.

Il peut modifier son mot de passe et consulter les informations autorisées de son compte.

Il ne peut pas modifier ses notes, ses paiements, sa classe, son matricule ou ses informations administratives.

Il ne doit jamais pouvoir consulter les informations personnelles ou scolaires d'un autre élève.

3.2. Enseignant

L'enseignant est responsable de l'exécution des activités pédagogiques qui lui sont attribuées.

Il doit pouvoir :

Consulter ses classes.
Consulter ses matières.
Consulter ses élèves.
Consulter son horaire.
Créer des évaluations.
Saisir des notes.
Enregistrer des brouillons.
Corriger les résultats.
Publier les notes selon ses autorisations.
Enregistrer les présences.
Consulter les statistiques de ses classes.
Préparer ses leçons.
Soumettre ses fiches de préparation au préfet.
Consulter les observations administratives.

Il ne peut pas accéder aux classes ou matières qui ne lui sont pas attribuées, sauf autorisation particulière.

3.3. Préfet

Le préfet assure la supervision pédagogique.

Il doit disposer d'une vue globale des activités pédagogiques.

Il peut :

Consulter les horaires de toutes les classes.
Superviser les enseignants.
Examiner les fiches de préparation.
Consulter les évaluations.
Suivre les résultats.
Comparer les performances des classes.
Consulter les statistiques de présence.
Identifier les difficultés pédagogiques.
Suivre les taux de réussite et d'échec.
Produire des rapports pédagogiques.

Le préfet ne doit pas recevoir automatiquement les droits financiers ou administratifs de la Direction.

3.4. Directeur de discipline

Il est responsable du suivi disciplinaire des élèves.

Il doit pouvoir :

Consulter les élèves.
Consulter les classes.
Consulter les absences.
Consulter les retards.
Enregistrer les incidents.
Enregistrer les observations disciplinaires.
Appliquer les sanctions autorisées.
Consulter les notes de conduite.
Envoyer des convocations aux parents.
Consulter l'historique disciplinaire.
Suivre les notifications.

Il ne peut pas modifier les notes académiques ni les paiements sans autorisation spécifique.

3.5. Direction

La Direction est responsable de la gestion administrative de l'établissement.

Elle possède les fonctionnalités nécessaires à l'administration quotidienne.

Elle peut :

Créer les élèves.
Créer les enseignants.
Créer les personnels.
Attribuer les matricules.
Affecter les rôles.
Créer les classes.
Gérer les sections.
Gérer les options.
Créer les matières.
Organiser les années scolaires.
Organiser les périodes.
Affecter les enseignants.
Organiser les horaires.
Superviser les présences.
Superviser les notes.
Valider les bulletins.
Gérer les inscriptions.
Gérer les réinscriptions.
Consulter les rapports.
Consulter les journaux d'audit.
Gérer les paramètres administratifs.

Les opérations financières doivent être soumises aux permissions correspondantes.

3.6. Administrateur général

L'administrateur général assure la gestion technique et globale de la plateforme.

Il peut :

Gérer les utilisateurs.
Gérer les rôles.
Gérer les permissions.
Configurer la sécurité.
Gérer les paramètres globaux.
Consulter les journaux d'audit.
Gérer les établissements si une architecture multiétablissement est prévue.
Effectuer les opérations de maintenance autorisées.

Attention : posséder un rôle d'administrateur ne signifie pas que toutes les opérations sont automatiquement autorisées sans contrôle.

4. Gestion complète des utilisateurs et des matricules

Le matricule constitue l'identifiant principal de connexion.

Chaque utilisateur doit posséder un matricule unique.

Exemples :

ELE-2026-0001
ENS-2026-0001
PREF-2026-001
DISC-2026-001
DIR-2026-001

Ces formats sont des exemples. Ils doivent pouvoir être configurés par l'administration.

Fonctionnement
Une personne autorisée crée un utilisateur.
Le système attribue ou enregistre son matricule.
Un code temporaire d'activation est généré.
Le matricule et le code temporaire sont communiqués à l'utilisateur.
L'utilisateur se connecte pour activer son compte.
Il définit son propre mot de passe.
Son compte devient actif.

Le code d'activation doit être temporaire, à usage unique et stocké de manière sécurisée.

Un matricule ne doit jamais être réutilisé pour une autre personne, même si l'ancien compte est désactivé.

Prévoir :

Unicité des matricules.
Activation sécurisée.
Réinitialisation des mots de passe.
Désactivation des comptes.
Historique des comptes.
Protection contre les tentatives répétées.
Expiration des sessions.
Journalisation des connexions.
5. Gestion des établissements, années scolaires et périodes

EduSpace doit permettre de gérer plusieurs années scolaires sans perdre les données historiques.

Exemple :

2025-2026
2026-2027
2027-2028

Chaque année scolaire possède :

Une date de début.
Une date de fin.
Un statut.
Des périodes scolaires.
Des classes.
Des affectations.
Des horaires.
Des évaluations.
Des résultats.
Des présences.
Des paiements.

L'administration doit pouvoir ouvrir une nouvelle année scolaire.

Lorsqu'une nouvelle année commence :

Les anciennes données restent conservées.
Les élèves peuvent être réinscrits.
Les élèves peuvent passer dans une classe supérieure.
Les élèves peuvent redoubler.
Les élèves peuvent être transférés.
Les nouvelles affectations sont créées.
Les nouveaux horaires sont définis.

Le matricule personnel de l'élève reste identique d'une année à l'autre.

Attention : changer d'année scolaire ne signifie jamais supprimer les anciennes données.

6. Gestion des classes, sections, options et matières

Le système doit représenter correctement l'organisation pédagogique de l'établissement.

Une classe appartient à une année scolaire.

Une classe peut être associée à une section ou à une option.

Exemple :

1re secondaire.
2e secondaire.
3e secondaire scientifique.
4e secondaire commerciale.
5e secondaire technique.

Les noms et structures doivent être configurables.

Chaque classe possède :

Un nom.
Une année scolaire.
Une section éventuelle.
Une option éventuelle.
Un titulaire éventuel.
Une capacité éventuelle.
Une liste d'élèves.
Une liste de matières.
Gestion des matières

Chaque matière doit posséder :

Un nom.
Un code.
Une description éventuelle.
Un coefficient.
Une classe concernée.
Une période.
Un enseignant responsable selon les affectations.

Un enseignant peut intervenir dans plusieurs classes et enseigner plusieurs matières.

Il faut donc utiliser des relations adaptées entre enseignants, matières et classes.

Ne crée pas une relation simplifiée qui empêcherait un enseignant d'avoir plusieurs affectations.

7. Gestion complète des élèves

La Direction doit pouvoir gérer le cycle de vie administratif de chaque élève.

Création d'un élève

Prévoir :

Nom.
Postnom.
Prénom.
Sexe si nécessaire.
Date de naissance.
Lieu de naissance.
Adresse.
Photo.
Informations du responsable légal.
Numéro de téléphone du responsable.
Matricule.
Classe.
Section.
Option.
Année scolaire.
Statut d'inscription.

Les champs réellement nécessaires doivent être définis selon la politique de l'établissement.

Cycle de vie

Un élève peut être :

Préinscrit.
Inscrit.
Réinscrit.
Transféré.
Passé dans la classe supérieure.
Redoublant.
Sorti de l'établissement.

Chaque changement doit être historisé.

Il ne faut pas supprimer les anciens dossiers pour effectuer une réinscription.

8. Gestion complète des enseignants et des personnels

La Direction doit pouvoir créer et gérer les personnels.

Pour un enseignant, prévoir :

Identité.
Photo.
Matricule.
Téléphone.
Adresse.
Qualification.
Spécialité.
Date d'engagement.
Statut professionnel.
Matières enseignées.
Classes attribuées.

Les affectations doivent être historisées.

Si un enseignant change de classe ou de matière, les anciennes affectations doivent rester consultables.

9. Gestion des emplois du temps

EduSpace doit posséder un véritable module de planification des cours.

Un cours doit être lié à :

Une classe.
Une matière.
Un enseignant.
Un jour.
Une heure de début.
Une heure de fin.
Une salle.
Une année scolaire.

Le système doit contrôler les conflits.

Exemples :

Un enseignant ne peut pas donner deux cours simultanément.
Une salle ne peut pas accueillir deux cours simultanément.
Une classe ne peut pas suivre deux matières au même moment.

Prévoir :

Vue hebdomadaire.
Vue par classe.
Vue par enseignant.
Vue par salle.
Consultation sur téléphone.
Modification contrôlée.
Historique des changements.

Les élèves consultent uniquement l'horaire de leur classe.

Les enseignants consultent leur propre horaire.

La Direction et les responsables autorisés disposent d'une vue globale.

10. Gestion des évaluations et des notes

Ce module constitue l'un des éléments centraux d'EduSpace.

Il doit gérer toutes les évaluations scolaires.

Types d'évaluations
Interrogations.
Devoirs.
Travaux pratiques.
Travaux dirigés.
Examens.
Évaluations complémentaires.

Chaque évaluation possède :

Un titre.
Une matière.
Une classe.
Un enseignant.
Une période.
Une date.
Un maximum de points.
Une catégorie.
Un statut.
Saisie des notes

L'enseignant doit pouvoir saisir les notes de ses élèves dans une interface simple.

Exemple :

Élève	Matricule	Note /20	Statut
Élève A	ELE-001	15	Brouillon
Élève B	ELE-002	12	Brouillon
Élève C	ELE-003	17	Brouillon

L'enseignant peut enregistrer les résultats en brouillon.

Après vérification, il peut les publier si ses permissions l'autorisent.

Une note en brouillon n'est jamais visible par l'élève.

Une note publiée devient consultable.

Toute modification après publication doit être autorisée et enregistrée dans le journal d'audit.

Calculs

Le système doit calculer automatiquement :

Moyennes par évaluation.
Moyennes par matière.
Moyennes par période.
Moyennes générales.
Résultats avec coefficients.
Totaux.
Taux de réussite.
Taux d'échec.

Les formules doivent être configurables.

Ne jamais inventer de notes ou de moyennes.

11. Gestion des bulletins scolaires

EduSpace doit générer de véritables bulletins scolaires en PDF.

Un bulletin doit être produit à partir des notes enregistrées dans la base de données.

Il doit comporter :

Logo de l'établissement.
Nom de l'établissement.
Adresse et contacts.
Identité de l'élève.
Matricule.
Classe.
Année scolaire.
Période.
Liste des matières.
Notes obtenues.
Maximums.
Coefficients.
Moyennes.
Total des points.
Moyenne générale.
Appréciations.
Décision finale.
Signatures et validations.

Le bulletin doit être professionnel, lisible et adapté à l'impression.

Circuit de validation
Les enseignants saisissent les notes.
Les résultats sont vérifiés.
Les moyennes sont calculées.
Le bulletin est généré.
Une personne autorisée le valide.
Le bulletin est publié.
L'élève peut le consulter et le télécharger.

Un bulletin publié ne doit pas être modifié silencieusement.

Toute correction doit être autorisée et historisée.

12. Gestion des présences et absences

Ce module doit permettre de suivre les présences des élèves.

Un enseignant peut enregistrer la présence pour une séance donnée.

Les statuts possibles :

Présent.
Absent.
En retard.
Excusé.

Chaque présence doit contenir :

Élève.
Classe.
Séance.
Matière.
Enseignant.
Date.
Heure.
Statut.
Observation éventuelle.

Le système doit empêcher les doublons.

Une absence peut déclencher une notification au service de discipline.

Le système doit produire :

Statistiques par élève.
Statistiques par classe.
Statistiques par enseignant.
Historique des absences.
Historique des retards.
Taux de présence.
13. Gestion des fiches de préparation

Les enseignants doivent pouvoir préparer leurs leçons dans EduSpace.

Une fiche comprend :

Matière.
Classe.
Date.
Sujet.
Objectifs pédagogiques.
Prérequis.
Matériel didactique.
Méthodes pédagogiques.
Déroulement de la leçon.
Exercices.
Évaluation.
Observations.
Circuit de validation
L'enseignant crée une fiche.
Il enregistre un brouillon.
Il soumet la fiche.
Le préfet reçoit une notification.
Le préfet examine la fiche.
Il ajoute ses observations.
Il approuve ou demande une correction.
L'enseignant consulte la décision.

Prévoir un historique des versions et des décisions.

14. Gestion de la discipline

Le directeur de discipline doit disposer d'un module spécifique.

Il doit permettre de gérer :

Absences.
Retards.
Incidents.
Infractions au règlement.
Observations.
Sanctions.
Notes de conduite.
Convocations.
Historique disciplinaire.

Chaque incident doit être associé à un élève et à un auteur.

Convocations

Une convocation doit comporter :

Élève concerné.
Responsable légal.
Motif.
Date de création.
Date du rendez-vous.
Heure.
Auteur.
Statut.

Les statuts peuvent être :

Brouillon.
Envoyée.
Consultée.
Honorée.
Non honorée.
Annulée.

Attention : le statut « envoyée » ne doit être attribué que si l'envoi a réellement été effectué.

15. Gestion financière et paiements

EduSpace doit intégrer un véritable module financier.

La Direction ou le service financier autorisé doit pouvoir définir les frais scolaires.

Exemples :

Frais d'inscription.
Frais de minerval.
Frais de fonctionnement.
Frais d'examens.
Autres frais définis par l'établissement.

Chaque frais doit comporter :

Son nom.
Son montant.
Son année scolaire.
Sa classe ou catégorie concernée.
Ses échéances.
Son statut.
Enregistrement d'un paiement

Chaque paiement doit comporter :

Élève.
Frais concerné.
Montant.
Date.
Mode de paiement.
Référence éventuelle.
Auteur de l'enregistrement.
Numéro de reçu.

Prévoir les paiements partiels.

Le système doit calculer :

Montant total attendu.
Montant payé.
Solde restant.
Montant en retard.
Historique des paiements.
Reçus

Un reçu doit être généré en PDF.

Il doit contenir les informations de l'établissement, de l'élève, du paiement et de la personne ayant enregistré l'opération.

Un paiement ne doit jamais être déclaré effectué par l'élève lui-même.

Toute annulation ou correction doit être autorisée et historisée.

16. Notifications centralisées

Toutes les notifications doivent être centralisées.

Les événements suivants doivent pouvoir produire des notifications :

Absence.
Retard.
Convocation.
Paiement en attente.
Note publiée.
Bulletin disponible.
Changement d'horaire.
Fiche de préparation soumise.
Fiche examinée.
Information administrative.

Chaque notification doit comporter :

Destinataire.
Objet.
Message.
Date.
Statut de lecture.
Type de notification.
Référence à l'événement concerné.

Prévoir une notification interne dans l'application.

Les e-mails et SMS peuvent être ajoutés si les services sont configurés.

Ne simule jamais un envoi externe qui n'a pas réellement eu lieu.

17. Tableaux de bord et statistiques

Chaque rôle doit avoir un tableau de bord spécifique.

Les tableaux de bord doivent présenter des données réelles.

Tableau de bord élève
Informations personnelles.
Prochain cours.
Dernières notes publiées.
Moyennes.
Absences.
Notifications.
Paiements en attente.
Dernier bulletin.
Tableau de bord enseignant
Classes attribuées.
Matières.
Prochains cours.
Évaluations.
Notes en brouillon.
Présences à compléter.
Fiches de préparation.
Notifications.
Tableau de bord préfet
Effectif total.
Effectifs par classe.
Taux de présence.
Absences.
Retards.
Moyennes par classe.
Résultats par matière.
Taux de réussite.
Taux d'échec.
Fiches en attente.
Tableau de bord discipline
Nombre d'absences.
Nombre de retards.
Incidents enregistrés.
Convocations en attente.
Élèves concernés.
Évolution disciplinaire.
Tableau de bord Direction
Effectifs.
Classes.
Enseignants.
Inscriptions.
Réinscriptions.
Résultats.
Présences.
Situation financière.
Rapports administratifs.
Tableau de bord administrateur général
Utilisateurs.
Comptes actifs.
Comptes désactivés.
Rôles.
Permissions.
Activités récentes.
Journaux d'audit.
État technique de la plateforme.

Aucune statistique fictive n'est autorisée.

Si aucune donnée n'existe, afficher un état vide explicite.

18. Gestion des rapports et exports

Prévoir la génération de rapports professionnels.

Exemples :

Liste des élèves par classe.
Liste des enseignants.
Effectifs par année.
Résultats par matière.
Résultats par classe.
Statistiques de réussite.
Statistiques d'absences.
Rapport disciplinaire.
Rapport financier.
Historique des paiements.
Liste des impayés.
Rapport de fin d'année.

Les exports doivent être disponibles selon les permissions.

Formats recommandés :

PDF.
Excel.
CSV lorsque pertinent.

Les documents privés doivent être protégés contre les accès directs non autorisés.

19. Journalisation et audit

EduSpace doit disposer d'un système d'audit.

Les opérations sensibles doivent être enregistrées.

Exemples :

Connexion.
Déconnexion.
Création d'utilisateur.
Modification d'un profil.
Désactivation d'un compte.
Attribution d'un rôle.
Modification d'une permission.
Saisie d'une note.
Publication d'une note.
Correction d'un bulletin.
Enregistrement d'un paiement.
Annulation d'un paiement.
Modification d'un horaire.
Clôture d'une année scolaire.

Chaque événement doit conserver :

Auteur.
Action.
Type d'objet.
Identifiant de l'objet.
Anciennes valeurs si nécessaire.
Nouvelles valeurs si nécessaire.
Date et heure.
Informations techniques utiles.

Les utilisateurs ordinaires ne doivent pas pouvoir modifier ou supprimer les journaux d'audit.

20. Sécurité technique

La sécurité doit être intégrée à toute l'application.

Utilise les mécanismes Laravel adaptés :

Middleware.
Policies.
Gates.
Form Requests.
Validation serveur.
Transactions SQL.
Protection CSRF.
Hachage des mots de passe.
Limitation des tentatives de connexion.
Protection des fichiers.
Contrôle d'accès aux ressources.
Validation des uploads.
Gestion sécurisée des sessions.

Trois niveaux de contrôle sont obligatoires :

Niveau 1 : interface

L'utilisateur ne voit que les fonctions qui lui sont autorisées.

Niveau 2 : serveur

Les routes et contrôleurs vérifient systématiquement les permissions.

Niveau 3 : données

Les requêtes vérifient que l'utilisateur peut réellement accéder à l'enregistrement demandé.

Exemple :

Un élève ne doit pas pouvoir consulter le bulletin d'un autre élève en remplaçant simplement son identifiant dans l'URL.

21. Identité visuelle et expérience utilisateur

EduSpace doit posséder une identité visuelle cohérente.

Direction artistique
Bleu comme couleur principale.
Blanc comme couleur de surface.
Bleu clair pour les éléments secondaires.
Gris neutre pour les textes secondaires.
Typographie professionnelle.
Icônes cohérentes.
Espacements réguliers.
Composants harmonisés.
Interface

L'application doit être :

Moderne.
Professionnelle.
Responsive.
Rapide.
Accessible.
Facile à utiliser.

Elle doit fonctionner correctement sur :

Ordinateur.
Tablette.
Téléphone Android.
Téléphone iPhone.
Navigation

Chaque rôle doit avoir son propre menu.

Ne présente pas à un élève les menus administratifs.

Ne présente pas à un enseignant les opérations financières réservées.

Ne présente pas au directeur de discipline les fonctions académiques non autorisées.

Prévoir :

Barre latérale sur ordinateur.
Navigation adaptée au mobile.
Recherche.
Filtres.
Pagination.
Notifications.
Profil utilisateur.
Messages de succès.
Messages d'erreur.
États vides.
Confirmations des opérations sensibles.

L'interface doit être entièrement en français.

Évite les pages surchargées, les statistiques décoratives et les boutons sans fonctionnement.

22. Architecture technique et qualité du code

Le projet existant utilise Laravel et MySQL.

Tu dois conserver les technologies et l'architecture existantes lorsqu'elles sont fonctionnelles.

Respecte les conventions Laravel.

Organise correctement :

Models.
Controllers.
Requests.
Policies.
Middleware.
Services.
Jobs.
Events.
Notifications.
Resources.
Views.
Routes.
Migrations.
Tests.

Évite les contrôleurs gigantesques.

Évite de répéter les mêmes règles métier dans plusieurs fichiers.

Centralise les opérations complexes dans des services lorsque cela améliore la maintenabilité.

Utilise les transactions pour les opérations impliquant plusieurs tables.

Ajoute les index et contraintes nécessaires à la cohérence des données.

N'ajoute pas de dépendance inutile.

23. Tests fonctionnels et techniques

Tu dois tester le fonctionnement réel de tous les modules.

Authentification
Connexion avec matricule.
Activation du compte.
Création du premier mot de passe.
Refus d'un matricule inexistant.
Refus d'un compte désactivé.
Refus d'un matricule déjà attribué.
Protection contre les tentatives répétées.
Élèves
Consultation du profil.
Consultation de l'horaire.
Consultation des notes.
Consultation du bulletin.
Consultation des paiements.
Refus de modification des informations protégées.
Refus d'accès aux données d'autres élèves.
Enseignants
Consultation des classes.
Consultation des matières.
Création d'évaluations.
Saisie des notes.
Publication des résultats.
Enregistrement des présences.
Création des fiches de préparation.
Préfet
Consultation des horaires.
Examen des fiches.
Consultation des statistiques.
Production des rapports.
Discipline
Consultation des absences.
Enregistrement des incidents.
Gestion des convocations.
Gestion des notes de conduite.
Direction
Création des utilisateurs.
Gestion des classes.
Gestion des matières.
Gestion des affectations.
Gestion des années scolaires.
Validation des bulletins.
Gestion des opérations administratives autorisées.
Finance
Création des frais.
Enregistrement des paiements.
Paiements partiels.
Calcul des soldes.
Génération des reçus.
Contrôle des permissions financières.
Sécurité

Teste systématiquement les accès interdits.

Un utilisateur ne doit jamais obtenir une permission simplement parce qu'il connaît une URL.

Qualité
Tests automatisés.
Tests de régression.
Vérification des routes.
Vérification des migrations.
Vérification des relations.
Vérification des erreurs.
Vérification de l'interface mobile.

Utilise des données de test isolées.

Ne détruis jamais la base de données existante pour effectuer des tests.

24. Règles absolues de reprise du projet

Tu dois reprendre le projet existant, pas le reconstruire.

Avant toute modification :

Examine le projet.
Examine la base de données.
Examine les migrations.
Examine les routes.
Examine les contrôleurs.
Examine les modèles.
Examine les vues.
Examine les tests.
Identifie les fonctionnalités déjà opérationnelles.
Établis un rapport d'audit.

Ne supprime aucune donnée existante.

N'exécute jamais :

php artisan migrate:fresh
php artisan db:wipe

Ne supprime pas les migrations historiques.

Ne remplace pas les données réelles par des données de démonstration.

Ne modifie pas les identifiants existants sans autorisation.

Toute migration doit être conçue pour préserver les données.

Si une opération risque de provoquer une perte de données, arrête-toi et demande une confirmation.

25. Ordre obligatoire d'exécution

Tu dois réaliser la mission dans cet ordre :

Phase	Travail
1	Audit complet du projet
2	Diagnostic de la base de données
3	Vérification de l'architecture
4	Correction de l'authentification
5	Gestion des matricules et comptes
6	Rôles et permissions
7	Gestion des élèves et personnels
8	Années scolaires et classes
9	Matières et affectations
10	Horaires
11	Évaluations et notes
12	Présences
13	Fiches de préparation
14	Discipline
15	Finance
16	Bulletins PDF
17	Notifications
18	Tableaux de bord
19	Rapports et exports
20	Journalisation et audit
21	Interface responsive
22	Tests complets
23	Corrections finales
24	Rapport de livraison

Une phase ne doit pas être considérée comme terminée sans vérification.

26. Rapport final de livraison

À la fin de la mission, tu dois produire un rapport détaillé.

Il doit préciser :

A. État initial
Architecture existante.
Modules présents.
Modules fonctionnels.
Modules incomplets.
Problèmes identifiés.
B. Modifications
Fichiers créés.
Fichiers modifiés.
Modèles créés ou corrigés.
Contrôleurs créés ou corrigés.
Routes créées ou modifiées.
Migrations exécutées.
Permissions ajoutées.
C. Fonctionnalités

Pour chaque module, indique :

Fonctionnalité implémentée.
Fonctionnalité testée.
Résultat du test.
Problèmes restants.
D. Sécurité
Contrôles d'accès.
Permissions par rôle.
Protection des données.
Gestion des sessions.
Journalisation.
E. Configuration
Configuration PDF.
Configuration des notifications.
Configuration des e-mails.
Configuration des sauvegardes.
F. Guide d'utilisation

Explique précisément comment :

Créer un élève.
Créer son matricule.
Générer son code d'activation.
Activer son compte.
Créer un enseignant.
Lui attribuer ses matières et classes.
Se connecter avec chaque rôle.
Créer une évaluation.
Saisir et publier les notes.
Générer un bulletin.
Enregistrer un paiement.
Consulter les rapports.

Ne déclare pas le projet terminé si des fonctionnalités importantes ne sont pas opérationnelles.

27. CONSIGNE FINALE : TA MISSION COMMENCE MAINTENANT

Tu dois comprendre que EduSpace est un système de gestion scolaire complet et intégré, et non un simple tableau de bord administratif.

Je veux une application professionnelle, fiable, sécurisée, évolutive et utilisable dans un établissement scolaire réel en République démocratique du Congo.

Tu dois privilégier :

La fiabilité des données.
La sécurité des utilisateurs.
La cohérence de la base de données.
Le fonctionnement réel des fonctionnalités.
La simplicité d'utilisation.
La qualité de l'interface.
La maintenabilité du code.

Tu disposes d'une grande autonomie technique pour prendre les décisions adaptées, à condition de respecter les règles métier et de préserver les données existantes.

PREMIÈRE ACTION OBLIGATOIRE :

Commence par examiner intégralement le projet EduSpace existant.

Ne modifie aucun fichier immédiatement.

Présente d'abord :

Ton diagnostic technique.
Ton diagnostic fonctionnel.
Les problèmes de sécurité.
Les fonctionnalités manquantes.
Ton plan de travail détaillé.

Attends la validation du plan avant d'entreprendre les modifications.

Ensuite, exécute la mission méthodiquement jusqu'à obtenir une plateforme réellement opérationnelle.

Le résultat attendu n'est pas une démonstration visuelle : c'est un véritable logiciel de gestion scolaire prêt à être utilisé dans un établissement.
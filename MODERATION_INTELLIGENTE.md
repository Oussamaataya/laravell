# 🛡️ Système de Modération Intelligente

## Vue d'ensemble

Le système de modération intelligente améliore considérablement la détection des contenus inappropriés dans la gestion des publications et commentaires. Il utilise une approche multicouche avec analyse contextuelle, détection de contournements et scoring adaptatif.

## 🚀 Fonctionnalités principales

### ✨ Détection intelligente
- **Patterns avancés** : Détection de mots inappropriés avec regex sophistiquées
- **Contournements** : Détection du l33t speak (m3rd3, c0nn4rd, etc.)
- **Variantes** : Espaces, tirets, répétitions de caractères
- **Analyse contextuelle** : Évaluation du ton agressif, harcèlement, spam

### 📊 Système de scoring
- **Score de sévérité** (0-100) basé sur les mots détectés et le contexte
- **Seuils configurables** pour différents niveaux d'action
- **Confiance** dans la détection pour éviter les faux positifs
- **Modificateurs utilisateur** selon la réputation et l'historique

### 🎯 Actions adaptatives
- **Approuver** (score < 40) : Contenu sûr
- **Avertir** (40-69) : Contenu potentiellement problématique
- **Réviser** (70-89) : Contenu nécessitant une révision manuelle
- **Bloquer** (90+) : Contenu violant les conditions d'utilisation

## 🔧 Configuration

### Fichier de configuration : `config/moderation.php`

```php
// Seuil de sévérité principal
'severity_threshold' => 70,

// Mode strict (plus restrictif)
'strict_mode' => false,

// Utilisateurs de confiance (seuils réduits)
'trusted_users' => [
    'enabled' => true,
    'threshold_reduction' => 20,
    'roles' => ['admin', 'moderator', 'verified']
]
```

### Variables d'environnement

```env
MODERATION_SEVERITY_THRESHOLD=70
MODERATION_STRICT_MODE=false
MODERATION_ENABLE_SUGGESTIONS=true
MODERATION_LOG_DETECTIONS=true
```

## 📁 Structure des services

### `IntelligentModerationService`
Service principal de modération avec :
- Normalisation du texte
- Détection de patterns
- Analyse contextuelle
- Calcul de scores
- Génération de suggestions

### `CommentToneService` (amélioré)
Analyse du ton avec intégration de la modération intelligente :
- Détection de sentiment
- Analyse contextuelle
- Facteurs de modération

### `PublicationModerationService`
Service spécialisé pour les publications :
- Analyse titre + contenu
- Gestion des utilisateurs de confiance
- Actions selon les scores
- Historique de modération

## 🎨 Interface utilisateur

### Feedback en temps réel
- **Barre de progression** pendant l'analyse
- **Indicateurs visuels** selon le niveau de risque
- **Suggestions** de remplacement automatiques
- **Messages contextuels** adaptatifs

### Codes couleur
- 🟢 **Vert** (Sûr) : Score < 30
- 🟡 **Jaune** (Attention) : Score 30-50
- 🟠 **Orange** (Risqué) : Score 50-70
- 🔴 **Rouge** (Bloqué) : Score > 70

## 🔍 Catégories de détection

### Profanité (`profanity`)
- Langage vulgaire et grossier
- Poids : 1.0
- Exemples : merde, putain, connard

### Insultes (`insults`)
- Attaques personnelles
- Poids : 0.8
- Exemples : con, idiot, crétin

### Discours de haine (`hate_speech`)
- Discrimination et haine
- Poids : 1.5
- Exemples : ta mère, fdp

### Contenu sexuel (`sexual`)
- Références sexuelles explicites
- Poids : 1.2

### Harcèlement (`harassment`)
- Menaces et intimidation
- Poids : 1.3

## 🧪 Tests et validation

### Commande de test
```bash
# Test automatique avec suite de cas
php artisan moderation:test

# Mode interactif pour vos propres textes
php artisan moderation:test --interactive
```

### Exemples de détection

#### Contournements détectés ✅
- `c0nn4rd` → `connard`
- `m3rd3` → `merde`
- `c o n n a r d` → `connard`
- `connnnard` → `connard`

#### Faux positifs évités ✅
- `console de jeu` ≠ insulte
- `connection internet` ≠ insulte
- `con comme un balai` → expression idiomatique

## 📈 Métriques et monitoring

### Statistiques disponibles
- Nombre total de patterns
- Catégories actives
- Seuils configurés
- Règles contextuelles

### Logging (optionnel)
```php
'log_detections' => true  // Log toutes les détections pour amélioration
```

## 🔄 Intégration

### Publications
1. **Création** : Analyse automatique avant sauvegarde
2. **Temps réel** : Feedback pendant la saisie (AJAX)
3. **Suggestions** : Propositions d'amélioration

### Commentaires
1. **Validation** : Blocage automatique des contenus inappropriés
2. **Feedback** : Messages d'erreur explicatifs
3. **Alternatives** : Suggestions de remplacement

## 🛠️ Personnalisation

### Ajouter de nouveaux patterns
```php
// Dans IntelligentModerationService::initializeBadWordsPatterns()
'nouvelle_categorie' => [
    '/pattern_regex/iu' => 75,  // Sévérité
]
```

### Modifier les seuils
```php
// Dans config/moderation.php
'actions' => [
    'medium' => [
        'action' => 'warn',
        'message' => 'Votre message personnalisé'
    ]
]
```

### Whitelist personnalisée
```php
'whitelist' => [
    'expressions' => [
        'nouvelle expression acceptable'
    ]
]
```

## 🚀 Améliorations futures

- [ ] Intégration IA externe (OpenAI Moderation)
- [ ] Apprentissage automatique sur les faux positifs
- [ ] Détection d'images inappropriées
- [ ] Modération multilingue
- [ ] API de modération externe

## 📞 Support

Pour toute question ou amélioration :
1. Consultez les logs de modération
2. Testez avec la commande interactive
3. Ajustez les seuils selon vos besoins
4. Personnalisez les patterns pour votre contexte

---

*Système développé pour améliorer la qualité des échanges tout en préservant la liberté d'expression* 🛡️✨

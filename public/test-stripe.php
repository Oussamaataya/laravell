<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Intégration Stripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Charger les variables d'environnement depuis le fichier .env
    $envFile = dirname(__DIR__) . '/.env';
    $stripeKey = '';
    $stripeSecret = '';
    
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, 'STRIPE_KEY=') === 0) {
                $stripeKey = trim(substr($line, strlen('STRIPE_KEY=')));
            }
            if (strpos($line, 'STRIPE_SECRET=') === 0) {
                $stripeSecret = trim(substr($line, strlen('STRIPE_SECRET=')));
            }
        }
    }
    
    $isTest = strpos($stripeKey, 'test') !== false;
    ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">✅ Test Intégration Stripe</h3>
                    </div>
                    <div class="card-body">
                        <h5>Configuration Stripe :</h5>
                        <ul class="list-group mb-4">
                            <li class="list-group-item">
                                <strong>Clé Publique:</strong> 
                                <code><?php echo $stripeKey ? substr($stripeKey, 0, 20) . '...' : 'Non configurée'; ?></code>
                                <?php echo $stripeKey ? '✅' : '❌'; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Clé Secrète:</strong> 
                                <code><?php echo $stripeSecret ? substr($stripeSecret, 0, 20) . '...' : 'Non configurée'; ?></code>
                                <?php echo $stripeSecret ? '✅' : '❌'; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Mode:</strong> 
                                <?php echo $isTest ? '<span class="badge bg-success">TEST (recommandé) ✅</span>' : '<span class="badge bg-warning">PRODUCTION ⚠️</span>'; ?>
                            </li>
                        </ul>

                        <h5>Prochaines étapes :</h5>
                        <ol>
                            <li>Connectez-vous à votre compte</li>
                            <li>Visitez la page d'une campagne de collecte</li>
                            <li>Cliquez sur "Effectuer le Don Sécurisé"</li>
                            <li>Vous serez redirigé vers Stripe Checkout</li>
                            <li>Utilisez une carte de test : <code>4242 4242 4242 4242</code></li>
                            <li>Vérifiez le paiement dans votre <a href="https://dashboard.stripe.com/test/payments" target="_blank">Dashboard Stripe</a></li>
                        </ol>

                        <div class="alert alert-info mt-4">
                            <h6>📝 Cartes de Test Stripe :</h6>
                            <ul class="mb-0">
                                <li><strong>Succès:</strong> 4242 4242 4242 4242</li>
                                <li><strong>Échec:</strong> 4000 0000 0000 0002</li>
                                <li><strong>3D Secure:</strong> 4000 0027 6000 3184</li>
                                <li>Date: N'importe quelle date future (ex: 12/25)</li>
                                <li>CVC: 123</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="/collectes" class="btn btn-success btn-lg">
                                🎯 Voir les Campagnes
                            </a>
                            <a href="https://dashboard.stripe.com/test/payments" target="_blank" class="btn btn-outline-primary">
                                💳 Ouvrir Dashboard Stripe
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <small class="text-muted">
                            <strong>Note:</strong> En mode TEST, tous les paiements sont simulés. 
                            Aucun argent réel n'est débité.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

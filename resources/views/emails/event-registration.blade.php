<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-message {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .event-details {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .event-details h3 {
            color: #667eea;
            margin-top: 0;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            width: 140px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #333;
            flex: 1;
        }
        .ticket-info {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .ticket-code {
            background-color: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 30px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Inscription Confirmée !</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Welcome Message -->
            <div class="welcome-message">
                <h2 style="color: #667eea; margin-top: 0;">Bienvenue {{ $user->name }} ! 👋</h2>
                <p style="margin: 0;">
                    Nous sommes ravis de confirmer votre inscription à notre événement. 
                    Votre participation est importante pour nous !
                </p>
            </div>

            <!-- Event Details -->
            <div class="event-details">
                <h3>📅 Détails de l'Événement</h3>
                
                <div class="detail-row">
                    <span class="detail-label">🎯 Événement :</span>
                    <span class="detail-value"><strong>{{ $event->title }}</strong></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">📆 Date :</span>
                    <span class="detail-value">{{ $event->start_date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">🕐 Heure :</span>
                    <span class="detail-value">{{ $event->start_time }} - {{ $event->end_time }}</span>
                </div>

                @if($event->is_online)
                    <div class="detail-row">
                        <span class="detail-label">💻 Format :</span>
                        <span class="detail-value">Événement en ligne</span>
                    </div>
                @else
                    <div class="detail-row">
                        <span class="detail-label">📍 Lieu :</span>
                        <span class="detail-value">{{ $event->location }}, {{ $event->city }}</span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">💰 Prix :</span>
                    <span class="detail-value">{{ $event->is_free ? 'Gratuit' : number_format($event->price, 2) . ' €' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">🎫 Statut :</span>
                    <span class="detail-value" style="color: #28a745; font-weight: bold;">✅ Confirmé</span>
                </div>
            </div>

            @if($registration->ticket_code)
            <!-- Ticket Info -->
            <div class="ticket-info">
                <h3 style="margin-top: 0;">🎫 Votre Billet Électronique</h3>
                <p style="margin: 10px 0;">Présentez ce code à l'entrée de l'événement</p>
                <div class="ticket-code">
                    {{ $registration->ticket_code }}
                </div>
                <p style="margin: 10px 0; font-size: 14px;">
                    @if($registration->qr_code_path)
                        Un QR Code est joint à cet email pour un accès rapide.
                    @endif
                </p>
            </div>
            @endif

            <div class="divider"></div>

            <!-- Call to Action -->
            <div style="text-align: center;">
                <p style="font-size: 18px; color: #333; margin-bottom: 20px;">
                    Prêt à participer ? Consultez tous les détails !
                </p>
                <a href="{{ route('events.my-registrations') }}" class="button">
                    Voir Mes Inscriptions
                </a>
            </div>

            <!-- Additional Info -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>📌 Rappels Importants :</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Arrivez 15 minutes avant le début de l'événement</li>
                    <li>Gardez votre billet à portée de main</li>
                    <li>En cas d'empêchement, annulez votre inscription depuis votre compte</li>
                    @if($event->is_online)
                        <li>Le lien de connexion vous sera envoyé 24h avant l'événement</li>
                    @endif
                </ul>
            </div>

            <!-- Description -->
            @if($event->description)
            <div style="margin: 20px 0;">
                <h3 style="color: #667eea;">À propos de l'événement</h3>
                <p style="color: #666; line-height: 1.8;">
                    {{ Str::limit($event->description, 300) }}
                </p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-weight: 600; color: #333; margin-bottom: 10px;">
                Besoin d'aide ?
            </p>
            <p style="margin: 10px 0;">
                Contactez-nous à : <a href="mailto:{{ config('mail.from.address') }}" style="color: #667eea;">{{ config('mail.from.address') }}</a>
            </p>
            
            <div class="divider" style="margin: 20px auto; max-width: 200px;"></div>
            
            <p style="margin: 20px 0;">
                <strong>ECO EVENT</strong><br>
                Votre plateforme d'événements éco-responsables
            </p>
            
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                Vous recevez cet email car vous vous êtes inscrit à un événement sur notre plateforme.<br>
                © {{ date('Y') }} ECO EVENT. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Akevas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .welcome-message {
            margin-bottom: 30px;
        }
        
        .welcome-message h2 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .welcome-message p {
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .user-info {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #667eea;
        }
        
        .user-info h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 120px;
        }
        
        .info-value {
            color: #2d3748;
            font-weight: 500;
        }
        
        .cta-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            background-color: #2d3748;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .footer p {
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        
        .highlight {
            background: linear-gradient(120deg, #a8edea 0%, #fed6e3 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        
        .highlight h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .highlight p {
            color: #4a5568;
            font-size: 14px;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .info-label {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Bienvenue sur Akevas !</h1>
            <p>Votre compte a été créé avec succès</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="welcome-message">
                <h2>Bonjour {{ $user['firstName'] ?? $user['userName'] }} !</h2>
                <p>Nous sommes ravis de vous accueillir sur notre plateforme. Votre compte a été créé avec succès et vous pouvez dès maintenant profiter de tous nos services.</p>
            </div>
            
            <!-- User Information -->
            <div class="user-info">
                <h3>📋 Vos informations de compte</h3>
                <div class="info-row">
                    <span class="info-label">Nom d'utilisateur :</span>
                    <span class="info-value">{{ $user['userName'] ?? 'Non défini' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nom complet :</span>
                    <span class="info-value">{{ $user['firstName'] ?? '' }} {{ $user['lastName'] ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">{{ $user['phone_number'] ?? 'Non défini' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span class="info-value">{{ $user['email'] ?? 'Non défini' }}</span>
                </div>
            </div>
            
            <!-- Highlight Section -->
            <div class="highlight">
                <h3>🚀 Prêt à commencer ?</h3>
                <p>Connectez-vous à votre compte et découvrez toutes les fonctionnalités disponibles sur notre plateforme.</p>
            </div>
            
            <!-- Call to Action -->
            <div class="cta-section">
                <a href="{{ config('app.url') }}/login" class="cta-button">
                    Se connecter maintenant
                </a>
            </div>
            
            <!-- Additional Information -->
            <div style="margin-top: 30px; padding: 20px; background-color: #f0f9ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <h4 style="color: #1e40af; margin-bottom: 10px;">💡 Prochaines étapes :</h4>
                <ul style="color: #1e3a8a; padding-left: 20px;">
                    <li>Complétez votre profil avec vos informations personnelles</li>
                    <li>Vérifiez votre adresse email pour activer votre compte</li>
                    <li>Explorez nos services et fonctionnalités</li>
                    <li>Contactez notre support si vous avez des questions</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Akevas</strong> - Votre plateforme de confiance</p>
            <p>Merci de nous faire confiance pour vos besoins</p>
            <div class="social-links">
                <a href="#">Support</a> |
                <a href="#">Aide</a> |
                <a href="#">Contact</a>
            </div>
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.8;">
                Cet email a été envoyé à {{ $user['email'] ?? 'votre adresse email' }}
            </p>
        </div>
    </div>
</body>
</html>

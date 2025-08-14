<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau client - Notification Admin</title>
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
            background-color: #f8f9fa;
        }
        
        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            border-bottom: 4px solid #e74c3c;
        }
        
        .header h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 15px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .alert-badge {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .notification-summary {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #f39c12;
        }
        
        .notification-summary h2 {
            color: #856404;
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .notification-summary p {
            color: #856404;
            font-size: 15px;
            margin: 0;
        }
        
        .client-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #e9ecef;
        }
        
        .client-info h3 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-item {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #3498db;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #2c3e50;
            font-weight: 500;
            font-size: 15px;
        }
        
        .timestamp {
            background-color: #e8f4fd;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            border-left: 3px solid #3498db;
        }
        
        .timestamp h4 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .timestamp p {
            color: #495057;
            font-size: 14px;
            margin: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #229954;
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            transform: translateY(-1px);
        }
        
        .admin-actions {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid #dee2e6;
        }
        
        .admin-actions h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .action-list {
            list-style: none;
            padding: 0;
        }
        
        .action-list li {
            padding: 8px 0;
            color: #495057;
            font-size: 14px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .action-list li:last-child {
            border-bottom: none;
        }
        
        .action-list li:before {
            content: "•";
            color: #3498db;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .footer p {
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .footer-links {
            margin-top: 15px;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            margin: 0 15px;
            font-size: 12px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .priority-indicator {
            background-color: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-left: 10px;
        }
        
        @media (max-width: 700px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="alert-badge">Nouveau Client</div>
            <h1>🔔 Notification Administrative</h1>
            <p>Un nouveau client vient de s'inscrire sur la plateforme Akevas</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Notification Summary -->
            <div class="notification-summary">
                <h2>📊 Résumé de l'inscription</h2>
                <p>Un nouvel utilisateur a créé son compte sur la plateforme. Veuillez examiner les informations ci-dessous et prendre les actions nécessaires.</p>
            </div>
            
            <!-- Client Information -->
            <div class="client-info">
                <h3>👤 Informations du nouveau client <span class="priority-indicator">Nouveau</span></h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nom d'utilisateur</div>
                        <div class="info-value">{{ $user['userName'] ?? 'Non défini' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $user['firstName'] ?? '' }} {{ $user['lastName'] ?? '' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Numéro de téléphone</div>
                        <div class="info-value">{{ $user['phone_number'] ?? 'Non défini' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Adresse email</div>
                        <div class="info-value">{{ $user['email'] ?? 'Non défini' }}</div>
                    </div>
                   
                </div>
            </div>
            
            <!-- Timestamp -->
            <div class="timestamp">
                <h4>⏰ Détails temporels</h4>
                <p><strong>Date d'inscription :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
                <p><strong>Heure :</strong> {{ now()->format('H:i:s') }} ({{ config('app.timezone') }})</p>
            </div>
            
            <!-- Admin Actions -->
            <div class="admin-actions">
                <h3>⚡ Actions recommandées</h3>
                <ul class="action-list">
                    <li>Vérifier la validité des informations fournies</li>
                    <li>Contacter le client pour confirmer son inscription</li>
                    <li>Vérifier les documents d'identité si nécessaire</li>
                    <li>Attribuer les permissions appropriées selon le rôle</li>
                    <li>Ajouter des notes internes si nécessaire</li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ config('app.url') }}/admin/users/{{ $user['id'] ?? '' }}" class="btn btn-primary">
                    👁️ Voir le profil complet
                </a>
                <a href="{{ config('app.url') }}/admin/users" class="btn btn-success">
                    📋 Gérer les utilisateurs
                </a>
                <a href="mailto:{{ $user['email'] ?? '' }}" class="btn btn-warning">
                    ✉️ Contacter le client
                </a>
            </div>
            
            <!-- Additional Notes -->
            <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #6c757d;">
                <h4 style="color: #495057; margin-bottom: 10px; font-size: 16px;">📝 Notes importantes :</h4>
                <ul style="color: #6c757d; padding-left: 20px; font-size: 14px;">
                    <li>Cet email est envoyé automatiquement lors de chaque nouvelle inscription</li>
                    <li>Le client a reçu un email de bienvenue séparé</li>
                    <li>Vérifiez les paramètres de sécurité et les permissions</li>
                    <li>Consultez les logs pour plus de détails sur l'inscription</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>🔐 Akevas - Panel Administratif</strong></p>
            <p>Notification automatique - Ne pas répondre à cet email</p>
            <div class="footer-links">
                <a href="{{ config('app.url') }}/admin">Dashboard Admin</a> |
                <a href="{{ config('app.url') }}/admin/settings">Paramètres</a> |
                <a href="{{ config('app.url') }}/admin/logs">Logs système</a>
            </div>
            <p style="margin-top: 15px; font-size: 11px; opacity: 0.7;">
                Email généré automatiquement le {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>

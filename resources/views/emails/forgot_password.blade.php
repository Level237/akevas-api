<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération de mot de passe - Akevas</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="400" style="border-collapse: collapse; border: 1px solid #cccccc; background-color: #ffffff; border-radius: 15px; overflow: hidden;">
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0; background-color: #ffffff;">
                            <h1 style="color: #ed7e0f; margin: 0; font-size: 28px; font-weight: bold;">AKEVAS</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-size: 18px; font-weight: bold; padding-bottom: 20px;">
                                        Réinitialisation de votre mot de passe
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #444444; font-size: 16px; line-height: 24px; padding-bottom: 30px;">
                                        Bonjour,<br><br>
                                        Vous avez demandé la réinitialisation de votre mot de passe sur Akevas. Utilisez le code de vérification ci-dessous pour continuer :
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <div style="background-color: #f8f9fa; border: 2px dashed #ed7e0f; border-radius: 10px; padding: 20px; display: inline-block;">
                                            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333333;">
                                                {{ $otp }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #666666; font-size: 14px; line-height: 20px; padding-top: 30px; text-align: center;">
                                        Ce code est valide pendant <strong>15 minutes</strong>. Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; background-color: #ed7e0f;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #ffffff; font-size: 12px; text-align: center;">
                                        &copy; {{ date('Y') }} Akevas. Tous droits réservés.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
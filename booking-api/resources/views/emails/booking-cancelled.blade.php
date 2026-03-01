<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .info-box { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .footer { background: #f8fafc; padding: 20px 30px; text-align: center; color: #94a3b8; font-size: 13px; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
        <h1>Réservation annulée</h1>
    </div>

    <div class="body">
        <p>Bonjour <strong>{{ $booking->user->name }}</strong>,</p>
        <p>Votre réservation a été annulée. Voici le récapitulatif :</p>

        <div class="info-box">
            <div class="info-row">
                <span>🏨 Hébergement</span>
                <span>{{ $booking->resource->name }}</span>
            </div>
            <div class="info-row">
                <span>📅 Arrivée</span>
                <span>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>📅 Départ</span>
                <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/resources" class="btn">
                Faire une nouvelle réservation
            </a>
        </div>
    </div>

    <div class="footer">
        <p>© 2026 BookingApp — Tous droits réservés</p>
    </div>
</div>
</body>
</html>

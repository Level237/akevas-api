<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        .button {
            background-color: #ed7e0f;
            color: white !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .table td {
            border-bottom: 1px solid #eee;
            padding: 10px;
            font-size: 14px;
        }

        .badge-warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>

<body style="font-family: 'Segoe UI', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9;">
    <table class="table">
        <thead>
            <tr>
                <th style="width: 50%;">Produit / Variante</th>
                <th style="text-align: center;">Stock actuel</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $item)
            <tr>
                <td style="color: #333; font-weight: 500;">
                    {{ $item['name'] }}
                </td>
                <td style="text-align: center;">
                    <span style="font-size: 16px; font-weight: bold; {{ $item['qty'] <= 3 ? 'color: #d9534f;' : '' }}">
                        {{ $item['qty'] }}
                    </span>
                    @if($item['qty'] <= 3)
                        <div style="font-size: 10px; color: #d9534f; font-weight: bold; text-transform: uppercase;">
                        Critique
                        </div>
                        @endif
                </td>
                <td style="text-align: center;">
                    @if(isset($item['url']))
                    <a href="https://seller.akevas.com/seller/edit/product/{{ $item['url'] }}" class="button" style="padding: 6px 12px; font-size: 12px;">
                        Voir
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center; margin: 30px 0;">
        <a href="https://seller.akevas.com/seller/products" class="button">
            Voir tous mes produits
        </a>
    </div>
</body>

</html>
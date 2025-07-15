<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update :: {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-spacing: 0;
        }
        td {
            padding: 0;
            vertical-align: top;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            display: block;
            max-width: 100%;
            height: auto;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        p {
            margin: 0 0 1em 0;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0 0 0.8em 0;
            font-weight: bold;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 20px 0;
        }
        .details {
            background-color: #f9f9f9;
            border-left: 5px solid #007bff;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .details p {
            margin: 0 0 8px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #666666;
            border-top: 1px solid #eeeeee;
            padding-top: 10px;
        }
        .button {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        /* Responsive styles (less reliable in all clients, but good to have) */
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content-area {
                padding: 20px !important;
            }
            .header-image {
                width: 100% !important;
                height: auto !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <p>Hello {{ $user->first_name ?? $user->email }},</p>
            <p>{{ "Your order with ID: {$this->order->order_id} has been {$this->status}" }}</p>

            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
                <tr>
                    <td align="center" style="padding: 20px 0;">
                        <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="padding: 20px; text-align: center; background-color: #007bff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        Product
                                    </th>
                                    <th style="padding: 20px; text-align: center; background-color: #007bff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        Unit * Quantity
                                    </th>
                                    <th style="padding: 20px; text-align: center; background-color: #007bff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        Sub-Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($items as $item)
                                    <tr>
                                        <td><img src="{{ $item->product->image }}" width="200" alt=""></td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->product->unit_price }} * {{ $item->product->quantity }}</td>
                                        <td>
                                            @php
                                                $sub_total = $item->product->unit_price * $item->product->quantity;
                                                $tot_arr += $sub_total;
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>            
                                    <th>Total</th>
                                    <td>{{ $total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <p>Thanks,</p>
            <p>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .order-details {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
        }
        .order-details h2 {
            margin-top: 0;
            color: #111827;
            font-size: 18px;
            margin-bottom: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
        }
        .detail-value {
            font-weight: 600;
            color: #111827;
        }
        .download-button {
            display: inline-block;
            background-color: #f59e0b;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-weight: 600;
            margin: 24px 0;
        }
        .download-button:hover {
            background-color: #d97706;
        }
        .notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            background-color: #1f2937;
            padding: 30px;
            text-align: center;
        }
        .footer p {
            color: #9ca3af;
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Thank You for Your Purchase!</h1>
            </div>
            
            <div class="content">
                <p>Hi {{ $order->customer_name }},</p>
                
                <p>Thank you for your purchase! We're excited to confirm that your order has been successfully processed.</p>
                
                <div class="order-details">
                    <h2>Order Information</h2>
                    <div class="detail-row">
                        <span class="detail-label">Order Number</span>
                        <span class="detail-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Product</span>
                        <span class="detail-value">{{ $order->product_name }}</span>
                    </div>
                    @if($order->quantity > 1)
                    <div class="detail-row">
                        <span class="detail-label">Quantity</span>
                        <span class="detail-value">{{ $order->quantity }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid</span>
                        <span class="detail-value" style="color: #f59e0b; font-size: 18px;">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date</span>
                        <span class="detail-value">{{ $order->paid_at->format('F d, Y g:i A') }}</span>
                    </div>
                </div>
                
                @if($order->canDownload())
                <div style="text-align: center;">
                    <a href="{{ route('order.download', $order->download_token) }}" class="download-button">
                        <i class="bi bi-download"></i> Download Your Product
                    </a>
                </div>
                
                <div class="notice">
                    <strong><i class="bi bi-exclamation-triangle"></i> Important:</strong> Your download link will expire in 24 hours. Please make sure to download your product immediately. If you encounter any issues, please contact our support team.
                </div>
                @elseif($order->product && $order->product->product_type === 'digital')
                <p>Your download link will be sent to you shortly. Please check your email for the download link.</p>
                @endif
                
                <p>If you have any questions about your order, please don't hesitate to contact our support team.</p>
                
                <p>Thank you for shopping with us!</p>
                
                <p style="margin-top: 30px;">
                    Best regards,<br>
                    <strong>{{ config('app.name', 'Online Store') }}</strong>
                </p>
            </div>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Online Store') }}. All rights reserved.</p>
                <p style="margin-top: 8px;">
                    This is an automated email. Please do not reply directly to this message.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
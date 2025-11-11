<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Finance Report - {{ $league->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('https://fonts.gstatic.com/s/dejavu/v1/DejaVuSans.ttf') format('truetype');
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        /* Header Section */
        .header {
            display: flex;
            background: #2d5a5a;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .company-section {
            flex: 1;
            padding: 30px;
            background: #2d5a5a;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .invoice-section {
            flex: 1;
            padding: 30px;
            background: #2d5a5a;
            position: relative;
        }
        
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            position: relative;
        }
        
        .invoice-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: -20px;
            width: 0;
            height: 0;
            border-left: 20px solid #2d5a5a;
            border-top: 20px solid transparent;
            border-bottom: 20px solid transparent;
        }
        
        .invoice-details {
            background: white;
            color: #000;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .invoice-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .invoice-detail-label {
            font-weight: bold;
            color: #2d5a5a;
        }
        
        /* Main Content */
        .main-content {
            padding: 40px;
            background: white;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d5a5a;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .invoice-to {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .invoice-to-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .contact-info {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
        }
        
        .payment-method {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        /* Transactions Table */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .transactions-table th {
            background: #2d5a5a;
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .transactions-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        
        .transactions-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .amount-income {
            color: #28a745;
            font-weight: bold;
        }
        
        .amount-expense {
            color: #dc3545;
            font-weight: bold;
        }
        
        /* Bottom Section */
        .bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .terms-section {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }
        
        .thank-you {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            text-transform: uppercase;
        }
        
        .summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .summary-total {
            background: #2d5a5a;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .summary-total-row {
            display: flex;
            justify-content: space-between;
        }
        
        .signature-section {
            margin-top: 30px;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 0 auto 10px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        
        /* Footer */
        .footer {
            background: #2d5a5a;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }
        
        .footer-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-icon {
            width: 16px;
            height: 16px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-style: italic;
        }
        
        .no-data-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-section">
                <div class="company-logo">M</div>
                <div class="company-name">MAKEMYLEAGUE</div>
                <div class="company-tagline">Cricket League Management</div>
            </div>
            <div class="invoice-section">
                <div class="invoice-title">FINANCE REPORT</div>
                <div class="invoice-details">
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Report No:</span>
                        <span>#{{ strtoupper(substr($league->slug, 0, 3)) }}{{ date('Ymd') }}</span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Period:</span>
                        @if($startDate && $endDate)
                            <span>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                        @else
                            <span>All Transactions</span>
                        @endif
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Generated:</span>
                        <span>{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-grid">
                <div>
                    <div class="section-title">League Details</div>
                    <div class="invoice-to">{{ $league->name }}</div>
                    <div class="invoice-to-subtitle">{{ $league->game->name ?? 'Cricket League' }}</div>
                    <div class="contact-info">
                        <div><strong>Season:</strong> {{ $league->season }}</div>
                        <div><strong>Status:</strong> {{ ucfirst($league->status) }}</div>
                        @if($league->start_date && $league->end_date)
                            <div><strong>Duration:</strong> {{ $league->start_date->format('M d, Y') }} to {{ $league->end_date->format('M d, Y') }}</div>
                        @endif
                        @if($league->localBody)
                            <div><strong>Location:</strong> {{ $league->localBody->name }}</div>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="section-title">Financial Summary</div>
                    <div class="payment-method">
                        <div class="payment-row">
                            <span><strong>Total Income:</strong></span>
                            <span class="amount-income">Rs. {{ number_format($totalIncome, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span><strong>Total Expenses:</strong></span>
                            <span class="amount-expense">Rs. {{ number_format($totalExpenses, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span><strong>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}:</strong></span>
                            <span class="{{ $netProfit >= 0 ? 'amount-income' : 'amount-expense' }}">Rs. {{ number_format(abs($netProfit), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            @if($finances->count() > 0)
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">NO.</th>
                            <th style="width: 40%;">TRANSACTION DESCRIPTION</th>
                            <th style="width: 15%;">CATEGORY</th>
                            <th style="width: 10%;">TYPE</th>
                            <th style="width: 15%;">AMOUNT</th>
                            <th style="width: 15%;">DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finances as $index => $finance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $finance->title }}</td>
                                <td>{{ $finance->expenseCategory->name }}</td>
                                <td>
                                    <span style="padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; {{ $finance->type === 'income' ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                                        {{ ucfirst($finance->type) }}
                                    </span>
                                </td>
                                <td class="amount-{{ $finance->type }}">
                                    Rs. {{ number_format($finance->amount, 2) }}
                                </td>
                                <td>{{ $finance->transaction_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <div class="no-data-icon">📊</div>
                    <p>No transactions found for the @if($startDate && $endDate) selected period @else league @endif.</p>
                </div>
            @endif

            <!-- Bottom Section -->
            <div class="bottom-section">
                <div class="terms-section">
                    <div class="section-title">Terms & Conditions</div>
                    <p>This financial report is generated automatically by the MakeMyLeague system. All transactions are recorded with proper documentation and approval from authorized league organizers. The report includes all income and expense transactions for the specified period.</p>
                    <p>For any discrepancies or queries regarding the transactions, please contact the league organizer within 7 days of report generation.</p>
                    <div class="thank-you">Thank you for using MakeMyLeague</div>
                </div>
                <div class="summary-section">
                    <div class="section-title">Summary</div>
                    <div class="summary-row">
                        <span>Total Income:</span>
                        <span class="amount-income">Rs. {{ number_format($totalIncome, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Expenses:</span>
                        <span class="amount-expense">Rs. {{ number_format($totalExpenses, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Transactions Count:</span>
                        <span>{{ $finances->count() }}</span>
                    </div>
                    <div class="summary-total">
                        <div class="summary-total-row">
                            <span>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}:</span>
                            <span>Rs. {{ number_format(abs($netProfit), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">League Organizer Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-section">
                <div class="footer-icon">📞</div>
                <span>Contact: +91 9876543210</span>
            </div>
            <div class="footer-section">
                <div class="footer-icon">✉</div>
                <span>support@makemyleague.com</span>
            </div>
            <div class="footer-section">
                <div class="footer-icon">📍</div>
                <span>www.makemyleague.com</span>
            </div>
        </div>
    </div>
</body>
</html>
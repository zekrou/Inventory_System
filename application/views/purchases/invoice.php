<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - <?php echo $purchase_data['purchase_no']; ?></title>
    <style>
        @media print {
            @page { margin: 0.75cm; size: A4; }
            .no-print { display: none !important; }
            body { background: white; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a1a;
            background: #e8e8e8;
        }
        
        .page-wrapper {
            max-width: 210mm;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 30px rgba(0,0,0,0.15);
        }
        
        .invoice-container {
            padding: 35px 45px;
        }
        
        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 8px;
            margin: -35px -45px 30px -45px;
        }
        
        /* Header */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 35px;
        }
        
        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .company-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .company-info p {
            color: #666;
            font-size: 10px;
            line-height: 1.6;
            margin: 1px 0;
        }
        
        .invoice-meta {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .invoice-meta h2 {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-meta table {
            float: right;
            margin-top: 10px;
        }
        
        .invoice-meta td {
            padding: 4px 0;
            font-size: 10px;
        }
        
        .invoice-meta td:first-child {
            text-align: right;
            padding-right: 12px;
            color: #666;
            font-weight: 500;
        }
        
        .invoice-meta td:last-child {
            font-weight: 700;
            color: #1a1a1a;
        }
        
        /* Print Button */
        .print-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(102, 126, 234, 0.4);
        }
        
        /* Info Boxes */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .info-box {
            display: table-cell;
            width: 48%;
            padding: 20px;
            background: #f8f9fc;
            border-left: 3px solid #667eea;
        }
        
        .info-box:last-child {
            padding-left: 25px;
        }
        
        .info-box h3 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #667eea;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 5px 0;
            font-size: 10px;
            vertical-align: top;
        }
        
        .info-box td:first-child {
            color: #666;
            width: 38%;
            font-weight: 500;
        }
        
        .info-box td:last-child {
            color: #1a1a1a;
            font-weight: 600;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
        }
        
        .status-pending { background: #fff4e6; color: #e67e22; }
        .status-received { background: #e8f5e9; color: #27ae60; }
        .status-cancelled { background: #ffebee; color: #e74c3c; }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        
        .items-table thead {
            background: #f8f9fc;
            border-top: 2px solid #667eea;
            border-bottom: 2px solid #667eea;
        }
        
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            color: #667eea;
            letter-spacing: 0.8px;
        }
        
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e8e8e8;
        }
        
        .items-table td {
            padding: 14px 10px;
            font-size: 10px;
            color: #333;
        }
        
        .items-table td.text-right { text-align: right; }
        .items-table td.text-center { text-align: center; }
        
        .items-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .items-table .product-name {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .items-table tfoot {
            border-top: 2px solid #667eea;
            background: #f8f9fc;
        }
        
        .items-table tfoot td {
            padding: 14px 10px;
            font-weight: 700;
            font-size: 12px;
            color: #1a1a1a;
        }
        
        /* Summary */
        .summary-section {
            float: right;
            width: 360px;
            margin: 25px 0;
        }
        
        .summary-box {
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-box tr:last-child {
            border-bottom: none;
        }
        
        .summary-box td {
            padding: 14px 18px;
            font-size: 11px;
        }
        
        .summary-box td:first-child {
            font-weight: 600;
            color: #666;
        }
        
        .summary-box td:last-child {
            text-align: right;
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .summary-box .total-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .summary-box .total-row td {
            color: white;
            font-size: 14px;
            padding: 16px 18px;
        }
        
        .summary-box .paid-row {
            background: #e8f5e9;
        }
        
        .summary-box .paid-row td {
            color: #27ae60;
            font-weight: 700;
        }
        
        .summary-box .due-row {
            background: #ffebee;
        }
        
        .summary-box .due-row td {
            color: #e74c3c;
            font-weight: 700;
            font-size: 12px;
        }
        
        /* Payment History */
        .payment-history {
            clear: both;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e8e8e8;
        }
        
        .payment-history h3 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .payment-history table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-history thead {
            background: #f8f9fc;
            border-bottom: 2px solid #667eea;
        }
        
        .payment-history th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            color: #667eea;
            letter-spacing: 0.5px;
        }
        
        .payment-history td {
            padding: 12px 10px;
            font-size: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .payment-history td.text-right {
            text-align: right;
        }
        
        /* Notes */
        .notes-box {
            margin: 25px 0;
            padding: 18px;
            background: #fffbf0;
            border-left: 3px solid #f39c12;
            border-radius: 4px;
        }
        
        .notes-box h4 {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notes-box p {
            font-size: 10px;
            color: #555;
            line-height: 1.7;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e8e8e8;
            text-align: center;
        }
        
        .invoice-footer p {
            font-size: 9px;
            color: #999;
            margin: 4px 0;
        }
        
        .invoice-footer .thank-you {
            font-size: 13px;
            font-weight: 600;
            color: #667eea;
            margin-top: 8px;
        }
        
        /* Clearfix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-btn no-print">
        🖨 Print Invoice
    </button>

    <div class="page-wrapper">
        <div class="top-bar"></div>
        <div class="invoice-container">
            
            <!-- Header -->
            <div class="invoice-header">
                <div class="company-info">
                    <?php if(isset($company) && !empty($company)): ?>
                        <h1><?php echo isset($company['company_name']) ? $company['company_name'] : 'LOTFI'; ?></h1>
                        <?php if(isset($company['address'])): ?>
                            <p><?php echo $company['address']; ?></p>
                        <?php else: ?>
                            <p>Lotissement N04, Oued Zenati, Guelma, Algeria</p>
                        <?php endif; ?>
                        <?php if(isset($company['phone'])): ?>
                            <p>Tel: <?php echo $company['phone']; ?></p>
                        <?php else: ?>
                            <p>Tel: 0672657793</p>
                        <?php endif; ?>
                        <?php if(isset($company['email'])): ?>
                            <p>Email: <?php echo $company['email']; ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <h1>LOTFI</h1>
                        <p>Lotissement N04, Oued Zenati, Guelma, Algeria</p>
                        <p>Tel: 0672657793</p>
                    <?php endif; ?>
                </div>
                
                <div class="invoice-meta">
                    <h2>Purchase Order</h2>
                    <table>
                        <tr>
                            <td>PO Number:</td>
                            <td><?php echo $purchase_data['purchase_no']; ?></td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td><?php echo date('d/m/Y', strtotime($purchase_data['purchase_date'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-box">
                    <h3>Supplier Details</h3>
                    <table>
                        <tr>
                            <td>Supplier:</td>
                            <td><?php echo isset($purchase_data['supplier_name']) ? $purchase_data['supplier_name'] : '-'; ?></td>
                        </tr>
                        <?php if(!empty($purchase_data['supplier_phone'])): ?>
                        <tr>
                            <td>Phone:</td>
                            <td><?php echo $purchase_data['supplier_phone']; ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(!empty($purchase_data['supplier_email'])): ?>
                        <tr>
                            <td>Email:</td>
                            <td><?php echo $purchase_data['supplier_email']; ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(!empty($purchase_data['supplier_address'])): ?>
                        <tr>
                            <td>Address:</td>
                            <td><?php echo $purchase_data['supplier_address']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <div class="info-box">
                    <h3>Purchase Information</h3>
                    <table>
                        <tr>
                            <td>Status:</td>
                            <td>
                                <?php if($purchase_data['status'] == 'pending'): ?>
                                    <span class="status-badge status-pending">Pending</span>
                                <?php elseif($purchase_data['status'] == 'received'): ?>
                                    <span class="status-badge status-received">Received</span>
                                <?php else: ?>
                                    <span class="status-badge status-cancelled">Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if($purchase_data['status'] == 'received' && !empty($purchase_data['received_date'])): ?>
                        <tr>
                            <td>Received:</td>
                            <td><?php echo date('d/m/Y', strtotime($purchase_data['received_date'])); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Payment:</td>
                            <td><?php echo ucfirst(isset($purchase_data['payment_status']) ? $purchase_data['payment_status'] : 'Partial'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:42%;">Description</th>
                        <th style="width:13%;">SKU</th>
                        <th class="text-center" style="width:10%;">Quantity</th>
                        <th class="text-right" style="width:15%;">Unit Price</th>
                        <th class="text-right" style="width:15%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach($purchase_items as $item): 
                        $item_total = $item['quantity'] * $item['unit_price'];
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td class="product-name"><?php echo $item['product_name']; ?></td>
                        <td><?php echo isset($item['sku']) ? $item['sku'] : '-'; ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-right"><?php echo number_format($item['unit_price'], 2); ?> DZD</td>
                        <td class="text-right"><?php echo number_format($item_total, 2); ?> DZD</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">TOTAL AMOUNT</td>
                        <td class="text-right"><?php echo number_format($purchase_data['total_amount'], 2); ?> DZD</td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-box">
                    <table>
                        <tr class="total-row">
                            <td>Total Amount</td>
                            <td><?php echo number_format($purchase_data['total_amount'], 2); ?> DZD</td>
                        </tr>
                        <tr class="paid-row">
                            <td>Amount Paid</td>
                            <td><?php echo number_format(isset($purchase_data['paid_amount']) ? $purchase_data['paid_amount'] : 0, 2); ?> DZD</td>
                        </tr>
                        <tr class="due-row">
                            <td>Balance Due</td>
                            <td>
                                <?php 
                                $due = isset($purchase_data['due_amount']) 
                                       ? $purchase_data['due_amount'] 
                                       : ($purchase_data['total_amount'] - (isset($purchase_data['paid_amount']) ? $purchase_data['paid_amount'] : 0));
                                echo number_format($due, 2); 
                                ?> DZD
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <!-- Payment History -->
            <?php if(!empty($payment_history)): ?>
            <div class="payment-history">
                <h3>Payment History</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-right">Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach($payment_history as $payment): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                            <td><?php echo $payment['reference_number'] ?: '-'; ?></td>
                            <td class="text-right"><strong><?php echo number_format($payment['amount_paid'], 2); ?> DZD</strong></td>
                            <td><?php echo $payment['notes'] ?: '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <!-- Notes -->
            <?php if(!empty($purchase_data['notes'])): ?>
            <div class="notes-box">
                <h4>Additional Notes</h4>
                <p><?php echo nl2br(htmlspecialchars($purchase_data['notes'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <div class="invoice-footer">
                <p>This is a computer-generated document.</p>
                <p class="thank-you">Thank you for your business</p>
            </div>
            
        </div>
    </div>
</body>
</html>

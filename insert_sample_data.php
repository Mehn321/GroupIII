<?php
/**
 * Sample Data Insertion Script
 * Populates the database with realistic shopping-related Q&A
 * Run this after setup_db.php to populate sample data
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/KnowledgeBase.php';

// Initialize
$kb = new KnowledgeBase();
$categories = $kb->getCategories();

// Map category names to IDs
$category_map = [];
foreach ($categories as $cat) {
    $category_map[$cat['name']] = $cat['id'];
}

// Sample Q&A data organized by category
$sample_data = [
    'Orders & Tracking' => [
        [
            'question' => 'How can I track my order?',
            'answer' => 'You can track your order in real-time by logging into your account and visiting the "My Orders" section. You\'ll find a tracking number and a link to the carrier\'s tracking page. You can also check your order status via the tracking link sent to your email after shipment.',
            'keywords' => 'track, tracking number, order status, where is my order'
        ],
        [
            'question' => 'How do I know if my order has been placed successfully?',
            'answer' => 'Once you complete your purchase, you\'ll receive an order confirmation email immediately. This email contains your order number, items purchased, total price, and estimated delivery date. You can use the order number to track your package at any time.',
            'keywords' => 'order confirmation, placed, successful, email'
        ],
        [
            'question' => 'Can I modify or cancel my order?',
            'answer' => 'If your order hasn\'t shipped yet, you can modify or cancel it within 1 hour of purchase. Please contact our customer support team immediately with your order number. If the order has already shipped, you\'ll need to refuse delivery or initiate a return once received.',
            'keywords' => 'modify, cancel, change order, delete'
        ],
        [
            'question' => 'What is the order number format?',
            'answer' => 'Your order number is a unique 8-digit code that starts with "ORD-" followed by numbers. For example: ORD-12345678. You can find this number in your confirmation email and in your account dashboard.',
            'keywords' => 'order number, order ID, order code'
        ]
    ],
    'Shipping & Delivery' => [
        [
            'question' => 'How long does shipping take?',
            'answer' => 'Standard shipping typically takes 5-7 business days from the order date. Express shipping takes 2-3 business days. International orders may take 10-21 business days depending on the destination country. Shipping times are estimates and may vary during peak seasons.',
            'keywords' => 'shipping time, delivery time, how long, arrival, days'
        ],
        [
            'question' => 'What shipping options are available?',
            'answer' => 'We offer three shipping options: Standard Shipping (5-7 days, free over $50), Express Shipping (2-3 days, $15 flat rate), and Overnight Shipping (next business day, $30 flat rate). Select your preferred option at checkout.',
            'keywords' => 'shipping options, express, overnight, standard, methods'
        ],
        [
            'question' => 'Do you ship internationally?',
            'answer' => 'Yes, we ship to most countries worldwide. International shipping typically takes 10-21 business days. Customs duties and taxes may apply depending on your country. You can calculate international shipping costs during checkout.',
            'keywords' => 'international, worldwide, shipping outside US, customs'
        ],
        [
            'question' => 'Can I change my shipping address after ordering?',
            'answer' => 'If your order hasn\'t shipped yet, you can change your delivery address within 2 hours of placing the order. Contact our support team with your order number and new address. If the order has already shipped, we cannot change the address, but you can refuse delivery or arrange for re-delivery.',
            'keywords' => 'change address, delivery address, update address, redirect'
        ],
        [
            'question' => 'What areas do you deliver to?',
            'answer' => 'We deliver to all 50 US states, US territories, Canada, and 195+ countries worldwide. Some remote areas may have extended delivery times or additional fees. Check delivery availability by entering your zip code at checkout.',
            'keywords' => 'delivery areas, coverage, where we ship, delivery zones'
        ]
    ],
    'Returns & Refunds' => [
        [
            'question' => 'What is your return policy?',
            'answer' => 'We offer a 30-day money-back guarantee. Items must be unused, in original packaging, and in resalable condition. To initiate a return, log into your account, select the item, and choose "Return This Item." We\'ll provide a prepaid shipping label and refund you within 5-7 business days of receiving the returned item.',
            'keywords' => 'return policy, 30 days, money back, guarantee'
        ],
        [
            'question' => 'How do I start a return?',
            'answer' => 'To return an item: 1) Log into your account and go to "My Orders", 2) Select the order and click "Return This Item", 3) Choose a reason and confirm, 4) We\'ll email you a prepaid shipping label, 5) Pack the item securely and drop it off at the carrier location. Your refund will be processed 5-7 days after we receive it.',
            'keywords' => 'how to return, return process, return steps, initiate return'
        ],
        [
            'question' => 'How long does it take to receive a refund?',
            'answer' => 'Refunds are processed within 5-7 business days after we receive and inspect your returned item. The time it takes for the refund to appear in your bank account or on your credit card depends on your financial institution (usually 3-5 additional business days).',
            'keywords' => 'refund time, how long refund, processing time'
        ],
        [
            'question' => 'Can I return an item that was on sale?',
            'answer' => 'Yes, sale items are eligible for return under the same 30-day policy. The item will be refunded at the sale price you paid, not the original price. This is the standard for all retailers.',
            'keywords' => 'sale items, discount, return discount, sale return'
        ],
        [
            'question' => 'What items cannot be returned?',
            'answer' => 'Non-returnable items include: opened electronics (without original packaging), used cosmetics or personal care items, items purchased over 30 days ago, clearance items marked "final sale", and damaged items (customer damage). All other items can be returned within 30 days for a full refund.',
            'keywords' => 'non-returnable, cannot return, not returnable, exceptions'
        ],
        [
            'question' => 'How do I get a partial refund or exchange?',
            'answer' => 'For exchanges, select "Exchange" instead of "Return" when processing your request. For partial refunds (such as for minor damages), contact our customer support team with photos and order number. We\'ll review your case and offer appropriate compensation.',
            'keywords' => 'exchange, partial refund, damage, compensation'
        ]
    ],
    'Payment & Billing' => [
        [
            'question' => 'What payment methods do you accept?',
            'answer' => 'We accept all major credit cards (Visa, MasterCard, American Express, Discover), PayPal, Apple Pay, Google Pay, and Amazon Pay. All payments are processed securely using SSL encryption technology.',
            'keywords' => 'payment methods, credit card, paypal, pay'
        ],
        [
            'question' => 'Is my payment information secure?',
            'answer' => 'Yes, all payments are secured using industry-standard SSL encryption. We never store full credit card information on our servers. Our checkout is PCI DSS compliant, ensuring your financial information is protected.',
            'keywords' => 'secure, safe, encryption, privacy, credit card security'
        ],
        [
            'question' => 'Why was my card declined?',
            'answer' => 'Common reasons include: insufficient funds, incorrect card details, expired card, card blocked by your bank, or mismatched billing address. Please: 1) Verify card details and expiration date, 2) Check with your bank, 3) Try a different payment method, or 4) Contact support if problems persist.',
            'keywords' => 'card declined, payment failed, card rejected'
        ],
        [
            'question' => 'Can I use multiple payment methods?',
            'answer' => 'Currently, we only allow one payment method per transaction. However, you can split payments across multiple orders if needed. Contact support for special arrangements.',
            'keywords' => 'multiple payments, split payment, different cards'
        ],
        [
            'question' => 'Do you offer installment plans?',
            'answer' => 'Yes, for orders over $100, we offer 3-month, 6-month, and 12-month interest-free installment plans through Afterpay and Klarna. Select "Buy Now, Pay Later" at checkout to see available options.',
            'keywords' => 'installment, financing, payment plan, klarna, afterpay'
        ],
        [
            'question' => 'How do I dispute a charge?',
            'answer' => 'If you see an unauthorized charge, contact our support team within 30 days with your order number. We\'ll investigate immediately. You can also file a dispute with your credit card company. Charges are typically reversed within 3-5 business days.',
            'keywords' => 'dispute, unauthorized, chargeback, wrong charge'
        ]
    ],
    'Product Availability' => [
        [
            'question' => 'How do I know if a product is in stock?',
            'answer' => 'On each product page, we display the current stock status: "In Stock" (ships within 1-2 days), "Limited Stock" (may ship within 5-7 days), or "Out of Stock" (currently unavailable). Pre-order items will show an expected availability date.',
            'keywords' => 'in stock, available, out of stock, stock status'
        ],
        [
            'question' => 'Can I pre-order out-of-stock items?',
            'answer' => 'Yes, for most out-of-stock items, you can place a pre-order and we\'ll ship it as soon as stock arrives. The pre-order price is locked in at the time of purchase. You\'ll receive email notifications about estimated arrival and shipment.',
            'keywords' => 'pre-order, pre order, backorder, coming soon'
        ],
        [
            'question' => 'When will an out-of-stock item be back in stock?',
            'answer' => 'Out-of-stock dates vary by product. Check the product page for "Expected Back In Stock" information. You can click "Notify Me" to receive an email alert when it\'s back in stock. We try to maintain inventory, but some items may be discontinued.',
            'keywords' => 'when back in stock, restock, coming back, availability date'
        ],
        [
            'question' => 'How do I get notified when an item is back in stock?',
            'answer' => 'Click the "Notify Me" button on any out-of-stock product page and enter your email. We\'ll send you an alert as soon as the item is available. You can manage notifications in your account preferences.',
            'keywords' => 'notify me, alert, back in stock notification, restock alert'
        ]
    ],
    'Account & Login' => [
        [
            'question' => 'How do I create an account?',
            'answer' => 'Click "Sign Up" on the homepage, enter your email and create a password, then verify your email address. You\'ll receive a confirmation email with a verification link. No credit card is required to create an account.',
            'keywords' => 'create account, sign up, register, new account'
        ],
        [
            'question' => 'I forgot my password. How do I reset it?',
            'answer' => 'Click "Forgot Password" on the login page, enter your email, and we\'ll send you a password reset link within 2 minutes. Click the link in the email and create a new password. The link expires in 24 hours for security.',
            'keywords' => 'forgot password, reset password, password recovery'
        ],
        [
            'question' => 'Can I place an order without an account?',
            'answer' => 'Yes, you can check out as a guest without creating an account. However, an account allows you to: track orders easily, save addresses for faster checkout, view order history, and manage returns. We recommend creating an account for better experience.',
            'keywords' => 'guest checkout, without account, no account needed'
        ],
        [
            'question' => 'How do I update my account information?',
            'answer' => 'Log into your account, go to "Account Settings" or "My Profile", and you can update: email address, password, phone number, addresses, and payment methods. Changes take effect immediately.',
            'keywords' => 'update account, change email, change password, update profile'
        ]
    ],
    'Warranty & Support' => [
        [
            'question' => 'Do your products come with a warranty?',
            'answer' => 'Most electronics and appliances come with a 1-year manufacturer\'s warranty covering defects in materials and workmanship. Accessories typically have a 30-day warranty. Warranty details are listed on each product page. Extended warranty options are available at checkout.',
            'keywords' => 'warranty, protection plan, guarantee, defect'
        ],
        [
            'question' => 'How do I claim a warranty?',
            'answer' => 'To file a warranty claim: 1) Contact our support team with your order number and issue description, 2) Provide photos or video of the defect, 3) We\'ll review and send you a prepaid shipping label, 4) Ship the item for inspection, 5) We\'ll repair, replace, or refund based on our assessment.',
            'keywords' => 'warranty claim, defective, repair, replace'
        ],
        [
            'question' => 'What if my item arrives damaged?',
            'answer' => 'If your item arrives damaged: 1) Don\'t discard the packaging, 2) Take photos of the damage, 3) Contact support immediately with your order number and photos, 4) We\'ll send a replacement or full refund within 24-48 hours. No return shipping needed for damaged items.',
            'keywords' => 'damaged, broken, arrived damaged, damaged delivery'
        ],
        [
            'question' => 'How do I contact customer support?',
            'answer' => 'You can reach us through: Email (support@example.com - response within 24 hours), Live Chat (available 9am-9pm EST daily), Phone (1-800-123-4567, M-F 9am-6pm EST), or submit a contact form on our website. We\'re here to help!',
            'keywords' => 'support, contact us, customer service, help'
        ]
    ]
];

// Insert sample data
echo "<h2>Inserting Sample Shopping-Related Q&A Data</h2><br>";

$inserted_count = 0;
$skipped_count = 0;
$total_count = 0;

foreach ($sample_data as $category_name => $qa_items) {
    if (!isset($category_map[$category_name])) {
        echo "⚠️  Category not found: $category_name<br>";
        continue;
    }
    
    $category_id = $category_map[$category_name];
    echo "<strong>$category_name</strong><br>";
    
    foreach ($qa_items as $item) {
        $total_count++;
        $question_id = $kb->addQA(
            $item['question'],
            $item['answer'],
            $category_id,
            $item['keywords']
        );
        
        if ($question_id) {
            echo "✓ " . substr($item['question'], 0, 50) . "...<br>";
            $inserted_count++;
        } elseif ($question_id === false) {
            echo "→ " . substr($item['question'], 0, 50) . "... (already exists, skipped)<br>";
            $skipped_count++;
        } else {
            echo "✗ Failed to insert: " . $item['question'] . "<br>";
        }
    }
    echo "<br>";
}

// Summary
echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p><strong>New items inserted:</strong> $inserted_count</p>";
echo "<p><strong>Items skipped (already exist):</strong> $skipped_count</p>";
echo "<p><strong>Total processed:</strong> $total_count</p>";
if ($inserted_count > 0 || $skipped_count > 0) {
    echo "<p style='color: green;'><strong>✓ Setup successful! Your chatbot is ready to use.</strong></p>";
    echo "<p><a href='index.html'>Go to Chat Now →</a></p>";
} else {
    echo "<p style='color: orange;'><strong>⚠️  No items processed. Please check the database.</strong></p>";
}

echo "<br><a href='admin.html'>Go to Admin Panel</a> | <a href='index.html'>Go to Chatbot</a>";
?>

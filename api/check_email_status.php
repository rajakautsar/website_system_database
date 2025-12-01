<?php
// Check if test email arrived - This script helps identify delivery issues

echo "=== CHECKING EMAIL STATUS ===\n\n";

echo "📧 EMAIL DETAILS:\n";
echo "- Message ID from Hostinger: 4dKZBF1zH8z1y4B\n";
echo "- Sent from: no-reply@deslink.id\n";
echo "- Sent to: rajakautsar09@gmail.com\n";
echo "- Sent at: 2025-12-01 07:43:37\n";
echo "- Subject: Test Email - 2025-12-01 07:43:37\n\n";

echo "🔍 WHERE TO CHECK:\n";
echo "1. Gmail Inbox at rajakautsar09@gmail.com\n";
echo "2. Gmail Spam folder (check for emails from no-reply@deslink.id)\n";
echo "3. Gmail All Mail\n";
echo "4. Gmail Promotions tab\n\n";

echo "⚠️  POSSIBLE ISSUES:\n";
echo "A. SPF/DKIM Records Not Set Up\n";
echo "   - Gmail may reject 'no-reply@deslink.id' if deslink.id domain doesn't have:\n";
echo "     • SPF record: v=spf1 include:hostinger.com ~all\n";
echo "     • DKIM: Public key from Hostinger added to DNS\n";
echo "   - Check: https://mxtoolbox.com/spf.aspx (search for deslink.id)\n\n";

echo "B. Sender Domain Reputation\n";
echo "   - New domain sending to Gmail may trigger spam filters\n";
echo "   - Solution: Send from established domain or use Gmail SMTP\n\n";

echo "C. Hostinger Queue Issue (Rare)\n";
echo "   - Email accepted but not delivered yet\n";
echo "   - Wait 5-10 minutes and check again\n\n";

echo "✅ NEXT STEP:\n";
echo "Please check rajakautsar09@gmail.com:\n";
echo "  - Is the test email in Inbox? YES = Problem solved! 🎉\n";
echo "  - Is it in Spam? YES = Whitelist no-reply@deslink.id in Gmail\n";
echo "  - Not found? YES = SPF/DKIM issue - need to configure DNS records\n\n";

echo "If email is not found after 10 minutes, we need to:\n";
echo "1. Check Hostinger DNS settings for SPF/DKIM\n";
echo "2. Or switch to Gmail SMTP (guaranteed to work)\n";
?>

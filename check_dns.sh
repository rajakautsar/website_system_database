#!/bin/bash
# Diagnostic: Check DNS records for deslink.id domain

echo "=== CHECKING DNS RECORDS FOR deslink.id ==="
echo ""
echo "Checking SPF record..."
dig TXT deslink.id | grep -i spf || echo "⚠️ No SPF record found"
echo ""
echo "Checking MX record..."
dig MX deslink.id || echo "No MX records"
echo ""
echo "Checking CNAME records..."
dig CNAME deslink.id || echo "No CNAME records"
echo ""
echo "If SPF is missing, add this to DNS:"
echo "TXT Record: v=spf1 include:hostinger.com ~all"

#!/usr/bin/env bash
# tests/test_verify_flow.sh
# Usage: run after starting local server (XAMPP or php -S) at http://localhost/dgitech

BASE_URL=${BASE_URL:-http://localhost/dgitech}

echo "Seeding admin (admin@local / Secret123)"
php scripts/seed_admin.php --email=admin@local --password=Secret123

echo "Creating sample payment..."
OUT=$(php scripts/create_sample_payment.php)
echo "Create output: $OUT"
PAYMENT_ID=$(echo "$OUT" | tail -n1 | jq -r '.payment_id')

if [ -z "$PAYMENT_ID" ] || [ "$PAYMENT_ID" == "null" ]; then
  echo "Failed to create payment. Output: $OUT"
  exit 1
fi

echo "Logging in as admin to get session cookie"
COOKIEJAR=$(mktemp)
curl -s -c $COOKIEJAR -d "email=admin@local&password=Secret123&submit=Login" $BASE_URL/login.php > /dev/null

echo "Verify payment id: $PAYMENT_ID"
curl -s -b $COOKIEJAR -X POST "$BASE_URL/admin/verify_payment.php" \
  -H "Content-Type: application/json" \
  -d "{\"payment_id\":$PAYMENT_ID,\"action\":\"approve\",\"note\":\"Test approve\"}" | jq

echo "Done"

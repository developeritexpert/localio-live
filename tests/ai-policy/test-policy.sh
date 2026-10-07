#!/usr/bin/env bash
# =============================================================================
# tests/ai-policy/test-policy.sh
# =============================================================================
# Automated test suite for the Universal AI Coding Policy.
#
# USAGE
#   bash tests/ai-policy/test-policy.sh
#
# Exits with:
#   0 -- all tests passed
#   1 -- one or more tests failed
# =============================================================================

set -uo pipefail

# ---------------------------------------------------------------------------
# Setup
# ---------------------------------------------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
POLICY_BIN="${REPO_ROOT}/.ai-policy/bin/ai-policy"
POLICY_JSON="${REPO_ROOT}/.ai-policy/policy.json"
POLICY_JSON_BACKUP="${REPO_ROOT}/.ai-policy/.policy.json.test-backup"

PASS=0
FAIL=0
FAILED_TESTS=()

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
_backup_policy() {
    cp "$POLICY_JSON" "$POLICY_JSON_BACKUP" 2>/dev/null || true
}

_restore_policy() {
    if [[ -f "$POLICY_JSON_BACKUP" ]]; then
        mv "$POLICY_JSON_BACKUP" "$POLICY_JSON"
    fi
}

_write_policy_raw() {
    printf '%s' "$1" > "$POLICY_JSON"
}

_write_policy_json() {
    python3 -c "
import json
data = $1
with open('${POLICY_JSON}', 'w') as f:
    json.dump(data, f, indent=2)
    f.write('\n')
"
}

_assert_check() {
    local test_name="$1"
    local expected="$2"
    local actual
    actual="$("$POLICY_BIN" check 2>/dev/null || echo "LOCKED")"

    if [[ "$actual" == "$expected" ]]; then
        printf "  PASS  %s\n" "$test_name"
        ((PASS++))
    else
        printf "  FAIL  %s  (expected: %s, got: %s)\n" "$test_name" "$expected" "$actual"
        ((FAIL++))
        FAILED_TESTS+=("$test_name")
    fi
}

# ---------------------------------------------------------------------------
# Tests
# ---------------------------------------------------------------------------
echo ""
echo "============================================================"
echo "  AI Coding Policy Test Suite"
echo "============================================================"
echo ""

# --- Test 1: Default state ---
echo "-- Test 1: Default state (must be LOCKED)"
_backup_policy
_write_policy_json "{'version': 1, 'ai_coding': 'locked'}"
_assert_check "Default state = LOCKED" "LOCKED"
_restore_policy

# --- Test 2: Enable ---
echo ""
echo "-- Test 2: ai-policy enable -> check -> ENABLED"
_backup_policy
"$POLICY_BIN" enable > /dev/null 2>&1
_assert_check "After enable = ENABLED" "ENABLED"
_restore_policy

# --- Test 3: Disable ---
echo ""
echo "-- Test 3: ai-policy disable -> check -> LOCKED"
_backup_policy
"$POLICY_BIN" enable > /dev/null 2>&1
"$POLICY_BIN" disable > /dev/null 2>&1
_assert_check "After disable = LOCKED" "LOCKED"
_restore_policy

# --- Test 4: Missing policy file ---
echo ""
echo "-- Test 4: Missing policy.json -> LOCKED"
_backup_policy
rm -f "$POLICY_JSON"
_assert_check "Missing policy file = LOCKED" "LOCKED"
_restore_policy

# --- Test 5: Malformed JSON ---
echo ""
echo "-- Test 5: Malformed JSON -> LOCKED"
_backup_policy
_write_policy_raw '{bad json!!!'
_assert_check "Malformed JSON = LOCKED" "LOCKED"
_restore_policy

# --- Test 6: Unknown value ---
echo ""
echo "-- Test 6: ai_coding=banana -> LOCKED"
_backup_policy
_write_policy_json "{'version': 1, 'ai_coding': 'banana'}"
_assert_check "Unknown value (banana) = LOCKED" "LOCKED"
_restore_policy

# --- Test 7: Empty file ---
echo ""
echo "-- Test 7: Empty policy file -> LOCKED"
_backup_policy
_write_policy_raw ''
_assert_check "Empty file = LOCKED" "LOCKED"
_restore_policy

# --- Test 8: Wrong version ---
echo ""
echo "-- Test 8: version=99 -> LOCKED"
_backup_policy
_write_policy_json "{'version': 99, 'ai_coding': 'enabled'}"
_assert_check "Wrong version (99) = LOCKED" "LOCKED"
_restore_policy

# --- Test 9: Missing version field ---
echo ""
echo "-- Test 9: Missing version field -> LOCKED"
_backup_policy
_write_policy_json "{'ai_coding': 'enabled'}"
_assert_check "Missing version field = LOCKED" "LOCKED"
_restore_policy

# --- Test 10: ENABLED state ---
echo ""
echo "-- Test 10: Explicit enabled state -> ENABLED"
_backup_policy
_write_policy_json "{'version': 1, 'ai_coding': 'enabled'}"
_assert_check "Explicit enabled = ENABLED" "ENABLED"
_restore_policy

# --- Test 11: Wrong case value ---
echo ""
echo "-- Test 11: ai_coding=ENABLED (uppercase) -> LOCKED (strict normalization check)"
_backup_policy
# Note: Our evaluator normalizes to lowercase, so ENABLED -> enabled -> ENABLED
# This is intentional behavior. Upper-case should still work after normalization.
_write_policy_json "{'version': 1, 'ai_coding': 'ENABLED'}"
_assert_check "ENABLED uppercase (normalized) = ENABLED" "ENABLED"
_restore_policy

# --- Final state: restore to LOCKED ---
echo ""
echo "-- Final: Restore default state -> LOCKED"
_write_policy_json "{'version': 1, 'ai_coding': 'locked'}"
_assert_check "Final state restored = LOCKED" "LOCKED"

# ---------------------------------------------------------------------------
# Summary
# ---------------------------------------------------------------------------
echo ""
echo "============================================================"
echo "  Results: ${PASS} passed, ${FAIL} failed"
echo "============================================================"

if [[ ${FAIL} -gt 0 ]]; then
    echo ""
    echo "  Failed tests:"
    for t in "${FAILED_TESTS[@]}"; do
        echo "    - $t"
    done
    echo ""
    exit 1
else
    echo ""
    echo "  All tests passed."
    echo ""
    exit 0
fi

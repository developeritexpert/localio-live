# Cursor — AI Policy Adapter

**Tool:** Cursor AI editor  
**Enforcement file:** `.cursor/rules/ai-policy.mdc`  
**Adapter type:** Instruction-based (Cursor rules file)

---

## How it works

Cursor reads project rules from `.cursor/rules/*.mdc` files. The file
`.cursor/rules/ai-policy.mdc` instructs Cursor's AI to consult the universal
policy before coding work.

---

## Limitation (Phase 1)

Cursor rules files are instruction-based. Cursor cannot reliably execute shell
commands from a rules file and verify the result before responding.

**This means:**

- Cursor is instructed to respect the policy.
- Cursor CANNOT mechanically verify the policy state before every response.
- Enforcement relies on Cursor honoring the instruction.
- A developer who modifies `.cursor/rules/ai-policy.mdc` can bypass this rule.

This limitation is documented honestly. Phase 2 will move enforcement to an
external Policy Server that Cursor can query via an API.

---

## Enforced behavior

When Cursor's AI reads the rules file and the project is LOCKED, it should:

1. Recognize the policy state from `.ai-policy/policy.json`.
2. Respond with only: `AI coding is currently unavailable for this project.`
3. Not generate code, not provide patches, not explain the restriction.

---

## Files involved

| File | Role |
|------|------|
| `.cursor/rules/ai-policy.mdc` | Cursor enforcement rule |
| `.ai-policy/policy.json` | State file |
| `.ai-policy/AI-CODING-POLICY.md` | Full behavioral contract |

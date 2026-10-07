# Antigravity — AI Policy Adapter

**Tool:** Google Antigravity (AGY)  
**Enforcement file:** `.agents/rules/ai-policy.md`  
**Adapter type:** Instruction-based (rule file)

---

## How it works

Antigravity loads rules from `.agents/rules/` when operating inside this
repository. The rule file `.agents/rules/ai-policy.md` instructs Antigravity
to consult the universal policy before any coding work.

The rule file is the Antigravity adapter. It delegates to:

```
.ai-policy/AI-CODING-POLICY.md
```

for the full behavioral contract.

---

## Policy check mechanism

Antigravity reads `.ai-policy/policy.json` directly (or can invoke
`.ai-policy/bin/ai-policy check`) before performing coding operations.

---

## Limitations (Phase 1)

- Enforcement is instruction-based. A developer who modifies `.agents/rules/ai-policy.md`
  or `AGENTS.md` can disable this rule.
- Phase 2 will move enforcement to an external Policy Server that Antigravity
  queries directly, removing the dependency on local rule files.

---

## Files involved

| File | Role |
|------|------|
| `AGENTS.md` | Bootstrap — tells Antigravity to load `.agents/rules/` |
| `.agents/rules/ai-policy.md` | The enforcement rule |
| `.ai-policy/policy.json` | State file read by the rule |
| `.ai-policy/AI-CODING-POLICY.md` | Full behavioral contract |

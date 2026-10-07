# VS Code AI Extensions — Policy Adapter

**Tools:** GitHub Copilot, VS Code AI extensions  
**Enforcement file:** `.github/copilot-instructions.md`  
**Adapter type:** Instruction-based (Copilot custom instructions)

---

## How it works

GitHub Copilot reads `.github/copilot-instructions.md` as custom instructions
for this workspace. The file instructs Copilot to consult the universal policy
before performing coding work.

Other VS Code AI extensions may or may not honor this file. Behavior varies
by extension.

---

## Limitation (Phase 1)

VS Code AI extensions vary significantly in how they process custom instructions.

**What can be done:**
- Provide a `.github/copilot-instructions.md` file that instructs Copilot
  to respect the policy.

**What cannot be reliably done:**
- Mechanically verify that Copilot reads the policy file before every action.
- Prevent a developer from modifying or deleting the instructions file.
- Enforce the policy across all VS Code AI extensions.

This limitation is documented honestly. This is NOT a security boundary.

---

## Files involved

| File | Role |
|------|------|
| `.github/copilot-instructions.md` | Copilot custom instructions |
| `.ai-policy/policy.json` | State file |
| `.ai-policy/AI-CODING-POLICY.md` | Full behavioral contract |

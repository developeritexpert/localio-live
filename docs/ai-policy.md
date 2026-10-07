# AI Coding Policy — Documentation

**Repository:** localio-live  
**Phase:** 1 (Local Repository Policy)  
**Default state:** LOCKED

---

## 1. What this policy does

This repository implements a **Universal AI Coding Policy** that controls
whether AI coding agents (Antigravity, Cursor, GitHub Copilot, etc.) are
authorized to perform coding work inside this project.

The policy has two states:

| State | Meaning |
|-------|---------|
| `LOCKED` | AI coding agents must refuse all coding requests |
| `ENABLED` | Normal AI coding behavior is permitted |

**The default state is LOCKED.** Every fresh clone of this repository starts
with AI coding locked.

---

## 2. Architecture

```
                    PROJECT
                       |
                       v
               Universal AI Policy
                  .ai-policy/
                       |
                  ai-policy check
                       |
              .--------+--------.
              |                 |
           LOCKED            ENABLED
              |                 |
              v                 v
       refuse AI coding     normal AI use
```

The policy is maintained in one place and all AI tool integrations point to it:

```
              .ai-policy/policy.json   (single source of truth)
                         |
               .ai-policy/lib/policy-checker
                         |
           .-------.-----+-----.-------.
           |       |           |       |
     Antigravity  Cursor    VS Code  Generic
```

---

## 3. Policy file

Location: `.ai-policy/policy.json`

```json
{
  "version": 1,
  "ai_coding": "locked"
}
```

Valid values for `ai_coding`:

| Value | Meaning |
|-------|---------|
| `"locked"` | AI coding disabled |
| `"enabled"` | AI coding permitted |

All other values are treated as `"locked"`.

---

## 4. Default behavior

- Fresh clone: **LOCKED**
- Missing policy file: **LOCKED**
- Malformed JSON: **LOCKED**
- Unknown value: **LOCKED**
- Any error: **LOCKED**

**The system always fails closed. It never fails open.**

---

## 5. Available commands

Add `.ai-policy/bin` to your PATH, or run with the full path.

### Check status (machine-readable)

```bash
.ai-policy/bin/ai-policy check
```

Output: `LOCKED` or `ENABLED` (nothing else)

### Check status (human-readable)

```bash
.ai-policy/bin/ai-policy status
```

Output:

```
Project:     localio-live
AI coding:   LOCKED
```

### Enable AI coding (Phase 1 only)

```bash
.ai-policy/bin/ai-policy enable
```

### Disable AI coding (Phase 1 only)

```bash
.ai-policy/bin/ai-policy disable
```

---

## 6. How to enable AI coding

```bash
.ai-policy/bin/ai-policy enable
.ai-policy/bin/ai-policy status
```

Expected output:

```
Project:     localio-live
AI coding:   ENABLED
```

---

## 7. How to disable AI coding

```bash
.ai-policy/bin/ai-policy disable
.ai-policy/bin/ai-policy status
```

Expected output:

```
Project:     localio-live
AI coding:   LOCKED
```

---

## 8. Locked response

When AI coding is LOCKED and a user makes a coding request, the AI agent
must respond with exactly this and nothing else:

```
AI coding is currently unavailable for this project.
```

The agent must not:
- Generate code
- Provide patches or diffs
- Explain the restriction
- Suggest a workaround
- Enable itself

---

## 9. AI tool integrations

### Antigravity

Rule file: `.agents/rules/ai-policy.md`  
Bootstrap: `AGENTS.md`  
Enforcement: Instruction-based (rule file loaded automatically)

### Cursor

Rule file: `.cursor/rules/ai-policy.mdc`  
Enforcement: Instruction-based  
**Limitation:** Cursor cannot mechanically verify the policy before every response.

### GitHub Copilot / VS Code

Instructions: `.github/copilot-instructions.md`  
Enforcement: Instruction-based  
**Limitation:** Varies by extension. Cannot guarantee all VS Code AI extensions
honor this file.

### Generic / Future agents

Contract: `.ai-policy/adapters/generic-agent.md`  
Any AI tool integrating with this project should follow the contract defined there.

---

## 10. Phase 1 limitations

### This is NOT a security boundary.

A developer who controls their own computer can:

- Edit `.ai-policy/policy.json` directly to change the state
- Delete `.ai-policy/policy.json` (fails closed — results in LOCKED)
- Edit `.agents/rules/ai-policy.md` to remove the Antigravity rule
- Edit `AGENTS.md` to bypass rule loading
- Modify `.cursor/rules/ai-policy.mdc` or `.github/copilot-instructions.md`
- Modify the policy CLI scripts
- Use a different AI tool that ignores policy files entirely

**None of these bypasses can be prevented in Phase 1.**

The purpose of Phase 1 is to establish the policy architecture and interfaces
so that Phase 2 can enforce them reliably via an external server.

---

## 11. Known bypasses

| Bypass | Result | Notes |
|--------|--------|-------|
| Edit `policy.json` directly | Bypasses CLI | Committed to Git, changes are visible |
| Delete `policy.json` | Fails closed (LOCKED) | Not a bypass |
| Corrupt `policy.json` | Fails closed (LOCKED) | Not a bypass |
| Modify rule files | Removes AI instruction | Visible in Git history |
| Use unsupported AI tool | No enforcement | Documented limitation |

---

## 12. Phase 2 migration

Phase 2 will replace the local `policy.json` with an external Policy Server.

```
ai-policy check
       |
  authenticated HTTPS
       |
  External Policy Server
       |
  project identity + developer identity
       |
  LOCKED / ENABLED
```

Phase 2 changes:
- `ai-policy check` will query the Policy Server instead of reading a local file
- `ai-policy enable` and `ai-policy disable` will be removed from the CLI
- Administrators will manage authorization via the Policy Server
- Developer identity will be verified (e.g., Git config email, SSH key)
- Authorization expiration, audit logging, and revocation will be supported
- All AI-tool adapters (Antigravity, Cursor, VS Code, generic) require **zero changes**

The `ai-policy check` interface is stable across Phase 1 and Phase 2.

---

## 13. Repository files

| File | Purpose |
|------|---------|
| `.ai-policy/policy.json` | Policy state (single source of truth) |
| `.ai-policy/lib/policy-checker` | Core evaluation logic |
| `.ai-policy/bin/ai-policy` | CLI |
| `.ai-policy/AI-CODING-POLICY.md` | Canonical behavioral contract |
| `.ai-policy/adapters/antigravity.md` | Antigravity adapter documentation |
| `.ai-policy/adapters/cursor.md` | Cursor adapter documentation |
| `.ai-policy/adapters/vscode.md` | VS Code adapter documentation |
| `.ai-policy/adapters/generic-agent.md` | Generic integration contract |
| `.agents/rules/ai-policy.md` | Antigravity enforcement rule |
| `AGENTS.md` | Antigravity bootstrap |
| `.cursor/rules/ai-policy.mdc` | Cursor enforcement rule |
| `.github/copilot-instructions.md` | GitHub Copilot instructions |
| `docs/ai-policy.md` | This file |
| `tests/ai-policy/test-policy.sh` | Automated tests |

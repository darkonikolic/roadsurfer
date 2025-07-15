# ⚠️ AI Execution Rules for All Prompts

All AI interactions within this repository must strictly follow these rules. These are not suggestions — they are mandatory constraints.

## 1. 🚫 No Hallucination
- Do not invent features, tools, technologies, steps, entities, or content not explicitly defined in the prompt or surrounding context.

## 2. 🧱 No Improvisation
- Do not "fill in the gaps" or generate assumptions.
- If a requirement is unclear, label it as `TBD` or prompt the user for clarification.

## 3. 🔍 Precision Only
- Use only real, verified, production-grade tools (e.g. Terraform, ArgoCD, Prometheus).
- Do not reference libraries or solutions that don't exist.

## 4. 🧭 Scope Integrity
- Do not duplicate logic or functionality already defined in `prompt 2`.
- If overlap occurs, refer to the existing implementation — do not reimplement.

## 5. 🧠 Architectural Principles
- All planning, structure, and decisions must comply with:
  - **KISS** (Keep It Simple, Stupid)
  - **DRY** (Don't Repeat Yourself)
  - **YAGNI** (You Aren't Gonna Need It)
  - **SOLID** (Object-Oriented Design Principles)

## 6. ✋ No Meta-Commentary
- Do not explain why something is helpful or educational unless explicitly asked.
- These prompts are for planning and demonstration — not learning.

<!--
## 7. 🛠️ Planning Only
- No code generation unless explicitly required.
- These prompts are for design, architecture, and execution planning. 
--> 
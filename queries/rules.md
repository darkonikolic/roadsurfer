# QUERY TO GENERATE/UPDATE RULES.MD
⚠️ The resulting rules must reflect current best practices for AI execution

## 🎯 OBJECTIVE
Generate or update comprehensive AI execution rules that reflect best practices for prompt engineering and AI interaction constraints.

## 📋 REQUIREMENTS

### **Core Structure:**
- Title: "⚠️ AI Execution Rules for All Prompts"
- Must be placed in `prompts/` directory
- Must include mandatory constraints language

### **Rule 1: No Hallucination**
- **Principle:** Do not invent features, tools, technologies, steps, entities, or content not explicitly defined in the prompt or surrounding context
- **Best Practice:** Always verify information exists before referencing
- **Implementation:** Use only real, verified, production-grade tools and technologies
- **Examples:** Terraform, ArgoCD, Prometheus, Kubernetes, AWS services

### **Rule 2: No Improvisation**
- **Principle:** Do not "fill in the gaps" or generate assumptions
- **Best Practice:** If a requirement is unclear, label it as `TBD` or prompt the user for clarification
- **Implementation:** Stick to explicit requirements only
- **Exception:** Only provide clarification when explicitly requested

### **Rule 3: Precision Only**
- **Principle:** Use only real, verified, production-grade tools
- **Best Practice:** Reference only tools and technologies that actually exist
- **Implementation:** Avoid hypothetical or theoretical solutions
- **Examples:** Use Terraform, not "InfrastructureTool"; use ArgoCD, not "GitOpsPlatform"

### **Rule 4: Scope Integrity**
- **Principle:** Do not duplicate logic or functionality already defined in other prompts
- **Best Practice:** If overlap occurs, refer to the existing implementation — do not reimplement
- **Implementation:** Maintain consistency across all prompts
- **Reference:** Always check for existing implementations before creating new ones

### **Rule 5: Architectural Principles**
- **Principle:** All planning, structure, and decisions must comply with established software engineering principles
- **Best Practices:**
  - **KISS** (Keep It Simple, Stupid) - Avoid over-engineering
  - **DRY** (Don't Repeat Yourself) - Eliminate code duplication
  - **YAGNI** (You Aren't Gonna Need It) - Don't build features you don't need
  - **SOLID** (Object-Oriented Design Principles) - Follow clean architecture patterns
- **Implementation:** Apply these principles to all technical decisions

### **Rule 6: No Meta-Commentary**
- **Principle:** Do not explain why something is helpful or educational unless explicitly asked
- **Best Practice:** These prompts are for planning and demonstration — not learning
- **Implementation:** Focus on actionable content, not educational explanations
- **Exception:** Only provide educational context when specifically requested

### **Rule 7: Planning Only**
- **Principle:** No code generation unless explicitly required
- **Best Practice:** These prompts are for design, architecture, and execution planning
- **Implementation:** Focus on structure, architecture, and planning documents
- **Exception:** Generate code only when explicitly requested in the prompt

### **Rule 8: Enterprise Focus**
- **Principle:** All solutions must be suitable for enterprise environments
- **Best Practice:** Use industry-standard tools and practices
- **Implementation:** Reference enterprise-grade solutions and methodologies
- **Examples:** AWS, Kubernetes, Terraform, CI/CD pipelines, security compliance

### **Rule 9: Real-World Applicability**
- **Principle:** All recommendations must be implementable in real production environments
- **Best Practice:** Avoid academic or theoretical approaches
- **Implementation:** Focus on practical, deployable solutions
- **Criteria:** Solutions must be suitable for job interviews and stakeholder presentations

### **Rule 10: Documentation Standards**
- **Principle:** All generated content must be suitable for technical documentation
- **Best Practice:** Use clear, structured, and professional language
- **Implementation:** Follow technical writing best practices
- **Format:** Use consistent markdown formatting and structure

### **Rule 11: Quality Assurance**
- **Principle:** All content must meet enterprise quality standards
- **Best Practice:** Include validation criteria and success metrics
- **Implementation:** Define clear acceptance criteria for all deliverables
- **Metrics:** Include measurable outcomes and performance indicators

### **Rule 12: Security First**
- **Principle:** All solutions must consider security implications
- **Best Practice:** Include security considerations in all technical decisions
- **Implementation:** Reference security best practices and compliance requirements
- **Examples:** OWASP guidelines, SOC2 compliance, encryption standards

## 🎯 OUTPUT REQUIREMENTS
- Generate comprehensive rules file with all 12 rules
- Include best practices and implementation guidelines for each rule
- Use clear, professional language suitable for enterprise environments
- Maintain consistent formatting and structure
- Ensure rules are actionable and enforceable
- Include examples where appropriate
- Place the generated rules in `prompts/rules.md`
- Make rules suitable for technical leadership review 
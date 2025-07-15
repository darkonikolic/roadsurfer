# QUERY TO GENERATE PROMPT 5
⚠️ The resulting prompt must strictly follow `rules.md`

## 🎯 OBJECTIVE
Generate an enterprise-level infrastructure and code maturity roadmap suitable for presentation to future employers or CTOs.

## 📋 REQUIREMENTS

### **Core Structure:**
- Title: "Enterprise-Level Infrastructure & Code Maturity Roadmap"
- Status: "BACKLOG - NOT STARTED"
- Must include rules.md enforcement clause at the top
- Must be placed in `prompts/backlog/` directory

### **Enterprise-Readiness Objective:**
This roadmap outlines the concrete engineering steps required to transform the current Symfony application into an enterprise-grade system suitable for production deployment, stakeholder presentation, and technical leadership review. Each step is designed to demonstrate senior engineering capabilities and real-world implementation skills.

### **Organize into 7 Clear Sections:**

#### **1. INFRASTRUCTURE READINESS**

**1.1 Kubernetes Production Cluster**
- Implementation: Deploy EKS cluster with Terraform, configure node groups, implement RBAC
- Tools: Terraform, AWS EKS, kubectl, Helm
- Justification: Demonstrates infrastructure as code and container orchestration expertise

**1.2 Database Architecture**
- Implementation: RDS MySQL with read replicas, connection pooling, automated backups
- Tools: AWS RDS, ProxySQL, AWS Backup
- Justification: Shows understanding of scalable database patterns and disaster recovery

**1.3 Caching & Session Management**
- Implementation: Redis cluster with persistence, session storage, query result caching
- Tools: AWS ElastiCache, Redis Cluster, Symfony Cache
- Justification: Essential for performance and scalability in enterprise applications

**1.4 Load Balancing & CDN**
- Implementation: Application Load Balancer with SSL termination, CloudFront CDN
- Tools: AWS ALB, CloudFront, Route53
- Justification: Demonstrates understanding of global distribution and traffic management

#### **2. APPLICATION ARCHITECTURE**

**2.1 Domain-Driven Design Implementation**
- Implementation: Bounded contexts, aggregates, domain services, value objects
- Tools: Symfony, PHP 8.2+, Doctrine ORM
- Justification: Shows ability to design maintainable, scalable business logic

**2.2 CQRS Pattern**
- Implementation: Command/Query separation, event sourcing preparation, read/write models
- Tools: Symfony Messenger, Doctrine, Event Sourcing
- Justification: Demonstrates advanced architectural patterns for complex business domains

**2.3 API Design & Versioning**
- Implementation: RESTful API with versioning, OpenAPI documentation, rate limiting
- Tools: Symfony API Platform, JWT, API Gateway
- Justification: Essential for enterprise integration and third-party consumption

#### **3. OBSERVABILITY**

**3.1 Distributed Tracing**
- Implementation: OpenTelemetry integration, trace correlation, performance analysis
- Tools: OpenTelemetry, Jaeger, Symfony Profiler
- Justification: Critical for debugging complex distributed systems

**3.2 Centralized Logging**
- Implementation: Structured logging, log aggregation, search and analysis
- Tools: ELK Stack, Fluentd, CloudWatch Logs
- Justification: Required for compliance and operational visibility

**3.3 Metrics & Alerting**
- Implementation: Custom business metrics, infrastructure monitoring, automated alerting
- Tools: Prometheus, Grafana, CloudWatch, PagerDuty
- Justification: Enables proactive issue detection and capacity planning

#### **4. SECURITY & COMPLIANCE**

**4.1 OAuth 2.0 & OpenID Connect**
- Implementation: JWT tokens, refresh token rotation, scope-based authorization
- Tools: LexikJWTAuthenticationBundle, OAuth2 Server
- Justification: Industry standard for enterprise authentication and authorization

**4.2 Security Hardening**
- Implementation: Security headers, CSP policies, vulnerability scanning, penetration testing
- Tools: OWASP ZAP, Trivy, Snyk, Security Headers
- Justification: Demonstrates security-first mindset required for enterprise applications

**4.3 Compliance & Governance**
- Implementation: SOC2 controls, data encryption, audit logging, access controls
- Tools: AWS KMS, CloudTrail, IAM, Compliance frameworks
- Justification: Essential for enterprise customers and regulatory requirements

#### **5. PERFORMANCE & SCALABILITY**

**5.1 Database Optimization**
- Implementation: Query optimization, indexing strategy, connection pooling, read replicas
- Tools: MySQL Performance Schema, ProxySQL, Database monitoring
- Justification: Database performance is often the bottleneck in enterprise applications

**5.2 Caching Strategy**
- Implementation: Multi-level caching, cache warming, invalidation patterns
- Tools: Redis, Symfony Cache, Varnish, CDN
- Justification: Critical for achieving sub-100ms response times under load

**5.3 Auto-scaling & Load Testing**
- Implementation: Horizontal pod autoscaling, custom metrics, comprehensive load testing
- Tools: Kubernetes HPA, K6, Artillery, Custom metrics
- Justification: Demonstrates ability to handle variable load and plan capacity

#### **6. DEVELOPER EXPERIENCE**

**6.1 CI/CD Pipeline**
- Implementation: Automated testing, security scanning, deployment automation
- Tools: GitHub Actions, ArgoCD, SonarQube, Trivy
- Justification: Shows modern DevOps practices and quality automation

**6.2 Code Quality & Testing**
- Implementation: Static analysis, mutation testing, property-based testing
- Tools: PHPStan, Psalm, Infection, Eris
- Justification: Demonstrates commitment to code quality and testing excellence

**6.3 Documentation & Knowledge Management**
- Implementation: API documentation, deployment guides, runbooks, architecture decisions
- Tools: OpenAPI, Markdown, Confluence, Architecture Decision Records
- Justification: Essential for team onboarding and enterprise knowledge transfer

#### **7. BUSINESS ALIGNMENT**

**7.1 Feature Flags & A/B Testing**
- Implementation: Feature toggle system, gradual rollouts, statistical significance testing
- Tools: Unleash, LaunchDarkly, Analytics platforms
- Justification: Enables safe deployments and data-driven feature decisions

**7.2 Business Intelligence**
- Implementation: Data warehouse, ETL processes, business metrics dashboards
- Tools: AWS Redshift, Apache Airflow, Grafana, Business metrics
- Justification: Demonstrates ability to provide business value through data insights

**7.3 Cost Optimization**
- Implementation: Resource monitoring, budget alerts, cost allocation, optimization strategies
- Tools: AWS Cost Explorer, Budget alerts, Resource tagging
- Justification: Shows financial responsibility and operational efficiency

### **Success Metrics for Enterprise Demo:**

**Performance Metrics:**
- Response Time: < 100ms average API response time
- Throughput: Support 10,000+ concurrent users
- Deployment Time: < 5 minutes from commit to production
- Uptime: 99.9% availability target

**Quality Metrics:**
- Test Coverage: > 90% code coverage
- Security: Zero critical vulnerabilities
- Performance: < 1 second page load times
- Reliability: < 1 hour recovery time objective

**Business Metrics:**
- Cost Efficiency: < $0.01 per API request
- Developer Velocity: < 1 day from feature to production
- Incident Response: < 30 minutes mean time to resolution
- Customer Satisfaction: > 95% uptime and performance SLA

### **Implementation Priority:**

**Phase 1: Foundation (Weeks 1-4)**
1. Infrastructure setup (EKS, RDS, Redis)
2. Basic CI/CD pipeline
3. Security hardening
4. Monitoring foundation

**Phase 2: Architecture (Weeks 5-8)**
1. DDD implementation
2. API design and documentation
3. Performance optimization
4. Advanced testing

**Phase 3: Enterprise Features (Weeks 9-12)**
1. OAuth 2.0 implementation
2. Advanced monitoring
3. Business intelligence
4. Cost optimization

**Phase 4: Production Readiness (Weeks 13-16)**
1. Load testing and optimization
2. Disaster recovery procedures
3. Compliance implementation
4. Final demo preparation

### **Enterprise Demo Deliverables:**

**Technical Presentation:**
- Live demonstration of deployment pipeline
- Performance metrics dashboard
- Security scan results
- Architecture diagrams and documentation

**Business Value Demonstration:**
- Cost analysis and optimization results
- Scalability testing outcomes
- Reliability metrics and SLAs
- Team productivity improvements

**Leadership Readout:**
- Risk assessment and mitigation strategies
- Compliance and security posture
- Operational procedures and runbooks
- Future roadmap and scaling plans

## 🎯 OUTPUT REQUIREMENTS
- Generate the complete prompt file with all sections and technical details
- Maintain exact structure and formatting
- Include all implementation details, tools, and justifications
- Place the generated prompt in `prompts/backlog/` directory
- Ensure all technical specifications and enterprise focus are preserved exactly 
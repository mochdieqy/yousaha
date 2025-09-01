# Use Case Diagram - Yousaha ERP System

## Overview
This document contains the use case diagram for the Yousaha ERP system based on the roles and permissions defined in `RolePermissionSeeder.php`. The diagram shows the relationship between different user roles and the system functionalities they can access.

## Mermaid Diagram

```mermaid
graph TB
    %% User Roles - Left Side
    subgraph "Management Roles"
        CO[👑 Company Owner]
        FM[💰 Finance Manager]
        SM[📈 Sales Manager]
        PM[📦 Purchase Manager]
    end
    
    %% User Roles - Right Side  
    subgraph "Operational Roles"
        IM[🏪 Inventory Manager]
        HRM[👥 HR Manager]
        EMP[👤 Employee]
        V[👁️ Viewer]
    end
    
    %% Core ERP Functions
    subgraph "Core ERP Functions"
        INV[📦 Inventory Management]
        SALES[🛒 Sales Order Management]
        PURCHASE[🛍️ Purchase Order Management]
        FINANCE[💰 Finance Management]
        HR[👥 HR Management]
        AI[🤖 AI Evaluation]
    end
    
    %% System Management
    subgraph "System Management"
        COMPANY[🏢 Company Profile]
        ROLES[⚙️ User & Role Management]
    end
    
    %% Company Owner - Full Access
    CO --> INV
    CO --> SALES
    CO --> PURCHASE
    CO --> FINANCE
    CO --> HR
    CO --> AI
    CO --> COMPANY
    CO --> ROLES
    
    %% Finance Manager
    FM --> FINANCE
    FM --> AI
    FM --> COMPANY
    
    %% Sales Manager
    SM --> INV
    SM --> SALES
    SM --> AI
    SM --> COMPANY
    
    %% Purchase Manager
    PM --> INV
    PM --> PURCHASE
    PM --> AI
    PM --> COMPANY
    
    %% Inventory Manager
    IM --> INV
    IM --> COMPANY
    
    %% HR Manager
    HRM --> HR
    HRM --> AI
    HRM --> COMPANY
    HRM --> ROLES
    
    %% Employee
    EMP --> HR
    EMP --> COMPANY
    
    %% Viewer
    V --> INV
    V --> SALES
    V --> PURCHASE
    V --> FINANCE
    V --> HR
    V --> AI
    V --> COMPANY
    
    %% Styling
    classDef management fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef operational fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef core fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef system fill:#fff3e0,stroke:#e65100,stroke-width:2px
    
    class CO,FM,SM,PM management
    class IM,HRM,EMP,V operational
    class INV,SALES,PURCHASE,FINANCE,HR,AI core
    class COMPANY,ROLES system
```

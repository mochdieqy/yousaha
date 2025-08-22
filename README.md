# Yousaha - Mini ERP Web Application

**Information System Model Design Using Ward Peppard and Rapid Application Development**

![Laravel](https://img.shields.io/badge/Laravel-v10.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-v8.1+-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Description

Yousaha is a mini ERP (Enterprise Resource Planning) web application developed as part of the thesis research "Information System Model Design Using Ward Peppard and Rapid Application Development". This application is designed to help small and medium enterprises manage their integrated business operations.

### 🎯 Background

Organizations greatly need the assistance of information systems and information technology (IS/IT) to survive and thrive in today's competitive business environment. This research analyzes the IS/IT needs of three companies:

- **PT Gimsak Teknologi Indonesia (GTI)** - Bogor: Goods and services trading company
- **PT Megah Lautan Utama (MLU)** - Bali: Fisheries company with export focus
- **PT Lingkar Nusa Teknologi (LNT)** - Jakarta: Drone pilot training center and certification

## ✨ Key Features

### 🏢 Company Management
- Multi-company support
- Company profile configuration
- User management and access control

### 📦 Inventory Management
- **Products**: Master product data with categories and specifications
- **Warehouses**: Multiple warehouse management
- **Stock**: Real-time stock tracking and stock history
- **Stock Shrink**: Stock shrinkage recording

### 💰 Sales Management
- **Sales Orders**: Sales order creation and tracking
- **Delivery**: Goods delivery management
- **Customers**: Integrated customer database
- **Status Tracking**: Real-time order status monitoring

### 🛒 Purchase Management
- **Purchase Orders**: Purchase order creation
- **Receipt**: Goods receipt from suppliers
- **Suppliers**: Supplier database management
- **Status Tracking**: Purchase status monitoring

### 💼 Finance Management
- **General Ledger**: General ledger and transaction details
- **Chart of Accounts**: Financial account management
- **Expense Management**: Expense recording
- **Income Tracking**: Income recording
- **Internal Transfer**: Inter-account transfers

### 👥 Human Resources (HR)
- **Employee Management**: Employee database
- **Department**: Department management
- **Payroll**: Payroll system
- **Attendance**: Attendance recording
- **Time Off**: Leave and time-off management

### 🏭 Assets
- **Asset Management**: Company asset management
- **Asset Tracking**: Asset tracking and monitoring

## 🛠 Technology Stack

### Backend
- **Laravel 10.x** - PHP Web Framework
- **PHP 8.1+** - Server-side scripting
- **MySQL** - Database management system

### Frontend
- **Blade Templates** - Laravel templating engine
- **Bootstrap** - CSS framework for responsive design
- **jQuery** - JavaScript library for DOM manipulation

### Development Methodology
- **Ward and Peppard** - Strategic IS/IT planning methodology
- **Rapid Application Development (RAD)** - Agile development approach

## 📊 Research Methodology

This application was developed using Ward and Peppard methodology which includes:

### 🔍 Strategic Analysis
- **PEST Analysis** - Analysis of Political, Economic, Social, and Technological factors
- **Value Chain Analysis** - Value chain analysis for business process identification
- **SWOT Analysis** - Analysis of Strengths, Weaknesses, Opportunities, and Threats
- **McFarlan Strategic Grid** - IS/IT application portfolio mapping

### 📈 Development Approach
- **Rapid Application Development (RAD)** for fast development
- **Iterative Development** for continuous improvement
- **User-Centered Design** based on user requirements

## 🚀 Installation

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL/MariaDB

### Installation Steps

1. **Clone repository**
   ```bash
   git clone https://github.com/your-username/yousaha.git
   cd yousaha
   ```
2. **Install dependencies**
   ```bash
   composer install
   ```
3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Database configuration**
   Edit the `.env` file and configure your database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=yousaha
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
5. **Database migration**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
6. **Start development server**
   ```bash
   php artisan serve
   ```
   The application will run at `http://localhost:8000`

## 📖 Usage

### System Login
1. Access the application through your browser
2. Use default credentials or create a new account
3. Select or create a company to start using the system

### Main Workflow

#### 1. Initial Setup
- Create company profile
- Setup chart of accounts
- Add warehouses and locations
- Input master product data

#### 2. Daily Operations
- **Sales**: Sales Order → Delivery → Invoice
- **Purchase**: Purchase Order → Receipt → Payment
- **Inventory**: Stock monitoring and adjustment
- **Finance**: Transaction recording and reconciliation

#### 3. HR Management
- Input employee data
- Record attendance
- Process monthly payroll

> 📋 **Quick Start**: For comprehensive documentation overview, see our [Documentation Guide](docs/README.md)  
> 🔄 **Process Flows**: View complete business workflows in our [Sequence Diagrams](docs/sequence/)

## 🏗 System Architecture
```yousaha/
├── app/                    # Application logic
│   ├── Http/Controllers/   # HTTP Controllers
│   ├── Models/            # Eloquent Models
│   └── Providers/         # Service Providers
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── views/            # Blade templates with Bootstrap
│   ├── css/              # Custom stylesheets
│   └── js/               # JavaScript files
├── public/               # Static assets and Bootstrap files
└── routes/               # Application routes```
## 📚 Documentation

### Complete Documentation Suite

This project includes comprehensive documentation covering all aspects of the system:

#### 📖 [User Guide](docs/USER_GUIDE.md)
Complete end-user manual with step-by-step instructions for all system features:
- Getting started and account setup
- Daily operations workflows
- Module-specific user guides
- Troubleshooting and best practices

#### 🏗️ [System Documentation](docs/SYSTEM_DOCUMENTATION.md) 
Comprehensive system overview and business processes:
- System architecture and features
- Business process flows
- Business rules and validations
- Technical architecture overview

#### 🔧 [Technical Implementation Guide](docs/TECHNICAL_IMPLEMENTATION.md)
Developer-focused technical documentation:
- System architecture and design patterns
- Database design and relationships
- Business logic implementation
- Security, testing, and deployment guides

#### 🌐 [API Documentation](docs/API_DOCUMENTATION.md)
Complete REST API reference:
- Authentication and authorization
- All endpoint specifications
- Request/response formats
- Error handling and status codes

#### 🔄 [Sequence Diagrams](docs/sequence/)
Detailed process flow diagrams for all business operations:
- Authentication and user management workflows
- Complete business process flows by module
- Error handling and transaction management
- System integration points and interactions

### Database Model
This application uses 39 main tables that include:
- User management and authentication
- Company and multi-tenant support
- Complete ERP modules (Inventory, Sales, Purchase, Finance, HR)
- Audit trail and logging

### Integration Features
- RESTful API for external integration
- SMTP email integration
- AI-powered employee evaluations
- Multi-format report exports (PDF, Excel, CSV)

### Process Documentation
- **[Sequence Diagrams](docs/sequence/)** - 54 detailed workflow diagrams organized by functional modules
- Complete business process visualization for implementation and testing
- websequencediagrams.com compatible format for easy modification

## 🤝 Contributing

This project was developed as part of academic research. Contributions and feedback are highly appreciated:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Create a Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` file for more information.

## 👨‍💻 Author

**Mochammad Dieqy Dzulqaidar**  
Student ID: 91123078  
Master's Program in Information Systems Management  
Gunadarma University  
Jakarta, 2025

## 📞 Contact

- Email: [your-email@example.com]
- LinkedIn: [Your LinkedIn Profile]
- GitHub: [Your GitHub Profile]

## 🙏 Acknowledgments

- Gunadarma University - Master's Program in Information Systems Management
- PT Gimsak Teknologi Indonesia
- PT Megah Lautan Utama  
- PT Lingkar Nusa Teknologi
- Laravel Community
- Open Source Community

---

**Note**: This application was developed for research and educational purposes. For production use, it is recommended to conduct additional security audits and performance optimization.
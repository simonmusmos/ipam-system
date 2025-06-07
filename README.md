# IPAM System

An open-source, microservices-based IP Address Management (IPAM) system designed to help network administrators efficiently manage IP addresses, subnets, and related resources.

## Features

- **Microservices Architecture**: Modular services for authentication, IP management, auditing, and API gateway.
- **User-Friendly Interface**: Intuitive web UI built with Vue.js for seamless interaction.
- **Authentication & Authorization**: Secure user management with role-based access control.
- **Audit Logging**: Comprehensive tracking of system activities for accountability.
- **Containerized Deployment**: Dockerized services for easy setup and scalability.

## Architecture

The system comprises the following services:

- `auth-service`: Handles user authentication and authorization.
- `ip-management-service`: Manages IP address allocations and subnet configurations.
- `audit-service`: Records system events and user activities.
- `gateway-service`: Acts as the API gateway, routing requests to appropriate services.
- `ui`: Frontend application providing a graphical interface for users.

Each service is developed using PHP (Laravel framework) and communicates via RESTful APIs.

## Getting Started

### Prerequisites

- Docker
- Docker Compose

### Installation

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/simonmusmos/ipam-system.git
   cd ipam-system

2. **Start the Services**:

   ```bash
   docker-compose up -d

3. **Access the Application**:

   Navigate to `http://localhost:8000` in your web browser to access the IPAM system UI.

## Usage

Upon accessing the application:

1. **Login**: Use the default credentials or register a new account.
2. **Manage IP Addresses**: Add, edit, or delete IP addresses and subnets.
3. **Audit Logs**: View system activities and user actions for monitoring purposes.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

## License

This project is for demo purposes only and is not licensed for commercial use.

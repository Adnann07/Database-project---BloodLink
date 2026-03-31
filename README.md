
======
BloodLink – Blood Bank Management System
========================================

BloodLink is a centralized **Blood** Bank Management System designed to streamline donor registration, blood inventory tracking, and blood request processing for hospitals and patients.  
It provides a unified web platform connecting admins, donors, and hospital staff to ensure safe, quick, and organized blood allocation.

### Team Members

| ID         | Name           | Email                                           | Role              |
|-----------|----------------|-------------------------------------------------|-------------------|
| 20230104019 | Mohammad Adnan | [adnan.cse.20230104019@aust.edu](mailto:adnan.cse.20230104019@aust.edu) | Lead              |
| 20230104016 | Sanjida Amin   | [erin.cse.20230104016@aust.edu](mailto:erin.cse.20230104016@aust.edu) | Front-end Developer |
| 20230104004 | Hasib Shahriar  | [hasib.cse.20230104004@aust.edu](mailto:hasib.cse.20230104004@aust.edu) | Back-end Developer |


## Core Idea and Roles

- Roles: Admin, Donor, Hospital/Receiver.  
- Goal: Maintain accurate stock by blood group, manage donor data, and process blood requests efficiently through a simple dashboard-based interface.

## Technology Stack

| Layer      | Technology                            |
|-----------|----------------------------------------|
| Frontend  | React , HTML , CSS , JavaScript       |
| Backend   | Laravel                               |
| Database  | MySQL                                 |
| Rendering | Server Side Rendering                 |


## Figma prototype
www.figma.com/proto/dCJ7SxhbsOUaWbDFfgdokb/BloodLink


## Core Features

### 1. Authentication & Dashboards

- Login/registration with roles: Admin, Donor, Hospital/Receiver.  
- Role-based redirection after login (Admin dashboard, Donor dashboard, Hospital dashboard).  
- Basic dashboards with simple cards for counts (total donors, available units, pending requests).

### 2. Donor & Inventory Management

- **Admin:**
  - Add, view, and update donor profiles.  
  - Record blood donations and update blood stock by blood group.  
  - Mark units as used, expired, or available.

- **Donor:**
  - View personal donation history.  
  - See next eligible donation date and basic profile details.

- **Inventory:**
  - View table of current units per blood group.  
  - Simple status indicators (Available, Low, Critical).

### 3. Blood Request Workflow

- **Hospital/Receiver:**
  - Create blood requests with patient details, required blood group, units, urgency, and hospital info.  
  - View status of submitted requests (Pending, Approved, Rejected, Fulfilled).

- **Admin:**
  - View list of all blood requests.  
  - Approve or reject requests and allocate units from inventory.  
  - Track history of fulfilled/denied requests.

### 4. AI-Powered Module

- **AI FAQ / Chatbot Flow (alternative):**
  - User types question in a chat widget (e.g., “Can I donate if I had fever last week?”).  
  - Frontend sends query to backend, backend forwards to AI API.  
  - Response is displayed in the chat UI, optionally logged for future analysis.

## Milestones

### Milestone 1 – Auth & Basic Structure

- Implement registration and login for Admin, Donor, and Hospital/Receiver.  
- Role-based redirection and simple dashboards.  
- Basic user profile management.

### Milestone 2 – Donor & Inventory

- CRUD for donors (Admin).  
- Record donations and automatically update inventory.  
- Display inventory by blood group with simple status indicators.

### Milestone 3 – Requests & AI

  - FAQ/chatbot for general users.  


## Additional Pages

- **Contact & Support Page**
  - Simple form with name, email, role, and message.  
  - Submissions visible in admin panel or forwarded via email.

- **FAQ Page**
  - Static FAQs about donation and system usage.  
  - Optional AI chatbot for dynamic answers.


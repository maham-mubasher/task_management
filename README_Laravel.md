# Task Management Application

This project is a full-stack task management application that allows users to create, edit, sort, filter, and paginate tasks. Users can attach files to tasks, assign tags, prioritize tasks, and filter by various criteria. This application is built with Laravel on the backend and Vue.js on the frontend.

## Table of Contents

- [Features](#features)
- [Setup](#setup)
- [Frontend Structure](#frontend-structure)
- [Backend API Endpoints](#backend-api-endpoints)
- [Usage](#usage)
- [Project Structure](#project-structure)

## Features

- **CRUD Operations**: Users can create, edit, and delete tasks.
- **Sorting**: Sort tasks by title, priority, due date, creation date, and completion date.
- **Filtering**: Filter tasks by priority, date ranges, and text queries.
- **Pagination**: Paginate tasks with dynamic data loading.
- **Attachments**: Optional file attachments for tasks, including images, videos, and documents.
- **Tags**: Assign multiple tags to tasks, with the ability to add, remove, and reuse tags.
- **Modals for Creating and Editing**: Use a modal form for creating and editing tasks with dynamically filled fields for easy modification.

## Setup

### Prerequisites

- **Node.js** (version >= 14)
- **Composer** (for PHP dependencies)
- **MySQL** 

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd task-management-app

2. Install the backend dependencies:

    ```bash
    composer install

3. Install the frontend dependencies:

    ```bash
    npm install
4. Create a copy of the .env file:

    ```bash
    cp .env.example .env
5. Generate an application key:

    ```bash
    php artisan key:generate

6. Set up the database by configuring your .env file with your database credentials:

    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE= task_todo
    DB_USERNAME=your_db_username
    DB_PASSWORD=your_db_password

7. Run the migrations to set up the database schema:

    ```bash
    php artisan migrate

8. Seed the database:

    ```bash
    php artisan db:seed class=PrioritySeeder

9. Build the frontend assets:

    ```bash
    npm run dev

10. Start the development server:

    ```bash
    php artisan serve

Now, you should be able to access the application in your browser at http://localhost:8000.


## Backend API Endpoints

| Endpoint           | HTTP Method | Description                                  | Parameters               |
|--------------------|-------------|----------------------------------------------|--------------------------|
| `/tasks`           | `GET`       | Retrieve all tasks with optional filters and pagination. | `params` (filters, pagination) |
| `/tasks/:id`       | `GET`       | Retrieve a specific task by ID.              | `id` (task ID)           |
| `/tasks`           | `POST`      | Create a new task.                           | `data` (task details)    |
| `/tasks/:id`       | `PUT`       | Update an existing task by ID.               | `id` (task ID), `data` (updated task details) |
| `/tasks/:id`       | `DELETE`    | Delete a task by ID.                         | `id` (task ID)           |
| `/tasks/:id/complete` | `PUT`   | Mark a task as complete.                     | `id` (task ID)           |
| `/tasks/:id/incomplete` | `PUT` | Mark a task as incomplete.                   | `id` (task ID)           |
| `/tasks/:id/archive` | `PUT`     | Archive a task by ID.                        | `id` (task ID)           |
| `/tasks/:id/restore` | `PUT`     | Restore an archived task by ID.              | `id` (task ID)           |
| `/priorities`      | `GET`       | Retrieve list of priorities.                 | None                     |
| `/getArchives`     | `GET`       | Retrieve all archived tasks.                 | None                     |
| `/tags`            | `GET`       | Retrieve list of all tags.                   | None                     |
| `/tags`            | `POST`      | Create a new tag.                            | `data` (tag details)     |



## Usage

The task management application allows users to manage tasks through a dynamic interface with filtering, sorting, and CRUD operations. Below are the steps to use key features:

1. **Viewing Tasks**: 
   - The main page displays a list of tasks with columns for Task title, Priority, Due Date, Status, Tags, Attachments, and Actions.
   - Pagination controls are provided at the bottom of the task list to navigate between pages.

2. **Creating a New Task**:
   - Click the **+ New Task** button to open a modal form for creating a new task.
   - Fill in the fields: Task Title, Description, Priority, Due Date, Tags, and Attach Files.
   - Add new tags or select existing ones, and optionally attach files to the task.
   - Click **Save** to create the task, which will then appear in the task list.

3. **Filtering Tasks**:
   - Filters are available to refine the displayed tasks by:
     - **Status** (Completed or Pending)
     - **Priority** (Urgent, High, Normal, Low)
     - **Due Date** (Specify a date range)
     - **Title Search** (Enter text to search by title)
   - After selecting your desired filters, click **Apply Filters**. Use the **Clear Filters** button to reset filters.

4. **Sorting Tasks**:
   - The **Sort By** dropdown allows sorting tasks by:
     - **Created Date**
     - **Completed Date**
     - **Priority**
     - **Due Date**
   - The **Order** dropdown lets you select ascending or descending order.

5. **Completing and Archiving Tasks**:
   - Mark tasks as **Complete** or **Incomplete** using the status toggle button in the Actions column.
   - Archive tasks by clicking the **Archive** button, which moves them to the Archived list.
   - Access archived tasks by clicking **Archived Tasks**. Archived tasks can be restored or permanently deleted.

6. **Viewing and Restoring Archived Tasks**:
   - In the Archived Tasks modal, view and manage archived tasks.
   - Tasks can be **Restored** to the main task list or **Deleted** permanently from the archive.

7. **Viewing Task Details**:
   - Click on a task title in the main list to view its details, including description, priority, due date, status, tags, and attachments in a detailed modal.
   - Attachments are downloadable directly from this view.

Each action interacts with backend API endpoints, keeping the task list up to date and reflecting the latest data with each operation.


## Project Structure

This project follows a well-organized structure, separating frontend and backend logic, components, resources, and configurations for ease of development and maintainability.

### Backend (Laravel)

- **app/**
  - **Actions**: Contains Fortify actions used for authentication.
  - **Events**: Custom events such as `TaskCreated`, `TaskArchived`, and `TaskCompleted`.
  - **Http/**
    - **Controllers**: Application logic controllers like `TaskController`, `PriorityController`, and `TagController` to handle various API requests.
    - **Middleware**: Custom middleware such as `HandleInertiaRequests`.
    - **Requests**: Form request validation classes, e.g., `TaskRequest`.
    - **Resources**: API resource transformers like `TaskResource`, `TagResource`, and `AttachmentResource` to format responses.
  - **Models**: Database models including `Task`, `Tag`, `Priority`, `User`, and `Attachment`.
  - **Observers**: Includes `TaskObserver` to handle model events such as creating or deleting tasks.
  - **Policies**: Authorization policies, e.g., `TaskPolicy`, to manage access control.
  - **Providers**: Service providers, e.g., `AppServiceProvider`, `AuthServiceProvider`, used for registering application services.
  - **Services**: Business logic, e.g., `TaskService` which manages task-related operations.

- **routes/**
  - **api.php**: Defines API routes.
  - **web.php**: Defines web routes.

- **resources/**
  - **views**: Blade templates for server-rendered views.
  - **js/**: JavaScript files and frontend components for the application.
    - **Components**: Vue components for various UI elements, including `Tasks.vue`, `TaskForm.vue`, `TaskDetails.vue`, and shared components like `DialogModal.vue`, `PrimaryButton.vue`, `SecondaryButton.vue`.
    - **Composables**: Reusable logic, including `UseFilters.js`, `UseTags.js`, and `UseTasks.js`.
    - **Layouts**: Layout components like `AppLayout.vue`.
    - **Pages**: Organized into subfolders for different parts of the app, such as `API`, `Auth`, `Profile`, with pages like `Dashboard.vue`, `PrivacyPolicy.vue`, `TermsOfService.vue`, and `Welcome.vue`.

- **config/**: Configuration files for various services and settings.

- **public/**: Contains publicly accessible assets.

- **storage/**: Used for file uploads and caching.

- **tests/**: Test files for application testing.

### Frontend (Vue)

- **router/**: Vue Router configuration files (`index.js`).
- **services/**: API services, e.g., `api.js` which defines Axios API calls for tasks, tags, priorities, etc.
- **Pages**: Contains page components for different routes.
  - **API/**: Pages related to API interactions.
  - **Auth/**: Pages for authentication-related components.
  - **Profile/**: Profile management pages such as `Dashboard.vue`, `PrivacyPolicy.vue`, and `TermsOfService.vue`.

- **.env**: Environment configuration file.

- **composer.json**: Laravel dependencies and package information.

- **package.json**: JavaScript dependencies and package information for frontend.

This structure promotes separation of concerns, making it easy to maintain and scale the application. Each section has a specific role, with controllers managing HTTP requests, models handling data interactions, and Vue components managing frontend user interfaces.



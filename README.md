# FEED - Simple Social Network

<img src="https://imgur.com/AorjW3n.png" alt="" />

![PHP](https://img.shields.io/badge/PHP-777BB4.svg?style=for-the-badge&logo=PHP&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630.svg?style=for-the-badge&logo=Composer&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=MySQL&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26.svg?style=for-the-badge&logo=HTML5&logoColor=white)
![SASS](https://img.shields.io/badge/Sass-CC6699.svg?style=for-the-badge&logo=Sass&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=for-the-badge&logo=JavaScript&logoColor=black)

A straightforward social network application with features for creating posts and commenting.

## 💻 Requirements:

- PHP >= 8.0
- Composer
- MySQL Database

## 🚀 Running the Backend

### 1. Setup Database

Make sure the MySQL server is running to connect and generate tables using the `database-schema.sql` file. Set the environment variables in the `.env` file (refer to `.env.example`).

### 2. Install Dependencies

```bash
composer install
```

## 3. Starting backend
```
php <localhost or ip>:8000
```

## 🚀 Running the frontend

`Note`: The frontend runs independently from the backend, and you can use a web server of your choice such as WAMP, Apache, etc.

In the `scripts/utils.js` file, modify the baseUrl variable to define the backend endpoint to be used.

# Google Drive API - Clone

This Laravel API serves as the backend for a cloud-based file management system similar to Google Drive. Users can authenticate, create folders, upload files, organize content in nested structures, restore deleted items from trash, and share folders with other users.

The API is built using clean RESTful principles and provides secure, scalable file operations integrated with **Google Cloud Storage**. All requests are protected via OAuth 2.0 tokens, and unit tests are included for stable, test-driven development.

## Features

- Login and Signup.
- File upload.
- Delete, and restore files/folders
- Trash system for soft-deleted items
- Folder sharing between users (with access control)
- Dockerized setup.
- Unit-tested APIs.

## Technologies Used

| Tool/Tech                   | Purpose                                     |
| --------------------------- | ------------------------------------------- |
| **PHP 8.3**                 | Server-side backend language                |
| **Laravel 12**              | Web application framework                   |
| **PostgreSQL**              | Relational database                         |
| **Laravel Passport**        | OAuth 2.0 authentication and token handling |
| **Docker & Docker Compose** | Containerization                            |
| **Google Cloud Storage**    | Remote file storage                         |
| **PHPUnit**                 | Unit testing                                |

## API Documentation

[Architecture Design](./design.md)  
[Swagger](./swagger.yaml)

## Learning Resources

- [Backend web development - a complete overview](https://www.youtube.com/watch?v=XBu54nfzxAQ)
- [20 System Design Concepts Explained in 10 Minutes](https://www.youtube.com/watch?v=i53Gi_K3o7I)
- [System Design was HARD until I Learned these 30 Concepts](https://www.youtube.com/watch?v=s9Qh9fWeOAk)
- [System Design: Why Is Docker Important?](http://youtube.com/watch?v=QEzbZKtLi-g)
- [How To Choose The Right Database?](https://www.youtube.com/watch?v=kkeFE6iRfMM)
- [Dropbox system design | Google drive system design | System design file share and upload](https://www.youtube.com/watch?v=U0xTu6E2CT8)
- [PostgreSQL CRASH COURSE - Learn PostgreSQL in 2024](https://www.youtube.com/watch?v=zw4s3Ey8ayo)
- [System Design Interview: Design Dropbox or Google Drive](https://www.youtube.com/watch?v=_UZ1ngy-kOI&t=220s)
- [Google Login using Socialite in Laravel](https://medium.com/@mimranisrar6/how-to-add-a-google-login-using-socialite-in-laravel-21f6eebafcec)

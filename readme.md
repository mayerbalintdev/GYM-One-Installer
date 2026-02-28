<p align="center">
  <img src="https://gymoneglobal.com/assets/img/text-color-logo.png" alt="GYM One Logo" width="320">
</p>

<h1 align="center">GYM One — Installer</h1>

<p align="center">
  <a href="https://github.com/mayerbalintdev/GYM-One-Installer/releases">
    <img src="https://img.shields.io/badge/version-1.3.0-blue.svg" alt="Version">
  </a>
  <img src="https://img.shields.io/badge/PHP-%3E%3D8.1-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-%3E%3D5.7-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/platform-Ubuntu%20%7C%20Apache%20%7C%20Nginx-lightgrey" alt="Platform">
</p>

<p align="center">
  The official installer for GYM One — automatically configures your server environment, sets up the database, and gets your gym management system running in minutes.
</p>

---

## 📋 Table of Contents

1. [Requirements](#-requirements)
2. [Before You Begin](#-before-you-begin)
3. [Installation Steps](#-installation-steps)
4. [Post-Installation](#-post-installation)
5. [Troubleshooting](#-troubleshooting)
6. [Updating from a Previous Version](#-updating-from-a-previous-version)
7. [Support](#-support)

---

## ⚙️ Requirements

Before running the installer, make sure your server meets the following requirements:

| Requirement | Minimum Version |
|---|---|
| PHP | 8.1 or higher |
| MySQL / MariaDB | 5.7 / 10.4 or higher |
| Web Server | Apache 2.4+ or Nginx 1.18+ |
| Composer | Latest stable |
| OS | Ubuntu 20.04+ recommended |

### Required PHP Extensions

The following PHP extensions must be enabled:

- `pdo_mysql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `curl`
- `zip`
- `gd`

> You can check your PHP extensions with `php -m` on the server.

---

## 🔍 Before You Begin

- Make sure your web server is running and accessible
- Have your MySQL credentials ready (host, database name, username, password)
- Ensure the web server has **write permissions** on the target directory
- It is recommended to run the installer on a **clean server** or a **fresh directory** — do not install over an existing GYM One instance without backing up first

---

## 🚀 Installation Steps

### 1. Upload Installer Files

Upload the GYM One installer files to the root directory of your web server (e.g., `/var/www/html/`).

All files must be accessible via the web server:
```
http://your-server-ip/
```

### 2. Open the Installer

Navigate to the installer URL in your web browser:
```
http://your-server-ip/
```

The installer will launch automatically and guide you through each step.

### 3. System Check

The installer will first run a **system compatibility check**, verifying:
- PHP version and required extensions
- File and directory permissions
- Database connectivity

Fix any issues flagged in red before proceeding.

### 4. Database Configuration

Provide your database connection details:
- **Host** (usually `localhost`)
- **Database name**
- **Username**
- **Password**

The installer will create the required tables and seed the initial data automatically.

### 5. Application Configuration

Set up your initial application settings:
- Site name and URL
- Default timezone
- Admin account credentials (email + password)
- Optional: SMTP email settings for notifications

### 6. Finish Installation

Click **"Finish Installation"** once all steps are complete. The installer will:
- Write the configuration files
- Set up directory permissions
- Remove installer access for security

### 7. Access GYM One

Your GYM One instance is now live at:
```
http://your-server-ip/
```
or
```
http://your-domain.com/
```

Log in with the admin credentials you set in Step 5.

> ⚠️ **Important:** Delete or restrict access to the installer directory after installation is complete to prevent unauthorized re-installation.

---

## 🔧 Post-Installation

After completing the installation, we recommend the following:

- **Set up HTTPS** — Use Let's Encrypt or a commercial SSL certificate
- **Configure email (SMTP)** — Required for membership notifications and reminders
- **Set up automated backups** — Regularly back up your database and uploaded files
- **Review file permissions** — Ensure sensitive directories (e.g., `config/`, `storage/`) are not publicly accessible
- **Test the admin panel** — Verify all modules are working correctly before going live

---

## 🛠️ Troubleshooting

### Installer won't load
- Check that your web server is running (`systemctl status apache2` or `nginx`)
- Verify the files are in the correct directory and accessible via the browser

### Database connection error
- Double-check your credentials and ensure MySQL is running (`systemctl status mysql`)
- Make sure the database user has sufficient privileges (`GRANT ALL ON db.* TO 'user'@'localhost'`)

### Missing PHP extensions
- Install missing extensions, e.g.: `sudo apt install php8.1-mbstring php8.1-xml php8.1-zip php8.1-gd`
- Restart your web server after installing extensions

### Permission errors
- Run `sudo chown -R www-data:www-data /var/www/html/` and `sudo chmod -R 755 /var/www/html/`

### Still stuck?
- Check web server logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Check PHP logs: `/var/log/php*.log`
- Visit the [GYM One Documentation](https://gymoneglobal.com/docs) for detailed guides

---

## 🔄 Updating from a Previous Version

If you are upgrading from an earlier version of GYM One:

1. **Back up your database** before doing anything else
2. **Back up your configuration files** (e.g., `.env`, `config/`)
3. Upload the new installer files to your server
4. Follow the on-screen upgrade instructions — the installer will detect your existing installation
5. Verify all settings and test thoroughly after the upgrade

> Skipping backups before an upgrade is strongly discouraged.

---

## 📬 Support

If you have questions or run into issues during installation, we're here to help:

- **Documentation:** [https://gymoneglobal.com/docs](https://gymoneglobal.com/docs)
- **Email:** [center@gymoneglobal.com](mailto:center@gymoneglobal.com)
- **Website:** [https://gymoneglobal.com](https://gymoneglobal.com)
- **GitHub Issues:** [Open an issue](https://github.com/mayerbalintdev/GYM-One-Installer/issues)

Press inquiries: [press@gymoneglobal.com](mailto:press@gymoneglobal.com)

---

<p align="center">
  Thank you for choosing GYM One! 💪<br>
  <i>Version 1.3.0 — Built with passion for the fitness community.</i>
</p>
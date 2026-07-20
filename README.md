# NeoAdzan

NeoAdzan is a robust and modern Islamic Prayer Time Schedule application built with PHP and MySQL. It provides accurate prayer times based on geographical coordinates and offers various calculation methods used worldwide.

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/cahyadsn/neoadzan/master/LICENSE)
[![GitHub forks](https://img.shields.io/github/forks/cahyadsn/neoadzan.svg)](https://github.com/cahyadsn/neoadzan/network)
[![GitHub stars](https://img.shields.io/github/stars/cahyadsn/neoadzan.svg)](https://github.com/cahyadsn/neoadzan/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/cahyadsn/neoadzan.svg)](https://github.com/cahyadsn/neoadzan/issues)

## SCREENSHOT
[![screenshot](https://github.com/cahyadsn/neoadzan/blob/master/img/ss002.png?raw=true 'neoadzan demo v2.0')](https://neoadzan.cahyadsn.com/)

## ✨ Features

- **Accurate Calculations**: Supports multiple calculation methods (Depag RI, MWL, ISNA, Egypt, Makkah, Karachi, Tehran, Jafari).
- **Dark/Light Mode**: Integrated theme toggle with persistent user preference via cookies.
- **Theme Color Persistence**: 12 color themes with cookie-based persistence (survives across sessions and browser restarts).
- **High Performance**: Integrated file-based caching system for near-instant schedule retrieval and reduced server load.
- **Hijri Calendar**: Integrated Hijri date conversion and display.
- **Regional Support**: Includes Indonesian regional data (Province & District) based on **Kepmendagri No 300.2.2-2430 Tahun 2025**.
- **Flexible Schedules**: View daily and monthly prayer schedules.
- **Modern UI**: Clean, responsive design using native modern CSS architecture and Vanilla JavaScript (No jQuery/Zepto dependencies).
- **API Ready**: Provides JSON endpoints for easy integration with other platforms.
- **Customizable**: Adjustable calculation parameters for Imsak, Dhuha, and more.

## 🛠 Technology Stack

- **Backend**: PHP 7.4+ (OOP with Traits)
- **Caching**: Custom file-based caching system
- **Database**: MySQL / MariaDB (using PDO with query optimization)
- **Frontend**: Vanilla JavaScript (ES6+), Modern CSS3
- **Icons**: Font Awesome 4.7
- **Configuration**: Dotenv support for environment variables

## 🚀 Installation

1. **Download**: Clone this repository or download the source code.
   ```bash
   git clone https://github.com/cahyadsn/neoadzan.git
   ```
2. **Database Setup**:
   - Create a new database (e.g., `wilayah`).
   - Import the database schema from `db/wilayah_level_1_2.sql`.
3. **Configuration**:
   - Copy `.env.example` to `.env`.
   - Update the database credentials in the `.env` file.
   - Ensure the `cache/` directory is writable by the web server.
4. **Deploy**: Copy all files to your web server's document root.
5. **Access**: Navigate to `http://localhost/neoadzan` in your browser.

## 📡 API Endpoints

NeoAdzan provides several API endpoints:

- **Get Daily Schedule**: `api/getDaily.php?lat={lat}&lng={lng}&z={timezone}`
- **Get Monthly Schedule**: `api/getMonthly.php?lat={lat}&lng={lng}&z={timezone}&y={year}&m={month}`
- **Get Provinces**: `api/getProvince.php`
- **Get Districts**: `api/getDistrict.php?id={province_id}`

## 🔄 Recent Updates

- **2026-07-20**: Implemented further performance optimizations:
  - Cached the database queries for provinces and default districts on the initial page load of `index.php` using the file-based cache (30-day TTL).
  - Updated API endpoints `api/getDaily.php` and `api/getMonthly.php` to support both `GET` and `POST` requests, utilizing request-specific parameters to construct robust, non-colliding cache keys.
- **2026-07-02**: Added cookie-based persistence for theme preferences:
  - Theme color and dark/light mode now persist via cookies (`neoadzan_theme`, `neoadzan_mode`) with 1-year expiry.
  - Preferences survive session expiry, browser restarts, and device reboots.
  - Server-side rendering of `data-mode` attribute eliminates flash of wrong theme on page load.
  - Replaced `localStorage`-based mode persistence with unified cookie approach.
  - Added whitelist validation for color and mode values.
- **2026-06-11**: Implemented comprehensive performance optimizations and new features:
  - Added file-based caching system for API and initial loads.
  - Optimized database queries and connection handling.
  - Improved browser caching via optimized asset versioning and HTTP headers.
  - Added dark/light mode toggle with persistent user preference.
- **2026-06-08**: Modernized UI/UX with native modern CSS and Vanilla JS.
- **2026-06-08**: Updated regional data to Kepmendagri No 300.2.2-2430 Year 2025.
- **2025-03-01**: Migrated from jQuery to Zepto.min.js (later replaced by Vanilla JS).
- **2024-05-15**: Updated regional data to Kepmendagri No 100.1.1-6117 Year 2022.

## ☕ Donation

Support the development of this project:

- **Bank BCA Digital (Blu)**: 000 576 776 186
- **Bank Jago**: 5003 5796 1022
- **Bank Sinarmas**: 005 462 4719
- **Bank Syariah Indonesia (BSI)**: 821-342-5550
- **PayPal**: [https://paypal.me/cahyadwiana](https://paypal.me/cahyadwiana)
- **QRIS**: Scan the QR code below

![QRIS Donation](https://github.com/cahyadsn/wilayah/blob/master/docs/qr_code.cahyadsn.png?raw=true)

## 📞 Contact

- **Facebook**: [cahya.dsn](https://m.facebook.com/cahya.dsn)
- **Email**: cahyadsn@gmail.com
- **Demo**: [neoadzan.cahyadsn.com](http://neoadzan.cahyadsn.com)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.


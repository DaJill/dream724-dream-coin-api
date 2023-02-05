### 圓圓 - 後端 (Laravel v5.7)

---

### 環境要求
自己看`composer.json`

---
### 安裝

#### 1. git clone 此專案

#### 2.進到專案目錄下，複製 .env.example
```json
$ cd dream-coin-api
$ cp .env.example .env
```

#### 3.編輯.env內容
編輯搜尋條件：
* DB相關
* AWS相關
* JWT相關
* ＭAIL相關
* Auth Key相關
* JWT相關

#### 4. 安裝套件
```json
$ composer install
```

#### 5. 建立資料庫
```json
$ php artisan migrate
```

#### 6. 產生假資料`(測試用)`
```json
$ php artisan db:seed
```
---

#### JWT secret key產生`(有需要才用)`
```json
$ php artisan jwt:secret

This will invalidate all existing tokens. Are you sure you want to override the secret key? (yes/no) [no]:
> yes
```
# GlobMall - 面向海外的企业级 PHP 商城

GlobMall 是一个基于 Laravel 13 + PHP 8.5 + MySQL 构建的全功能电商系统，支持中英文双语、Stripe 支付、Twilio 手机验证码、Google reCAPTCHA 等企业级安全特性。

## 技术栈

- **后端框架**: Laravel 13
- **PHP 版本**: PHP 8.5+
- **数据库**: MySQL 5.7 (端口 3307)
- **支付网关**: Stripe
- **短信服务**: Twilio
- **人机验证**: Google reCAPTCHA v2
- **前端框架**: Bootstrap 5.3 + Bootstrap Icons
- **图表库**: Chart.js
- **国际化**: Laravel Localization (中文/英文)

## 核心功能

### 前台功能
- 商品浏览、搜索、分类、品牌筛选
- 购物车管理（AJAX）
- 结算与 Stripe 支付集成
- 订单管理与历史
- 收货地址管理
- 收藏夹（Wishlist）
- 商品评价与评分
- 用户注册/登录（支持手机验证码）
- Google reCAPTCHA 人机验证

### 后台功能
- Dashboard 数据面板（订单统计、营收图表）
- 商品 CRUD（多语言）
- 分类/品牌管理
- 订单管理（状态更新、日志追踪）
- 优惠券管理
- 用户评价审核
- 用户管理
- 订单导出 CSV

## 快速开始

### 环境要求
- PHP >= 8.5
- MySQL >= 5.7
- Composer
- Node.js (可选)

### 安装步骤

1. 克隆项目
```bash
cd globmall
composer install
cp .env.example .env
```

2. 配置 .env 文件
```bash
# 数据库
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=globmall
DB_USERNAME=root
DB_PASSWORD=123456

# Google reCAPTCHA (可选)
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_ENABLED=true

# Twilio (可选，用于短信验证码)
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_ENABLED=false
```

3. 生成应用密钥
```bash
php artisan key:generate
```

4. 数据库迁移
```bash
php artisan migrate --seed
```

5. 启动开发服务器
```bash
php artisan serve --host=0.0.0.0 --port=8080
```

6. 访问
- 前台: http://127.0.0.1:8080
- 后台: http://127.0.0.1:8080/admin/dashboard

## 默认账号

- **管理员**: `admin@globmall.com` / `123456`
- **买家**: `john@test.com` / `123456`

## 安全特性

1. **CSRF 保护**: Laravel 原生 CSRF Token 验证
2. **XSS 防护**: Blade 模板自动转义
3. **SQL 注入防护**: Eloquent ORM 参数绑定
4. **人机验证**: Google reCAPTCHA v2 登录/注册防护
5. **短信验证码**: Twilio 手机验证码（可选启用）
6. **防刷机制**: 验证码 60 秒内防重复发送
7. **密码哈希**: bcrypt 算法
8. **会话安全**: 登录会话刷新

## 国际化

- URL 支持 `/zh` 和 `/en` 前缀
- 所有界面文本通过 Laravel Localization 管理
- 商品/分类/品牌支持多语言存储

## 目录结构

```
globmall/
├── app/
│   ├── Http/Controllers/     # 控制器（前台 + 后台 Admin/）
│   ├── Models/               # Eloquent 模型
│   └── Http/Middleware/      # 中间件（含 VerifyRecaptcha）
├── database/
│   ├── migrations/           # 29 张数据表迁移
│   └── seeders/              # 初始数据
├── resources/
│   ├── views/                # Blade 模板
│   └── lang/                 # 中英文翻译
├── routes/
│   └── web.php               # 前台与后台路由
└── .env                      # 环境配置
```

## 扩展方向

- REST API (Laravel Sanctum)
- 多币种支持
- 多商家（Multi-vendor）
- 第三方物流跟踪集成
- 实时聊天客服（WebSocket）

## License

MIT
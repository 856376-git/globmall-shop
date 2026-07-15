<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Product::truncate();
ProductTranslation::truncate();
ProductImage::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$categories = Category::all();
$brands = Brand::all();

if ($categories->isEmpty() || $brands->isEmpty()) {
    echo "Error: no categories or brands!\n";
    exit(1);
}

$templates = [
    ['Wireless Earbuds Pro','无线耳机专业版','主动降噪，超长续航，蓝牙5.3',89.99],
    ['Smart Watch Ultra','智能手表旗舰版','心率血氧监测，50米防水',249.99],
    ['Mechanical Keyboard 75%','机械键盘75%','RGB背光，热插拔轴体，PBT键帽',129.99],
    ['Gaming Mouse RGB','游戏鼠标RGB','12000DPI，7键可编程，轻量化设计',59.99],
    ['USB-C Docking Station','Type-C扩展坞','HDMI+USB3.0+SD卡槽，支持双屏',79.99],
    ['Portable SSD 512GB','移动固态硬盘512GB','读取速度1050MB/s，Type-C接口',119.99],
    ['4K Webcam Pro','4K高清摄像头','自动对焦，降噪麦克风，广角镜头',99.99],
    ['Bluetooth Speaker Mini','蓝牙音箱mini','360度环绕，IPX7防水，20小时续航',69.99],
    ['Wireless Charger 15W','无线充电器15W','快充协议，LED指示灯，防滑硅胶',29.99],
    ['Phone Case Premium Leather','真皮手机壳','头层牛皮，手工缝制，磁吸支架',49.99],
    ['Tablet Stand Aluminum','铝合金平板支架','多角度可调，折叠便携，散热设计',34.99],
    ['Noise Canceling Headphones','头戴降噪耳机','40mm驱动单元，主动降噪，30小时续航',179.99],
    ['Smart Home Hub Pro','智能家庭中枢','语音控制，ZigBee3.0，多设备联动',199.99],
    ['Action Camera 4K','运动相机4K','电子防抖，防水30米，WiFi直连',299.99],
    ['VR Headset Standalone','VR一体机','4K双眼，空间定位，手柄追踪',399.99],
    ['Mini Drone 4K','迷你无人机4K','航拍4K，30分钟续航，一键返航',499.99],
    ['Robot Vacuum LIDAR','激光导航扫地机','LDS激光导航，自动回充，APP远程',599.99],
    ['Air Purifier HEPA','空气净化器HEPA','H13级过滤，静音运行，PM2.5显示',199.99],
    ['Smart Doorbell Video','可视智能门铃','人脸识别，远程对讲，红外夜视',149.99],
    ['Electric Toothbrush','声波电动牙刷','31000次/分震动，2分钟智能计时',89.99],
    ['Ionic Hair Dryer','负离子吹风机','恒温护发，大风力，静音设计',129.99],
    ['Automatic Coffee Maker','全自动咖啡机','研磨一体，15Bar压力，一键制作',299.99],
    ['High-Speed Blender','高速破壁料理机','无刷电机，6叶刀头，加热功能',179.99],
    ['IH Rice Cooker','IH电饭煲','电磁加热，预约功能，柴火饭模式',149.99],
    ['Air Fryer XL','大容量空气炸锅','无油烹饪，5.5L容量，可视窗',119.99],
    ['Digital Toaster Oven','数字烤箱','上下火独立控温，多层烘焙',89.99],
    ['Slow Masticating Juicer','原汁机','低速研磨，保留营养，渣汁分离',159.99],
    ['Food Processor Multi','多功能料理机','绞肉切菜，多附件，大容量碗',199.99],
    ['Stainless Steel Kettle','不锈钢电水壶','304食品级，温控保温，防干烧',59.99],
    ['Ultrasonic Humidifier','超声波加湿器','静音运行，4L大容量，缺水保护',49.99],
    ['LED Desk Lamp Pro','护眼台灯','无频闪，5档色温，触控调光',79.99],
    ['Smart LED Strip 5m','智能灯带5米','1600万色，APP控制，音乐律动',39.99],
    ['Wireless Presenter','无线翻页笔','2.4G即插即用，激光指示，100米遥控',24.99],
    ['Ergonomic Office Chair','人体工学椅','网布透气，腰托可调，3D扶手',349.99],
    ['Standing Desk Electric','电动升降桌','双电机，记忆高度，静音升降',499.99],
    ['Monitor Light Bar','屏幕挂灯','非对称光源，触控调光，USB供电',69.99],
    ['Cable Organizer Box','理线盒','阻燃材质，隐藏线缆，大容量',34.99],
    ['Laptop Backpack Anti','防盗背包','USB充电口，防水涂层，减震隔层',79.99],
    ['Travel Adapter Universal','万能转换插头','150国通用，快充协议，安全门',29.99],
    ['Power Bank 20000mAh','充电宝20000mAh','PD快充，双向输入，数字显示',59.99],
];

$total = 0;
$index = 1;
foreach ($categories as $cat) {
    $count = rand(10, 15);
    for ($i = 0; $i < $count; $i++) {
        $tpl = $templates[$total % count($templates)];
        $brand = $brands->random();

        $nameEn = $tpl[0] . ' V' . rand(2, 9);
        $nameZh = $tpl[1] . ' ' . rand(2, 9) . '代';
        $slug = Str::slug($nameEn) . '-' . $index;
        $price = $tpl[3] + rand(0, 30);
        $stock = rand(20, 300);
        $isFeatured = ($i < 4);
        $isNew = ($i < 6);

        $product = Product::create([
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'slug' => $slug,
            'price' => $price,
            'compare_price' => $price + rand(20, 80),
            'cost' => round($price * 0.4, 2),
            'stock' => $stock,
            'low_stock_threshold' => 10,
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'status' => true,
            'is_featured' => $isFeatured,
            'is_new' => $isNew,
            'created_at' => now()->subDays(rand(1, 120)),
        ]);

        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => $nameEn,
            'description' => $tpl[2] . ' Premium quality with 1-year warranty.',
            'short_desc' => $tpl[2],
            'meta_title' => $nameEn,
            'meta_description' => $tpl[2],
        ]);

        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'zh',
            'name' => $nameZh,
            'description' => $tpl[2] . ' 高品质产品，享一年质保。',
            'short_desc' => $tpl[2],
            'meta_title' => $nameZh,
            'meta_description' => $tpl[2],
        ]);

        for ($j = 1; $j <= 4; $j++) {
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'products/' . (($total + $j) % 40 + 1) . '.jpg',
                'alt_text' => $nameEn . ' image ' . $j,
                'is_primary' => $j === 1,
            ]);
        }

        $total++;
        $index++;
    }
}

echo "Created {$total} products with translations and images!\n";

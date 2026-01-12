<?php
/**
 * Add All Images to Database
 * 
 * This script scans the assets/images directory and adds all images
 * to the media table with proper metadata.
 * Also assigns service images to their corresponding services.
 */

require_once 'config/config.php';
require_once 'functions/helpers.php';

// Image to service mapping based on filenames
$serviceImageMap = [
    'service.png' => 'household-management',
    'service-2-914x1024.png' => 'event-management',
    'service-3.jpg' => 'protocol-etiquette',
    'service-4.jpg' => 'property-services',
    'service-5.jpg' => 'consulting',
    'service-6.jpg' => 'vip-services',
];

// Image categories/folders
$imageCategories = [
    'logo' => ['logo.png', 'logo-light.png', 'niche-logo-png-150x150.png', 'brand-logo.pdf-3-120x57.png'],
    'logo-favicon' => ['cropped-brand-logo.pdf-1-32x32.png', 'cropped-brand-logo.pdf-1-180x180.png', 'cropped-brand-logo.pdf-1-192x192.png', 'cropped-brand-logo.pdf-1-270x270.png'],
    'hero' => ['niche-society-homepage-1-scaled.jpg', 'sunlit-library-escape-701x1024.jpg'],
    'services' => ['service.png', 'service-2-914x1024.png', 'service-3.jpg', 'service-4.jpg', 'service-5.jpg', 'service-6.jpg'],
    'company' => ['Niche-Society-mission.png', 'Niche-Society-values.png', 'Niche-Society-vison.png', 'Niche-Society-Arabic-ceo-1-661x1024.png', 'Niche-Society-Arabic-CP2.png', 'TEAM-scaled.jpg'],
    'ui' => ['1.svg'],
];

// Alt text mapping (Arabic and English)
$altTextMap = [
    'logo.png' => ['ar' => 'شعار نيش سوسايتي', 'en' => 'Niche Society Logo'],
    'logo-light.png' => ['ar' => 'شعار نيش سوسايتي - فاتح', 'en' => 'Niche Society Logo - Light'],
    'niche-logo-png-150x150.png' => ['ar' => 'شعار نيش سوسايتي مربع', 'en' => 'Niche Society Square Logo'],
    'brand-logo.pdf-3-120x57.png' => ['ar' => 'شعار العلامة التجارية', 'en' => 'Brand Logo'],
    'niche-society-homepage-1-scaled.jpg' => ['ar' => 'داخلية فاخرة - خدمات إدارة الممتلكات', 'en' => 'Luxury Interior - Property Management Services'],
    'sunlit-library-escape-701x1024.jpg' => ['ar' => 'مكتبة خاصة أنيقة', 'en' => 'Elegant Private Library'],
    'service.png' => ['ar' => 'خدمات الإدارة الذكية للممتلكات', 'en' => 'Smart Household Management Services'],
    'service-2-914x1024.png' => ['ar' => 'إدارة الفعاليات الفاخرة', 'en' => 'Luxury Event Management'],
    'service-3.jpg' => ['ar' => 'البروتوكول والإتيكيت', 'en' => 'Protocol & Etiquette'],
    'service-4.jpg' => ['ar' => 'خدمات الممتلكات', 'en' => 'Property Services'],
    'service-5.jpg' => ['ar' => 'الاستشارات', 'en' => 'Consulting Services'],
    'service-6.jpg' => ['ar' => 'خدمات VIP', 'en' => 'VIP Services'],
    'Niche-Society-mission.png' => ['ar' => 'مهمة نيش سوسايتي', 'en' => 'Niche Society Mission'],
    'Niche-Society-values.png' => ['ar' => 'قيم نيش سوسايتي', 'en' => 'Niche Society Values'],
    'Niche-Society-vison.png' => ['ar' => 'رؤية نيش سوسايتي', 'en' => 'Niche Society Vision'],
    'Niche-Society-Arabic-ceo-1-661x1024.png' => ['ar' => 'المدير التنفيذي', 'en' => 'CEO'],
    'Niche-Society-Arabic-CP2.png' => ['ar' => 'ملف الشركة', 'en' => 'Company Profile'],
    'TEAM-scaled.jpg' => ['ar' => 'فريق نيش سوسايتي', 'en' => 'Niche Society Team'],
    '1.svg' => ['ar' => 'أيقونة', 'en' => 'Icon'],
];

try {
    $imagesDir = ROOT_PATH . '/assets/images';
    
    if (!is_dir($imagesDir)) {
        die("❌ Images directory not found: $imagesDir\n");
    }
    
    echo "📸 Starting image import process...\n\n";
    
    // Get all image files
    $files = array_filter(scandir($imagesDir), function($file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
    });
    
    $added = 0;
    $updated = 0;
    $errors = 0;
    
    foreach ($files as $filename) {
        $filePath = $imagesDir . '/' . $filename;
        
        if (!file_exists($filePath)) {
            continue;
        }
        
        // Get file info
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);
        $fileType = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Get image dimensions if it's an image (not SVG)
        $width = null;
        $height = null;
        if ($fileType !== 'svg') {
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }
        
        // Determine folder/category
        $folder = 'general';
        foreach ($imageCategories as $cat => $files) {
            if (in_array($filename, $files)) {
                $folder = $cat;
                break;
            }
        }
        
        // Get alt text
        $altAr = $altTextMap[$filename]['ar'] ?? 'صورة';
        $altEn = $altTextMap[$filename]['en'] ?? 'Image';
        
        // Check if image already exists in database
        $existing = dbFetchOne(
            "SELECT id FROM media WHERE filename = ?",
            [$filename]
        );
        
        $filePathRelative = 'assets/images/' . $filename;
        
        if ($existing) {
            // Update existing record
            dbUpdate('media', [
                'original_name' => $filename,
                'file_path' => $filePathRelative,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'width' => $width,
                'height' => $height,
                'alt_text_ar' => $altAr,
                'alt_text_en' => $altEn,
                'folder' => $folder,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $existing['id']]);
            
            echo "  ✅ Updated: $filename ({$width}x{$height}, " . formatBytes($fileSize) . ")\n";
            $updated++;
        } else {
            // Insert new record
            dbInsert('media', [
                'filename' => $filename,
                'original_name' => $filename,
                'file_path' => $filePathRelative,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'width' => $width,
                'height' => $height,
                'alt_text_ar' => $altAr,
                'alt_text_en' => $altEn,
                'folder' => $folder,
                'status' => 'active'
            ]);
            
            echo "  ➕ Added: $filename ({$width}x{$height}, " . formatBytes($fileSize) . ")\n";
            $added++;
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "   ➕ Added: $added images\n";
    echo "   ✅ Updated: $updated images\n";
    echo "   ❌ Errors: $errors\n\n";
    
    // Now assign service images
    echo "🔗 Assigning images to services...\n";
    
    foreach ($serviceImageMap as $imageFile => $serviceSlug) {
        // Get service ID
        $service = dbFetchOne(
            "SELECT id FROM services WHERE slug = ?",
            [$serviceSlug]
        );
        
        if ($service) {
            // Update service with image
            dbUpdate('services', ['image' => $imageFile], ['id' => $service['id']]);
            
            echo "  ✅ Assigned '$imageFile' to service: $serviceSlug\n";
        } else {
            echo "  ⚠️  Service not found: $serviceSlug\n";
        }
    }
    
    echo "\n🎉 Image import complete!\n";
    echo "\n📝 Next steps:\n";
    echo "   1. Review images in the media table\n";
    echo "   2. Update any missing alt text or captions\n";
    echo "   3. Verify service images are correctly assigned\n";
    
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}


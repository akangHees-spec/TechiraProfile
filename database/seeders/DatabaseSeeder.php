<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\PageSection;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Admin
        User::updateOrCreate(
            ['email' => 'admin@techira.com'],
            [
                'name' => 'Admin Techira',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Settings (Key-Value Seed Key Wajib)
        $settings = [
            'company_name' => ['value' => 'Techira Nusantara', 'type' => 'text'],
            'logo' => ['value' => null, 'type' => 'image'],
            'favicon' => ['value' => null, 'type' => 'image'],
            'email' => ['value' => 'info@techira.com', 'type' => 'text'],
            'phone' => ['value' => '+62 21 5550199', 'type' => 'text'],
            'whatsapp_number' => ['value' => '+62 812-3456-7890', 'type' => 'text'],
            'whatsapp_message_template' => ['value' => 'Halo Techira Nusantara, saya tertarik dengan {name}. Info selengkapnya: {url}', 'type' => 'textarea'],
            'address' => ['value' => 'Gedung Cyber, Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan, DKI Jakarta 12710', 'type' => 'textarea'],
            'google_maps_embed' => ['value' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.267272213797!2d106.8213644!3d-6.2284534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3fb716503c7%3A0xc3458bfb972e259e!2sGedung%20Cyber!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'type' => 'textarea'],
            'social_facebook' => ['value' => 'https://facebook.com/techiranusantara', 'type' => 'text'],
            'social_instagram' => ['value' => 'https://instagram.com/techiranusantara', 'type' => 'text'],
            'social_linkedin' => ['value' => 'https://linkedin.com/company/techiranusantara', 'type' => 'text'],
            'youtube_url' => ['value' => 'https://youtube.com/c/techiranusantara', 'type' => 'text'],
        ];

        foreach ($settings as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'type' => $data['type'],
                ]
            );
        }

        // 3. Categories
        $categoriesData = [
            [
                'name' => 'Custom Software',
                'slug' => 'custom-software',
                'type' => 'service',
                'icon' => 'code',
                'description' => 'Layanan pengembangan software kustom berkualitas tinggi untuk kebutuhan bisnis Anda.',
                'whatsapp_number' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Infrastructure',
                'slug' => 'cloud-infrastructure',
                'type' => 'service',
                'icon' => 'cloud',
                'description' => 'Solusi infrastruktur cloud & devops yang andal, aman, dan scalable.',
                'whatsapp_number' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'SaaS Products',
                'slug' => 'saas-products',
                'type' => 'product',
                'icon' => 'package',
                'description' => 'Produk software-as-a-service siap pakai untuk meningkatkan efisiensi operasional bisnis.',
                'whatsapp_number' => '+6289999999999', // override category WA
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'IT Hardware & Network',
                'slug' => 'it-hardware-network',
                'type' => 'product',
                'icon' => 'server',
                'description' => 'Perangkat keras IT dan solusi jaringan terintegrasi untuk enterprise.',
                'whatsapp_number' => null,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $categories[$cData['slug']] = Category::updateOrCreate(
                ['slug' => $cData['slug']],
                $cData
            );
        }

        // 4. Products
        $productsData = [
            [
                'category_id' => $categories['saas-products']->id,
                'name' => 'Techira HRIS',
                'slug' => 'techira-hris',
                'short_description' => 'Sistem manajemen SDM dan payroll terintegrasi.',
                'description' => 'Techira HRIS mempermudah manajemen absensi, cuti, klaim, hingga perhitungan payroll bulanan karyawan secara otomatis. Cocok untuk startup dan corporate.',
                'price' => 1500000.00,
                'specifications' => ['Metode Deployment' => 'Cloud SaaS', 'Maksimal Pengguna' => 'Unlimited', 'SLA Uptime' => '99.9%'],
                'is_featured' => true,
                'whatsapp_click_count' => 12,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'category_id' => $categories['saas-products']->id,
                'name' => 'Techira POS',
                'slug' => 'techira-pos',
                'short_description' => 'Aplikasi kasir online pintar untuk retail & F&B.',
                'description' => 'Kelola transaksi, inventaris, dan laporan keuangan di berbagai cabang secara real-time dari satu dashboard dengan Techira Point of Sale.',
                'price' => 350000.00,
                'specifications' => ['Platform' => 'Android, Web, iPad', 'Offline Mode' => 'Tersedia', 'Integrasi Pembayaran' => 'QRIS, E-Wallet, Card'],
                'is_featured' => false,
                'whatsapp_click_count' => 5,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'category_id' => $categories['it-hardware-network']->id,
                'name' => 'Techira Smart Firewall Router',
                'slug' => 'techira-smart-firewall-router',
                'short_description' => 'Router enterprise dengan proteksi keamanan siber tingkat tinggi.',
                'description' => 'Keamanan jaringan kantor terjamin dari serangan luar dengan deteksi ancaman otomatis dan VPN bawaan untuk WFH yang aman.',
                'price' => 4500000.00,
                'specifications' => ['Throughput' => '1 Gbps', 'Port LAN' => '8 Port', 'VPN Tunnel' => 'Hingga 50 Koneksi'],
                'is_featured' => true,
                'whatsapp_click_count' => 24,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($productsData as $pData) {
            Product::updateOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );
        }

        // 5. Services
        $servicesData = [
            [
                'category_id' => $categories['custom-software']->id,
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'icon' => 'smartphone',
                'short_description' => 'Pembuatan aplikasi Android & iOS native/hybrid.',
                'description' => 'Kami membantu mewujudkan ide aplikasi mobile Anda menjadi kenyataan menggunakan teknologi Flutter atau React Native untuk performa terbaik dan hemat biaya.',
                'is_featured' => true,
                'whatsapp_click_count' => 45,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'category_id' => $categories['custom-software']->id,
                'name' => 'Web Application Development',
                'slug' => 'web-application-development',
                'icon' => 'globe',
                'short_description' => 'Sistem informasi berbasis web skala enterprise.',
                'description' => 'Pengembangan ERP, CRM, portal internal perusahaan, dan sistem manajemen database berkeamanan tinggi dengan arsitektur modern.',
                'is_featured' => true,
                'whatsapp_click_count' => 32,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'category_id' => $categories['cloud-infrastructure']->id,
                'name' => 'Cloud Migration & DevOps',
                'slug' => 'cloud-migration-devops',
                'icon' => 'cloud',
                'short_description' => 'Migrasi infrastruktur lokal ke AWS/GCP/Azure.',
                'description' => 'Layanan migrasi server tanpa downtime, setup CI/CD pipeline otomatis, dan optimasi biaya infrastruktur cloud perusahaan Anda.',
                'is_featured' => false,
                'whatsapp_click_count' => 18,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        $services = [];
        foreach ($servicesData as $sData) {
            $services[$sData['slug']] = Service::updateOrCreate(
                ['slug' => $sData['slug']],
                $sData
            );
        }

        // 6. Service Features
        $featuresData = [
            [
                'service_id' => $services['mobile-app-development']->id,
                'title' => 'UI/UX Design Modern & User-Friendly',
                'icon' => 'layout',
            ],
            [
                'service_id' => $services['mobile-app-development']->id,
                'title' => 'Integrasi API & Payment Gateway',
                'icon' => 'credit-card',
            ],
            [
                'service_id' => $services['mobile-app-development']->id,
                'title' => 'Publish ke Google Play Store & Apple App Store',
                'icon' => 'upload',
            ],
            [
                'service_id' => $services['web-application-development']->id,
                'title' => 'Keamanan Enkripsi SSL & Perlindungan CSRF',
                'icon' => 'shield',
            ],
            [
                'service_id' => $services['web-application-development']->id,
                'title' => 'Dashboard Analitik Real-Time',
                'icon' => 'bar-chart',
            ],
        ];

        foreach ($featuresData as $fData) {
            ServiceFeature::updateOrCreate(
                ['service_id' => $fData['service_id'], 'title' => $fData['title']],
                $fData
            );
        }

        // 7. Sliders
        $slidersData = [
            [
                'title' => 'Transformasi Digital Bisnis Anda',
                'subtitle' => 'Kami menghadirkan solusi software enterprise, cloud infrastruktur, dan IT support terbaik untuk mengakselerasi pertumbuhan bisnis Anda.',
                'button_text' => 'Konsultasi Sekarang',
                'button_link' => '#contact',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Tim Developer Expert & Berdedikasi',
                'subtitle' => 'Dipercaya oleh lebih dari 50+ perusahaan di Indonesia untuk menangani proyek IT berskala nasional.',
                'button_text' => 'Lihat Layanan',
                'button_link' => '#services',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($slidersData as $slData) {
            $slider = Slider::updateOrCreate(
                ['title' => $slData['title']],
                $slData
            );

            // Add seed image to Slider
            if ($slider->order === 1 && file_exists(public_path('seed-images/hero.png'))) {
                $slider->clearMediaCollection('image');
                $slider->addMedia(public_path('seed-images/hero.png'))
                    ->preservingOriginal()
                    ->toMediaCollection('image');
            }
        }

        // 8. Page Sections
        $sectionsData = [
            [
                'section_key' => 'about_us',
                'title' => 'Tentang Techira Nusantara',
                'subtitle' => 'Partner Teknologi Tepercaya untuk Masa Depan Bisnis Anda',
                'content' => "Didirikan pada tahun 2020, Techira Nusantara lahir dengan misi membantu bisnis dari skala menengah hingga enterprise melakukan transformasi digital yang sukses. Kami percaya bahwa teknologi bukan sekadar alat kerja, melainkan penggerak utama pertumbuhan bisnis di era modern.\n\nDengan tim software engineer, cloud architect, dan UI/UX designer yang berpengalaman, kami berkomitmen memberikan hasil kerja yang presisi, aman, dan siap menghadapi tantangan masa depan.",
                'order' => 1,
                'is_active' => true,
            ],
            [
                'section_key' => 'vision_mission',
                'title' => 'Visi & Misi Kami',
                'subtitle' => 'Fokus Kami Terhadap Inovasi dan Keandalan',
                'content' => "Visi kami adalah menjadi perusahaan penyedia solusi IT terdepan di Asia Tenggara yang dikenal karena inovasi, integritas, dan kualitas tanpa kompromi.\n\nMisi kami meliputi: memberikan layanan pengembangan software berkualitas dunia, membangun infrastruktur cloud yang andal dan aman, serta mendampingi klien di setiap langkah proses transformasi digital mereka demi kepuasan pelanggan yang maksimal.",
                'order' => 2,
                'is_active' => true,
            ],
            [
                'section_key' => 'why_us',
                'title' => 'Mengapa Memilih Techira?',
                'subtitle' => 'Kelebihan Yang Kami Tawarkan Untuk Kesuksesan Proyek Anda',
                'content' => "Kami tidak hanya menulis kode; kami memahami proses bisnis Anda. Techira unggul karena:\n\n1. Pendekatan berorientasi solusi yang disesuaikan kebutuhan klien.\n2. Transparansi penuh dalam proses development menggunakan framework agile.\n3. Keamanan data berstandar industri dengan enkripsi berlapis.\n4. Dukungan pasca-rilis (maintenance) yang responsif dan berdedikasi.",
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sectionsData as $secData) {
            $section = PageSection::updateOrCreate(
                ['section_key' => $secData['section_key']],
                $secData
            );

            // Add seed image to About Us page section
            if ($section->section_key === 'about_us' && file_exists(public_path('seed-images/about.png'))) {
                $section->clearMediaCollection('image');
                $section->addMedia(public_path('seed-images/about.png'))
                    ->preservingOriginal()
                    ->toMediaCollection('image');
            }
        }

        // 9. Testimonials
        $testimonialsData = [
            [
                'name' => 'Budi Santoso',
                'position' => 'CTO',
                'company' => 'Karya Mandiri Group',
                'message' => 'Bekerja sama dengan Techira adalah keputusan terbaik kami. Aplikasi mobile HRIS yang mereka kembangkan sangat stabil dan membantu memangkas waktu administratif kami hingga 40%. Sangat direkomendasikan!',
                'rating' => 5,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Siti Rahma',
                'position' => 'Founder & CEO',
                'company' => 'HijabStyle.id',
                'message' => 'Techira POS membantu kami memantau penjualan harian di 5 cabang retail kami secara real-time. Sistemnya intuitif, tim support mereka juga cepat merespons ketika kami membutuhkan penyesuaian sistem pembayaran.',
                'rating' => 5,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'David Wijaya',
                'position' => 'IT Director',
                'company' => 'Fintech Nusantara',
                'message' => 'Migrasi infrastruktur server kami ke AWS yang dipandu oleh tim DevOps Techira berjalan lancar tanpa downtime sama sekali. Keamanan data kami kini jauh lebih terjamin dan biaya cloud bulanan turun sekitar 25%.',
                'rating' => 4,
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($testimonialsData as $tData) {
            Testimonial::updateOrCreate(
                ['name' => $tData['name']],
                $tData
            );
        }

        // 10. Team Members
        $teamData = [
            [
                'name' => 'Rian Hidayat',
                'position' => 'CEO & Co-Founder',
                'bio' => 'Berpengalaman lebih dari 12 tahun di industri manajemen IT dan pengembangan bisnis teknologi.',
                'social_links' => ['linkedin' => 'https://linkedin.com', 'twitter' => 'https://twitter.com'],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Amanda Putri',
                'position' => 'Chief Technology Officer',
                'bio' => 'Mantan Senior Cloud Engineer di Silicon Valley dengan sertifikasi AWS Solutions Architect Professional.',
                'social_links' => ['linkedin' => 'https://linkedin.com', 'github' => 'https://github.com'],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Fahmi Irawan',
                'position' => 'Lead Software Architect',
                'bio' => 'Pakar arsitektur microservices dan keamanan siber dengan 8 tahun pengalaman memimpin proyek software enterprise.',
                'social_links' => ['linkedin' => 'https://linkedin.com', 'github' => 'https://github.com'],
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($teamData as $tmData) {
            $member = TeamMember::updateOrCreate(
                ['name' => $tmData['name']],
                $tmData
            );

            // Add seed images to Team Members
            if ($member->name === 'Rian Hidayat' && file_exists(public_path('seed-images/ceo.png'))) {
                $member->clearMediaCollection('photo');
                $member->addMedia(public_path('seed-images/ceo.png'))
                    ->preservingOriginal()
                    ->toMediaCollection('photo');
            } elseif ($member->name === 'Amanda Putri' && file_exists(public_path('seed-images/cto.png'))) {
                $member->clearMediaCollection('photo');
                $member->addMedia(public_path('seed-images/cto.png'))
                    ->preservingOriginal()
                    ->toMediaCollection('photo');
            } elseif ($member->name === 'Fahmi Irawan' && file_exists(public_path('seed-images/architect.png'))) {
                $member->clearMediaCollection('photo');
                $member->addMedia(public_path('seed-images/architect.png'))
                    ->preservingOriginal()
                    ->toMediaCollection('photo');
            }
        }

        // 11. Partners
        $partnersData = [
            [
                'name' => 'Amazon Web Services',
                'website_url' => 'https://aws.amazon.com',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Google Cloud Platform',
                'website_url' => 'https://cloud.google.com',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Microsoft Azure',
                'website_url' => 'https://azure.microsoft.com',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Alibaba Cloud',
                'website_url' => 'https://alibabacloud.com',
                'order' => 4,
                'is_active' => true,
            ],
        ];
        foreach ($partnersData as $prData) {
            $partner = Partner::updateOrCreate(
                ['name' => $prData['name']],
                $prData
            );

            if ($partner->name === 'Amazon Web Services' && file_exists(public_path('seed-images/aws.svg'))) {
                $partner->clearMediaCollection('logo');
                $partner->addMedia(public_path('seed-images/aws.svg'))
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            } elseif ($partner->name === 'Google Cloud Platform' && file_exists(public_path('seed-images/gcp.svg'))) {
                $partner->clearMediaCollection('logo');
                $partner->addMedia(public_path('seed-images/gcp.svg'))
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            } elseif ($partner->name === 'Microsoft Azure' && file_exists(public_path('seed-images/azure.svg'))) {
                $partner->clearMediaCollection('logo');
                $partner->addMedia(public_path('seed-images/azure.svg'))
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            } elseif ($partner->name === 'Alibaba Cloud' && file_exists(public_path('seed-images/alibaba.svg'))) {
                $partner->clearMediaCollection('logo');
                $partner->addMedia(public_path('seed-images/alibaba.svg'))
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            }
        }
        // 12. FAQs
        $faqsData = [
            [
                'question' => 'Berapa lama estimasi pembuatan sebuah software kustom?',
                'answer' => 'Waktu pengerjaan sangat bergantung pada kompleksitas modul dan skala proyek. Umumnya, aplikasi mobile/web skala kecil-menengah membutuhkan waktu 1-3 bulan, sedangkan sistem enterprise skala besar memerlukan 3-6 bulan atau lebih. Kami selalu memberikan timeline detail di awal kerja sama.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah Techira memberikan jaminan/maintenance pasca-rilis?',
                'answer' => 'Ya, tentu saja. Setiap proyek yang kami selesaikan mendapatkan garansi bug-free gratis selama 3 bulan pertama. Setelah itu, kami menawarkan paket maintenance bulanan yang mencakup pemantauan server, update keamanan, dan penambahan fitur minor.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah saya bisa berkonsultasi mengenai kebutuhan IT bisnis saya terlebih dahulu?',
                'answer' => 'Bisa. Kami menawarkan sesi konsultasi gratis di awal untuk membantu mengidentifikasi tantangan digital atau inefisiensi alur bisnis Anda, serta menyusun usulan solusi teknologi yang paling pas dan hemat biaya.',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($faqsData as $faqData) {
            Faq::updateOrCreate(
                ['question' => $faqData['question']],
                $faqData
            );
        }

        // 13. Contact Messages
        $messagesData = [
            [
                'name' => 'Johan Pratama',
                'email' => 'johan@retailindo.com',
                'phone' => '08112223334',
                'subject' => 'Tanya Aplikasi POS',
                'message' => 'Selamat siang, saya ingin menanyakan lebih detail terkait integrasi QRIS di aplikasi Techira POS. Apakah ada biaya tambahan dari bank partner?',
                'is_read' => false,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@manufacturecorp.com',
                'phone' => '08556667778',
                'subject' => 'Permintaan Penawaran ERP Custom',
                'message' => 'Halo, perusahaan kami membutuhkan sistem ERP kustom untuk mengelola inventory manufaktur bahan baku tekstil. Apakah kami bisa menjadwalkan meeting presentasi dari tim Techira minggu ini?',
                'is_read' => true,
            ],
        ];

        foreach ($messagesData as $msgData) {
            ContactMessage::updateOrCreate(
                ['email' => $msgData['email'], 'subject' => $msgData['subject']],
                $msgData
            );
        }
    }
}

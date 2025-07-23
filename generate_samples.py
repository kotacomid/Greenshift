#!/usr/bin/env python3
"""
Simple sample generator without external dependencies
Creates sample JSON configuration files
"""

import json
import os
from pathlib import Path

def create_sample_json():
    """Create sample JSON configuration files"""
    sample_configs = [
        {
            "theme_name": "HealthCare Modern",
            "description": "Modern healthcare and clinic website template - Solusi digital terdepan untuk pelayanan kesehatan di Indonesia",
            "industry": "healthcare",
            "business_type": "klinik",
            "colors": {
                "primary": "#3b82f6",
                "secondary": "#10b981",
                "accent": "#f59e0b"
            },
            "fonts": {
                "sans": ["Inter", "sans-serif"],
                "heading": ["Poppins", "sans-serif"]
            },
            "sections": {
                "hero": {
                    "title": "Revolusi Pelayanan Kesehatan Digital",
                    "subtitle": "Platform telemedicine dan manajemen pasien terdepan untuk transformasi digital klinik Indonesia",
                    "cta_primary": "Konsultasi Gratis",
                    "cta_secondary": "Lihat Demo Live"
                },
                "services": [
                    {
                        "title": "Telemedicine Platform",
                        "description": "Konsultasi online dengan dokter spesialis menggunakan video call HD berkualitas",
                        "icon": "video-camera",
                        "features": [
                            "Video call HD quality",
                            "Chat real-time dengan dokter",
                            "Resep digital terintegrasi",
                            "Riwayat konsultasi lengkap",
                            "Notifikasi appointment"
                        ]
                    },
                    {
                        "title": "Electronic Medical Records",
                        "description": "Sistem EMR terintegrasi dengan cloud storage dan multi-device access",
                        "icon": "document-text",
                        "features": [
                            "Cloud storage aman",
                            "Multi-device access",
                            "Automatic backup",
                            "Patient history tracking",
                            "Lab result integration"
                        ]
                    },
                    {
                        "title": "Appointment Management",
                        "description": "Sistem booking appointment online dengan konfirmasi otomatis",
                        "icon": "calendar",
                        "features": [
                            "Online booking 24/7",
                            "SMS/WhatsApp reminder",
                            "Queue management",
                            "Doctor schedule sync",
                            "Payment integration"
                        ]
                    },
                    {
                        "title": "Pharmacy Integration",
                        "description": "Integrasi dengan apotek untuk resep digital dan delivery obat",
                        "icon": "heart",
                        "features": [
                            "Digital prescription",
                            "Medicine delivery",
                            "Stock management",
                            "Price comparison",
                            "Insurance claim"
                        ]
                    },
                    {
                        "title": "Analytics Dashboard",
                        "description": "Dashboard analytics untuk monitoring performa klinik dan kepuasan pasien",
                        "icon": "chart-bar",
                        "features": [
                            "Patient analytics",
                            "Revenue tracking",
                            "Performance metrics",
                            "Satisfaction survey",
                            "Custom reports"
                        ]
                    },
                    {
                        "title": "Mobile Application",
                        "description": "Aplikasi mobile untuk pasien dan dokter dengan notifikasi push",
                        "icon": "device-mobile",
                        "features": [
                            "iOS & Android app",
                            "Push notifications",
                            "Offline mode",
                            "Biometric login",
                            "Multi-language support"
                        ]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Dr. Ahmad Fauzi",
                        "role": "Direktur Klinik Sehat Sentosa",
                        "content": "Platform ini benar-benar mengubah cara kami melayani pasien. Efisiensi operasional meningkat 300% dan kepuasan pasien mencapai 98%. ROI yang fantastis!",
                        "rating": 5,
                        "image": "doctor1.jpg",
                        "stats": {
                            "efficiency_increase": "300%",
                            "patient_satisfaction": "98%",
                            "roi": "450%"
                        }
                    },
                    {
                        "name": "Dr. Sari Indrawati",
                        "role": "Dokter Spesialis Anak - RS Bunda",
                        "content": "Fitur telemedicine sangat membantu pasien yang sulit datang ke klinik. Sistem EMR juga memudahkan tracking riwayat pasien secara real-time.",
                        "rating": 5,
                        "image": "doctor2.jpg",
                        "stats": {
                            "telemedicine_usage": "85%",
                            "time_saved": "2 hours/day",
                            "patient_reach": "+200%"
                        }
                    },
                    {
                        "name": "Budi Santoso",
                        "role": "Pasien Diabetes - Jakarta",
                        "content": "Monitoring diabetes jadi lebih mudah dengan aplikasi ini. Reminder minum obat dan konsultasi online sangat membantu kontrol gula darah saya.",
                        "rating": 5,
                        "image": "patient1.jpg",
                        "stats": {
                            "health_improvement": "40%",
                            "medication_adherence": "95%",
                            "consultation_frequency": "+150%"
                        }
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 1234 5678",
                "email": "info@kliniksehat.com",
                "address": "Jl. Kesehatan Raya No. 123, Menteng, Jakarta Pusat 10310",
                "whatsapp": "+62 812 3456 7890"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types"],
            "custom_post_types": ["testimonials", "services", "doctors", "appointments"],
            "animation_style": "smooth",
            "seo": {
                "meta_keywords": "klinik digital, telemedicine Indonesia, EMR system, healthcare technology",
                "meta_description": "Platform digital terdepan untuk transformasi klinik modern di Indonesia dengan telemedicine dan EMR terintegrasi"
            }
        },
        {
            "theme_name": "SanitasiPro Digital",
            "description": "Modern sanitation and waste management services - Solusi sanitasi modern dengan teknologi IoT untuk Indonesia",
            "industry": "sanitation",
            "business_type": "sedot_wc",
            "colors": {
                "primary": "#10b981",
                "secondary": "#3b82f6",
                "accent": "#f59e0b"
            },
            "fonts": {
                "sans": ["Roboto", "sans-serif"],
                "heading": ["Montserrat", "sans-serif"]
            },
            "sections": {
                "hero": {
                    "title": "Layanan Sedot WC Profesional 24/7",
                    "subtitle": "Booking online dengan tracking real-time, teknologi IoT, dan sistem pembayaran digital untuk solusi sanitasi modern",
                    "cta_primary": "Booking Sekarang",
                    "cta_secondary": "Cek Harga Area"
                },
                "services": [
                    {
                        "title": "Sedot WC Darurat 24/7",
                        "description": "Layanan darurat 24 jam dengan response time < 30 menit untuk area Jabodetabek",
                        "icon": "clock",
                        "features": [
                            "Response time < 30 menit",
                            "Tim profesional certified",
                            "Harga transparan no hidden cost",
                            "Peralatan modern & higienis",
                            "Garansi service 30 hari"
                        ]
                    },
                    {
                        "title": "Maintenance Rutin Terjadwal",
                        "description": "Perawatan berkala sistem septictank dengan reminder otomatis dan monitoring IoT",
                        "icon": "calendar",
                        "features": [
                            "Jadwal maintenance fleksibel",
                            "Reminder otomatis WhatsApp",
                            "IoT monitoring system",
                            "Diskon member hingga 25%",
                            "Report maintenance digital"
                        ]
                    },
                    {
                        "title": "Sistem Tracking Real-time",
                        "description": "Monitor perjalanan tim teknisi dan estimasi kedatangan dengan GPS tracking",
                        "icon": "location",
                        "features": [
                            "GPS tracking teknisi",
                            "Estimasi waktu kedatangan",
                            "Notifikasi status real-time",
                            "Rating & review system",
                            "Photo progress update"
                        ]
                    },
                    {
                        "title": "Pembersihan Industrial",
                        "description": "Layanan sanitasi untuk gedung bertingkat, pabrik, dan kompleks komersial",
                        "icon": "building",
                        "features": [
                            "Peralatan industrial grade",
                            "Certified waste disposal",
                            "Environmental compliance",
                            "Bulk pricing available",
                            "Contract maintenance"
                        ]
                    },
                    {
                        "title": "Eco-Friendly Treatment",
                        "description": "Treatment biologis ramah lingkungan dengan teknologi terdepan",
                        "icon": "leaf",
                        "features": [
                            "Bio-enzyme treatment",
                            "Zero chemical process",
                            "Waste water recycling",
                            "Carbon footprint tracking",
                            "Green certification"
                        ]
                    },
                    {
                        "title": "Smart Payment System",
                        "description": "Sistem pembayaran digital dengan berbagai metode dan cicilan 0%",
                        "icon": "credit-card",
                        "features": [
                            "Multiple payment methods",
                            "Cicilan 0% tersedia",
                            "Digital invoice & receipt",
                            "Loyalty point system",
                            "Corporate billing"
                        ]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Pak Wahyu Santoso",
                        "role": "Property Manager - Apartemen Mediterania",
                        "content": "Sejak pakai SanitasiPro, masalah septictank apartemen jadi terkontrol. Sistem monitoring IoT-nya membantu prediksi maintenance sebelum ada masalah besar.",
                        "rating": 5,
                        "image": "manager1.jpg",
                        "stats": {
                            "cost_reduction": "40%",
                            "emergency_calls": "-80%",
                            "tenant_satisfaction": "95%"
                        }
                    },
                    {
                        "name": "Ibu Ratna Sari",
                        "role": "Ibu Rumah Tangga - Depok",
                        "content": "Pelayanan sangat profesional dan cepat. Tim datang tepat waktu, kerja bersih, dan harga sesuai estimasi awal. Tracking GPS-nya juga membantu banget!",
                        "rating": 5,
                        "image": "customer1.jpg",
                        "stats": {
                            "service_speed": "25 menit",
                            "cleanliness_rating": "10/10",
                            "price_accuracy": "100%"
                        }
                    },
                    {
                        "name": "PT. Global Manufacturing",
                        "role": "Corporate Client - Bekasi",
                        "content": "Kontrak maintenance dengan SanitasiPro sangat cost-effective. Laporan digital dan compliance certificate membantu audit environmental kami.",
                        "rating": 5,
                        "image": "corporate1.jpg",
                        "stats": {
                            "annual_savings": "35%",
                            "compliance_score": "100%",
                            "downtime_reduction": "90%"
                        }
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 9876 5432",
                "email": "info@sanitasipro.com",
                "address": "Jl. Industri Raya No. 456, Cakung, Jakarta Timur 13910",
                "whatsapp": "+62 811 9876 5432"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types", "woocommerce"],
            "custom_post_types": ["services", "testimonials", "bookings", "areas"],
            "animation_style": "bouncy",
            "seo": {
                "meta_keywords": "sedot wc Jakarta, layanan sanitasi modern, IoT monitoring, emergency septic tank",
                "meta_description": "Layanan sedot WC profesional 24/7 dengan tracking real-time dan teknologi IoT untuk sanitasi modern Indonesia"
            }
        },
        {
            "theme_name": "TechRepair Master",
            "description": "Modern electronics and smartphone repair services - Service HP dan elektronik dengan AI diagnostics terdepan",
            "industry": "technology",
            "business_type": "service_hp",
            "colors": {
                "primary": "#f59e0b",
                "secondary": "#ef4444",
                "accent": "#8b5cf6"
            },
            "fonts": {
                "sans": ["Inter", "sans-serif"],
                "heading": ["Poppins", "sans-serif"]
            },
            "sections": {
                "hero": {
                    "title": "Service HP & Elektronik Terpercaya",
                    "subtitle": "AI diagnostics dengan garansi resmi, spare part original, dan teknisi bersertifikat internasional",
                    "cta_primary": "Diagnosis Gratis",
                    "cta_secondary": "Cek Garansi Device"
                },
                "services": [
                    {
                        "title": "AI-Powered Diagnostics",
                        "description": "Sistem diagnosis otomatis menggunakan AI untuk deteksi masalah dengan akurasi 99.8%",
                        "icon": "cpu",
                        "features": [
                            "AI diagnostics akurasi 99.8%",
                            "Instant problem detection",
                            "Predictive failure analysis",
                            "Cost estimation real-time",
                            "Repair complexity scoring"
                        ]
                    },
                    {
                        "title": "Smartphone Repair Center",
                        "description": "Perbaikan semua merk smartphone dengan spare part original dan garansi resmi",
                        "icon": "device-mobile",
                        "features": [
                            "All brand support",
                            "Original spare parts",
                            "Same-day repair service",
                            "Water damage recovery",
                            "Data recovery guarantee"
                        ]
                    },
                    {
                        "title": "Laptop & Computer Service",
                        "description": "Service laptop dan komputer dengan teknisi certified dan tools professional",
                        "icon": "desktop-computer",
                        "features": [
                            "Hardware & software repair",
                            "SSD/HDD upgrade service",
                            "Virus removal specialist",
                            "Performance optimization",
                            "Remote support available"
                        ]
                    },
                    {
                        "title": "Gaming Console Repair",
                        "description": "Spesialis repair PlayStation, Xbox, Nintendo dengan spare part original",
                        "icon": "puzzle",
                        "features": [
                            "PlayStation 5/4/3 repair",
                            "Xbox Series X/S/One repair",
                            "Nintendo Switch repair",
                            "Controller customization",
                            "Firmware update service"
                        ]
                    },
                    {
                        "title": "Express Repair Service",
                        "description": "Layanan repair ekspres 1-3 jam untuk masalah urgent dengan prioritas tinggi",
                        "icon": "lightning-bolt",
                        "features": [
                            "1-3 hours completion",
                            "Priority queue system",
                            "Express warranty included",
                            "While-you-wait service",
                            "Emergency hotline 24/7"
                        ]
                    },
                    {
                        "title": "Corporate IT Support",
                        "description": "Maintenance IT corporate dengan SLA guarantee dan on-site support",
                        "icon": "office-building",
                        "features": [
                            "SLA guarantee 99.9%",
                            "On-site support team",
                            "Preventive maintenance",
                            "IT asset management",
                            "24/7 helpdesk support"
                        ]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Dimas Pratama",
                        "role": "Content Creator - YouTube 500K Subscriber",
                        "content": "iPhone 13 Pro saya rusak karena jatuh, pikir udah gak bisa diapa-apain. Ternyata TechRepair bisa benerin dengan perfect! Kamera dan Face ID normal lagi.",
                        "rating": 5,
                        "image": "creator1.jpg",
                        "stats": {
                            "repair_success": "100%",
                            "turnaround_time": "4 hours",
                            "cost_vs_new": "70% cheaper"
                        }
                    },
                    {
                        "name": "CV. Digital Solutions",
                        "role": "Software House - 50 Employees",
                        "content": "Maintenance laptop team developer jadi lebih efficient dengan TechRepair. Response time cepat dan harga corporate yang kompetitif untuk budget IT kami.",
                        "rating": 5,
                        "image": "company1.jpg",
                        "stats": {
                            "downtime_reduction": "85%",
                            "cost_savings": "45%",
                            "employee_satisfaction": "96%"
                        }
                    },
                    {
                        "name": "Sarah Wijaya",
                        "role": "Mahasiswa UI - Fakultas Teknik",
                        "content": "Laptop untuk skripsi tiba-tiba mati total pas deadline. TechRepair berhasil recover semua data dan repair dalam 2 jam. Lifesaver banget!",
                        "rating": 5,
                        "image": "student1.jpg",
                        "stats": {
                            "data_recovery": "100%",
                            "repair_time": "2 hours",
                            "student_discount": "25%"
                        }
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 5555 9999",
                "email": "info@techrepairmaster.com",
                "address": "Jl. Teknologi Digital No. 789, Senayan, Jakarta Selatan 12190",
                "whatsapp": "+62 812 5555 9999"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types", "woocommerce"],
            "custom_post_types": ["repairs", "testimonials", "devices", "warranties"],
            "animation_style": "minimal",
            "seo": {
                "meta_keywords": "service hp Jakarta, repair iPhone, laptop repair, AI diagnostics, gaming console repair",
                "meta_description": "Service HP dan elektronik terpercaya dengan AI diagnostics, garansi resmi, dan teknisi bersertifikat internasional"
            }
        },
        {
            "theme_name": "EduTech Indonesia",
            "description": "Modern educational platform - Platform pembelajaran digital untuk transformasi pendidikan Indonesia",
            "industry": "education",
            "business_type": "sekolah",
            "colors": {
                "primary": "#8b5cf6",
                "secondary": "#3b82f6",
                "accent": "#10b981"
            },
            "fonts": {
                "sans": ["Inter", "sans-serif"],
                "heading": ["Poppins", "sans-serif"]
            },
            "sections": {
                "hero": {
                    "title": "Transformasi Digital Pendidikan Indonesia",
                    "subtitle": "Platform pembelajaran online dengan AI-powered personalization, virtual classroom, dan sistem manajemen sekolah terintegrasi",
                    "cta_primary": "Coba Gratis 30 Hari",
                    "cta_secondary": "Lihat Demo Platform"
                },
                "services": [
                    {
                        "title": "Virtual Classroom",
                        "description": "Kelas virtual interaktif dengan whiteboard digital, breakout rooms, dan recording otomatis",
                        "icon": "academic-cap",
                        "features": [
                            "HD video conferencing",
                            "Interactive whiteboard",
                            "Breakout rooms for groups",
                            "Auto class recording",
                            "Screen sharing & presentation"
                        ]
                    },
                    {
                        "title": "Learning Management System",
                        "description": "LMS lengkap dengan progress tracking, assessment tools, dan analytics dashboard",
                        "icon": "book-open",
                        "features": [
                            "Course content management",
                            "Progress tracking siswa",
                            "Online quiz & assignments",
                            "Grade book integration",
                            "Parent progress reports"
                        ]
                    },
                    {
                        "title": "AI-Powered Personalization",
                        "description": "Sistem AI yang menyesuaikan pembelajaran berdasarkan kemampuan dan gaya belajar siswa",
                        "icon": "brain",
                        "features": [
                            "Adaptive learning paths",
                            "Personalized recommendations",
                            "Learning style analysis",
                            "Weak point identification",
                            "Smart study scheduling"
                        ]
                    },
                    {
                        "title": "School Management System",
                        "description": "Sistem manajemen sekolah untuk administrasi, keuangan, dan komunikasi",
                        "icon": "clipboard-list",
                        "features": [
                            "Student information system",
                            "Financial management",
                            "Attendance tracking",
                            "Library management",
                            "Communication portal"
                        ]
                    },
                    {
                        "title": "Mobile Learning App",
                        "description": "Aplikasi mobile untuk akses pembelajaran di mana saja dan kapan saja",
                        "icon": "device-mobile",
                        "features": [
                            "Offline content access",
                            "Push notifications",
                            "Mobile quiz & assignments",
                            "Video streaming optimized",
                            "Parent monitoring app"
                        ]
                    },
                    {
                        "title": "Assessment & Analytics",
                        "description": "Tools assessment comprehensive dengan analytics untuk monitoring pembelajaran",
                        "icon": "chart-bar",
                        "features": [
                            "Auto-grading system",
                            "Learning analytics",
                            "Performance predictions",
                            "Custom report generator",
                            "Intervention recommendations"
                        ]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Dr. Siti Nurhaliza, M.Pd",
                        "role": "Kepala Sekolah - SMAN 1 Jakarta",
                        "content": "EduTech Indonesia membantu sekolah kami bertransformasi digital. Engagement siswa meningkat 85% dan efisiensi administrasi naik drastis.",
                        "rating": 5,
                        "image": "principal1.jpg",
                        "stats": {
                            "student_engagement": "+85%",
                            "admin_efficiency": "+200%",
                            "parent_satisfaction": "92%"
                        }
                    },
                    {
                        "name": "Budi Setiawan, S.Pd",
                        "role": "Guru Matematika - SMP Negeri 5 Bandung",
                        "content": "Fitur AI personalization sangat membantu. Siswa yang biasanya kesulitan dengan matematika jadi lebih paham dengan learning path yang disesuaikan.",
                        "rating": 5,
                        "image": "teacher1.jpg",
                        "stats": {
                            "learning_improvement": "+60%",
                            "teaching_efficiency": "+40%",
                            "student_satisfaction": "89%"
                        }
                    },
                    {
                        "name": "Rina Pratiwi",
                        "role": "Orang Tua Siswa - Kelas 10",
                        "content": "Sebagai orang tua, saya bisa monitoring progress anak real-time. Report yang detail membantu saya memahami di mana anak butuh bantuan lebih.",
                        "rating": 5,
                        "image": "parent1.jpg",
                        "stats": {
                            "parent_engagement": "+150%",
                            "child_improvement": "+45%",
                            "communication_satisfaction": "94%"
                        }
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 7777 8888",
                "email": "info@edutech-indonesia.com",
                "address": "Jl. Pendidikan Raya No. 123, Senopati, Jakarta Selatan 12110",
                "whatsapp": "+62 812 7777 8888"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types", "user-registration"],
            "custom_post_types": ["courses", "lessons", "students", "teachers", "assessments"],
            "animation_style": "smooth",
            "seo": {
                "meta_keywords": "platform pembelajaran online, LMS Indonesia, virtual classroom, AI education, sekolah digital",
                "meta_description": "Platform pembelajaran digital terdepan untuk transformasi pendidikan Indonesia dengan AI personalization dan virtual classroom"
            }
        },
        {
            "theme_name": "AutoCare Pro",
            "description": "Modern automotive service center - Bengkel mobil modern dengan booking online dan tracking service real-time",
            "industry": "automotive",
            "business_type": "bengkel",
            "colors": {
                "primary": "#ef4444",
                "secondary": "#f59e0b",
                "accent": "#10b981"
            },
            "fonts": {
                "sans": ["Roboto", "sans-serif"],
                "heading": ["Montserrat", "sans-serif"]
            },
            "sections": {
                "hero": {
                    "title": "Bengkel Mobil Modern & Terpercaya",
                    "subtitle": "Service kendaraan profesional dengan booking online, tracking real-time, dan garansi resmi untuk semua merk mobil",
                    "cta_primary": "Booking Service Online",
                    "cta_secondary": "Estimasi Biaya"
                },
                "services": [
                    {
                        "title": "Service Berkala & Tune-Up",
                        "description": "Perawatan rutin sesuai jadwal pabrikan dengan spare part original dan teknisi berpengalaman",
                        "icon": "wrench",
                        "features": [
                            "Service sesuai KM pabrikan",
                            "Spare part original/OEM",
                            "Teknisi certified",
                            "Garansi service 6 bulan",
                            "Digital service record"
                        ]
                    },
                    {
                        "title": "Engine Diagnostics",
                        "description": "Diagnosis mesin menggunakan scanner OBD terbaru untuk deteksi masalah akurat",
                        "icon": "cpu",
                        "features": [
                            "OBD scanner professional",
                            "ECU programming",
                            "Performance analysis",
                            "Emission test",
                            "Engine health report"
                        ]
                    },
                    {
                        "title": "Body Repair & Painting",
                        "description": "Perbaikan body dan cat mobil dengan teknologi oven baking dan color matching",
                        "icon": "paint-brush",
                        "features": [
                            "Oven baking technology",
                            "Computer color matching",
                            "Dent repair specialist",
                            "Insurance claim support",
                            "Paint protection film"
                        ]
                    },
                    {
                        "title": "Tire & Wheel Service",
                        "description": "Layanan ban dan velg lengkap dengan alignment, balancing, dan tire pressure monitoring",
                        "icon": "cog",
                        "features": [
                            "Wheel alignment 3D",
                            "Digital tire balancing",
                            "Tire pressure monitoring",
                            "Nitrogen filling",
                            "Tire rotation service"
                        ]
                    },
                    {
                        "title": "Emergency Roadside",
                        "description": "Layanan darurat 24/7 dengan tow truck dan teknisi mobile untuk bantuan di jalan",
                        "icon": "truck",
                        "features": [
                            "24/7 emergency hotline",
                            "GPS tracked tow truck",
                            "Mobile mechanic service",
                            "Battery jump start",
                            "Flat tire assistance"
                        ]
                    },
                    {
                        "title": "AC & Electrical Service",
                        "description": "Spesialis AC mobil dan sistem kelistrikan dengan peralatan diagnostic modern",
                        "icon": "lightning-bolt",
                        "features": [
                            "AC system diagnosis",
                            "Electrical troubleshooting",
                            "ECU repair service",
                            "Wiring harness repair",
                            "Battery health check"
                        ]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Pak Andi Wijaya",
                        "role": "Owner Toyota Fortuner 2020",
                        "content": "Service di AutoCare Pro sangat professional. Booking online mudah, progress service bisa ditrack real-time, dan hasilnya memuaskan. Recommended!",
                        "rating": 5,
                        "image": "customer1.jpg",
                        "stats": {
                            "service_satisfaction": "98%",
                            "turnaround_time": "2 hours faster",
                            "cost_transparency": "100%"
                        }
                    },
                    {
                        "name": "PT. Logistik Nusantara",
                        "role": "Fleet Manager - 50 Vehicles",
                        "content": "Maintenance fleet jadi lebih terorganisir dengan sistem AutoCare Pro. Scheduled maintenance reminder dan reporting yang detail sangat membantu operasional.",
                        "rating": 5,
                        "image": "fleet1.jpg",
                        "stats": {
                            "fleet_uptime": "+15%",
                            "maintenance_cost": "-25%",
                            "efficiency_gain": "+30%"
                        }
                    },
                    {
                        "name": "Ibu Sari Permata",
                        "role": "Owner Honda CR-V 2019",
                        "content": "Sebagai perempuan, saya merasa aman service di sini. Teknisi menjelaskan dengan detail, harga transparan, dan ada area waiting yang nyaman untuk customer.",
                        "rating": 5,
                        "image": "customer2.jpg",
                        "stats": {
                            "customer_comfort": "10/10",
                            "price_transparency": "100%",
                            "service_explanation": "Excellent"
                        }
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 4444 7777",
                "email": "info@autocare-pro.com",
                "address": "Jl. Otomotif Raya No. 456, Sunter, Jakarta Utara 14350",
                "whatsapp": "+62 812 4444 7777"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types", "woocommerce"],
            "custom_post_types": ["services", "bookings", "vehicles", "testimonials", "technicians"],
            "animation_style": "bouncy",
            "seo": {
                "meta_keywords": "bengkel mobil Jakarta, service mobil online, automotive repair, car maintenance, auto care",
                "meta_description": "Bengkel mobil modern dengan booking online, tracking service real-time, dan garansi resmi untuk semua merk kendaraan"
            }
        }
    ]
    
    # Create sample JSON files
    samples_dir = Path("sample_configs")
    samples_dir.mkdir(exist_ok=True)
    
    for i, config in enumerate(sample_configs):
        filename = samples_dir / f"theme_{i+1}_{config['business_type']}.json"
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(config, f, indent=2, ensure_ascii=False)
        
        print(f"📄 Created sample: {filename}")
    
    print(f"\n✅ Generated {len(sample_configs)} sample configuration files!")
    print(f"📁 Location: {samples_dir}")
    
    return sample_configs

if __name__ == "__main__":
    create_sample_json()
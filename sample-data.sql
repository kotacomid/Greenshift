-- GreenShift Business AI Generator - Sample Data
-- This file contains sample business profiles and data for testing the plugin
-- Execute this SQL after plugin activation to populate with realistic examples

-- Sample Business Profiles
INSERT INTO wp_gsba_business_profiles (
    user_id, business_name, business_type, description, logo_url, 
    primary_color, secondary_color, website_url, phone, email, 
    address, tagline, social_media, created_at
) VALUES 
(
    1, 
    'Tech Solutions Pro', 
    'technology', 
    'We provide cutting-edge technology solutions for businesses. Our team specializes in custom software development, cloud migration, and digital transformation consulting. With over 10 years of experience, we help companies leverage technology to increase efficiency, reduce costs, and drive growth.',
    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200&h=200&fit=crop&crop=center',
    '#667eea',
    '#764ba2',
    'https://techsolutionspro.com',
    '+1 (555) 123-4567',
    'contact@techsolutionspro.com',
    '123 Tech Street, Innovation District, San Francisco, CA 94105',
    'Transforming businesses through technology',
    '{"facebook":"https://facebook.com/techsolutionspro","linkedin":"https://linkedin.com/company/techsolutionspro","whatsapp":"+15551234567","instagram":""}',
    NOW()
),
(
    1,
    'Green Garden Landscaping',
    'landscaping',
    'Professional landscaping services for residential and commercial properties. We create beautiful, sustainable outdoor spaces that enhance your property value and provide year-round enjoyment. From design consultation to complete landscape installation and maintenance.',
    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=200&h=200&fit=crop&crop=center',
    '#22c55e',
    '#16a34a',
    'https://greengardenpro.com',
    '+1 (555) 987-6543',
    'info@greengardenpro.com',
    '456 Garden Avenue, Suburban Heights, Austin, TX 78701',
    'Creating beautiful outdoor spaces',
    '{"facebook":"https://facebook.com/greengardenpro","instagram":"https://instagram.com/greengardenpro","whatsapp":"+15559876543","linkedin":""}',
    NOW()
),
(
    1,
    'Downtown Coffee Roasters',
    'food_beverage',
    'Artisan coffee roasters and café serving premium, ethically-sourced coffee beans. We roast in small batches to ensure maximum freshness and flavor. Our café offers a cozy atmosphere perfect for meetings, studying, or simply enjoying exceptional coffee.',
    'https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=200&h=200&fit=crop&crop=center',
    '#8b4513',
    '#d2691e',
    'https://downtowncoffeeroasters.com',
    '+1 (555) 456-7890',
    'hello@downtowncoffeeroasters.com',
    '789 Main Street, Downtown District, Portland, OR 97205',
    'Freshly roasted, ethically sourced',
    '{"facebook":"https://facebook.com/downtowncoffeeroasters","instagram":"https://instagram.com/downtowncoffeeroasters","whatsapp":"","linkedin":""}',
    NOW()
),
(
    1,
    'Elite Fitness Studio',
    'fitness',
    'Premium fitness studio offering personalized training, group classes, and wellness coaching. Our certified trainers help you achieve your fitness goals through customized workout plans, nutritional guidance, and ongoing support in a motivating environment.',
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=200&h=200&fit=crop&crop=center',
    '#ef4444',
    '#dc2626',
    'https://elitefitnessstudio.com',
    '+1 (555) 321-9876',
    'info@elitefitnessstudio.com',
    '321 Fitness Boulevard, Health District, Miami, FL 33101',
    'Transform your body, transform your life',
    '{"facebook":"https://facebook.com/elitefitnessstudio","instagram":"https://instagram.com/elitefitnessstudio","whatsapp":"+15553219876","linkedin":""}',
    NOW()
),
(
    1,
    'Coastal Real Estate Group',
    'real_estate',
    'Leading real estate agency specializing in coastal properties. With deep local market knowledge and a commitment to exceptional service, we help clients buy, sell, and invest in prime waterfront and coastal properties. Your trusted partner in real estate.',
    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=200&h=200&fit=crop&crop=center',
    '#3b82f6',
    '#1d4ed8',
    'https://coastalrealestategroup.com',
    '+1 (555) 654-3210',
    'sales@coastalrealestategroup.com',
    '654 Ocean Drive, Coastal City, San Diego, CA 92101',
    'Your coastal property experts',
    '{"facebook":"https://facebook.com/coastalrealestategroup","linkedin":"https://linkedin.com/company/coastalrealestategroup","whatsapp":"","instagram":"https://instagram.com/coastalrealestategroup"}',
    NOW()
);

-- Sample Generated Pages (demonstrating successful AI generations)
INSERT INTO wp_gsba_generated_pages (
    user_id, business_profile_id, template_id, page_id, generated_content, 
    ai_model_used, seo_data, created_at
) VALUES 
(
    1, 1, 1, 1001,
    '{"hero_headline":"Transform Your Business with Cutting-Edge Technology","hero_subheading":"We help businesses leverage the latest technology to increase efficiency, reduce costs, and accelerate growth through custom solutions and expert consulting.","hero_cta_text":"Get Your Free Consultation","hero_image_url":"https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop","features_heading":"Why Choose Tech Solutions Pro","feature_1_title":"Custom Development","feature_1_description":"Tailored software solutions built specifically for your business needs and requirements.","feature_1_icon":"code","feature_2_title":"Cloud Migration","feature_2_description":"Seamlessly transition to the cloud with our proven migration strategies and support.","feature_2_icon":"cloud","feature_3_title":"24/7 Support","feature_3_description":"Round-the-clock technical support to ensure your systems run smoothly and efficiently.","feature_3_icon":"support","about_heading":"Leading Technology Innovation","about_description":"With over a decade of experience in technology consulting, we have helped hundreds of businesses transform their operations and achieve remarkable growth through strategic technology implementation.","about_image_url":"https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop","contact_heading":"Ready to Transform Your Business?","contact_info":"Get in touch today for a free consultation and discover how technology can drive your business forward."}',
    'openai',
    '{"meta_title":"Tech Solutions Pro - Transform Your Business with Technology","meta_description":"Leading technology consulting firm helping businesses leverage cutting-edge solutions for growth and efficiency. Custom development, cloud migration, and expert support.","keywords":"technology consulting, custom software development, cloud migration, Tech Solutions Pro","schema_markup":{"@type":"TechnologyCompany","name":"Tech Solutions Pro","description":"Technology consulting and custom software development","url":"https://techsolutionspro.com"}}',
    NOW()
),
(
    1, 2, 1, 1002,
    '{"hero_headline":"Create Your Dream Outdoor Paradise","hero_subheading":"Professional landscaping services that transform your property into a beautiful, sustainable outdoor space that you can enjoy year-round.","hero_cta_text":"Get Free Estimate","hero_image_url":"https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop","features_heading":"Complete Landscaping Solutions","feature_1_title":"Design & Planning","feature_1_description":"Custom landscape design tailored to your vision, property, and local climate conditions.","feature_1_icon":"design","feature_2_title":"Installation","feature_2_description":"Professional installation using quality materials and proven techniques for lasting results.","feature_2_icon":"build","feature_3_title":"Maintenance","feature_3_description":"Ongoing maintenance services to keep your landscape looking beautiful throughout the seasons.","feature_3_icon":"maintenance","about_heading":"Transforming Outdoor Spaces Since 2010","about_description":"Green Garden Landscaping has been creating stunning outdoor environments for over a decade. Our team of certified landscape professionals brings creativity, expertise, and attention to detail to every project.","about_image_url":"https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&h=400&fit=crop","contact_heading":"Ready to Transform Your Outdoor Space?","contact_info":"Contact us today for a free consultation and estimate. Let us help you create the outdoor paradise you have always dreamed of."}',
    'claude',
    '{"meta_title":"Green Garden Landscaping - Create Your Dream Outdoor Paradise","meta_description":"Professional landscaping services for residential and commercial properties. Design, installation, and maintenance for beautiful, sustainable outdoor spaces.","keywords":"landscaping services, garden design, landscape installation, Green Garden Landscaping","schema_markup":{"@type":"LandscapeCompany","name":"Green Garden Landscaping","description":"Professional landscaping and garden design services","url":"https://greengardenpro.com"}}',
    NOW()
);
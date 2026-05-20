<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Complete Email Marketing System',
                'description' => 'Master email marketing with this comprehensive system. Learn to build lists, craft compelling messages, automate campaigns, and analyze results. Perfect for Nigerian businesses looking to grow their online presence.',
                'short_description' => 'Master email marketing for your business',
                'price' => 15000,
                'sale_price' => null,
                'category' => 'Email Marketing',
                'image' => 'https://picsum.photos/800/600?random=1',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
            [
                'name' => 'WhatsApp Business Automation Kit',
                'description' => 'Automate your WhatsApp business communications with this powerful kit. Set up auto-replies, broadcast messages, chatbot responses, and more. Save time while providing excellent customer service.',
                'short_description' => 'Automate your WhatsApp business communications',
                'price' => 18000,
                'sale_price' => 12000,
                'category' => 'Automation',
                'image' => 'https://picsum.photos/800/600?random=2',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
            [
                'name' => 'Social Media Marketing Blueprint',
                'description' => 'Create a winning social media strategy with our comprehensive blueprint. Learn platform-specific tactics, content creation, engagement strategies, and paid advertising tips tailored for the Nigerian market.',
                'short_description' => 'Create a winning social media strategy',
                'price' => 12000,
                'sale_price' => null,
                'category' => 'Marketing',
                'image' => 'https://picsum.photos/800/600?random=3',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
            [
                'name' => 'SEO Mastery for Nigerian Businesses',
                'description' => 'Learn search engine optimization specifically designed for Nigerian businesses. Master local SEO, Google ranking factors, content optimization, and technical SEO to drive organic traffic to your website.',
                'short_description' => 'Master SEO for Nigerian businesses',
                'price' => 20000,
                'sale_price' => 15000,
                'category' => 'Marketing',
                'image' => 'https://picsum.photos/800/600?random=4',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
            [
                'name' => 'Landing Page Templates Pack',
                'description' => 'Get 50+ professionally designed landing page templates. These high-converting templates work for any business and can be easily customized. Save hours of design time and launch your campaigns faster.',
                'short_description' => '50+ high-converting landing page templates',
                'price' => 8000,
                'sale_price' => null,
                'category' => 'Templates',
                'image' => 'https://picsum.photos/800/600?random=5',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
            [
                'name' => 'E-Commerce Starter Guide',
                'description' => 'Start your online store with confidence. This comprehensive guide covers everything from choosing products, setting up your store, payment integration, shipping solutions, and marketing strategies specific to the Nigerian e-commerce landscape.',
                'short_description' => 'Start your online store the right way',
                'price' => 25000,
                'sale_price' => null,
                'category' => 'Guides',
                'image' => 'https://picsum.photos/800/600?random=6',
                'is_active' => true,
                'is_featured' => true,
                'stock' => 999,
                'track_stock' => false,
                'product_type' => 'digital',
            ],
        ];

        foreach ($products as $product) {
            $product['slug'] = Str::slug($product['name']);
            Product::create($product);
        }
    }
}
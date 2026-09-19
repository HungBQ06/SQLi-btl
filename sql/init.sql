-- Initialize database sqli_lab with utf8mb4 character set
CREATE DATABASE IF NOT EXISTS sqli_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sqli_lab;

SET NAMES utf8mb4;

-- Drop tables if they exist
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS tracking_logs;

-- 1. Table `users`: Stores administrator and user credentials
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample accounts
INSERT INTO users (username, password, email, role) VALUES
('admin', 'csattt', 'admin@sqli-lab.local', 'admin'),
('carlos', 'carlos_password_123', 'carlos@sqli-lab.local', 'user'),
('wiener', 'peter', 'wiener@sqli-lab.local', 'user');

-- 2. Table `products`: Stores catalog with image_url and rich descriptions
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(500),
    released INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample products with real Unsplash images and detailed English descriptions
INSERT INTO products (name, category, description, price, image_url, released) VALUES
(
    'Anti-Fog Swimming Goggles', 
    'Gifts', 
    'Professional competition swimming goggles featuring UV400 mirror-coated lenses and 3-layer anti-fog technology. Ergonomic ultra-soft medical silicone eye gaskets ensure a 100% leak-proof seal, complete with a dual quick-adjust head strap for maximum comfort during long training sessions.', 
    19.99, 
    'https://images.unsplash.com/photo-1530549387789-4c1017266635?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'Stainless Steel Vacuum Water Bottle (32oz)', 
    'Gifts', 
    'Premium 32oz (950ml) double-wall vacuum insulated water bottle crafted from food-grade 18/8 stainless steel. TempShield insulation keeps beverages cold for up to 24 hours or piping hot for 12 hours. Features a leak-proof flex cap with a heavy-duty carrying handle, perfect for outdoor hiking, gym workouts, and daily travel.', 
    15.50, 
    'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'Premium Lambskin Leather Jacket', 
    'Clothing', 
    'Classic handcrafted 100% genuine lambskin leather motorcycle jacket. Features a supple natural leather shell lined with breathable quilted satin. Built with heavy-duty YKK metal zippers, 4 exterior zippered pockets, and 2 concealed interior pockets for a timeless, rugged style.', 
    120.00, 
    'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'Waterproof Outdoor Tactical Boots', 
    'Footwear', 
    'High-cut outdoor tactical hiking boots constructed from full-grain waterproof leather and high-density Cordura nylon. Equipped with a Vibram deep-lug anti-slip rubber outsole for superior traction on wet surfaces, plus an ergonomic shock-absorbing EVA midsole for all-day ankle support.', 
    89.90, 
    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'Active Noise Cancelling Wireless Earbuds', 
    'Tech', 
    'Next-gen Bluetooth 5.3 wireless earbuds featuring Hybrid Active Noise Cancellation (ANC -35dB). Custom 10mm Titanium dynamic drivers deliver deep resonant bass and crystal-clear acoustic highs. Enjoy up to 30 hours of total playtime with the IPX5 sweatproof Qi wireless charging case.', 
    49.99, 
    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'PTIT Official Uniform Windbreaker Jacket', 
    'Clothing', 
    'Official Posts and Telecommunications Institute of Technology (PTIT) signature red-and-white windbreaker uniform jacket. Features 2-layer waterproof polyester fabric, embroidered red PTIT emblem on left chest, stand-up collar, elastic cuffs, and inner mesh lining for all-weather campus life.', 
    45.00, 
    '/assets/images/ptit_jacket.jpg', 
    1
),
(
    'AMOLED Smart Fitness Watch', 
    'Tech', 
    'Advanced fitness tracker and smartwatch featuring a 1.4-inch crystal AMOLED touch display. Equipped with real-time PPG heart rate monitoring, SpO2 blood oxygen tracking, dual-band GPS, and 10-day battery life with IP68 water resistance.', 
    149.99, 
    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'RGB Mechanical Gaming Keyboard', 
    'Tech', 
    'Custom 75% compact wireless mechanical keyboard with hot-swappable tactile switches. Built with sound-dampening acoustic foam, customizable per-key RGB backlighting, and dual Bluetooth 5.1 / 2.4GHz wireless connectivity.', 
    89.00, 
    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80', 
    1
),
(
    'Waterproof Anti-Theft Travel Backpack', 
    'Gifts', 
    'Ergonomic 30L laptop travel backpack made from high-density water-resistant Oxford fabric. Features an integrated TSA combination lock, hidden anti-theft back pocket, padded 15.6-inch laptop compartment, and external USB charging port.', 
    55.00, 
    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80', 
    1
);

-- 3. Table `tracking_logs`: Stores tracking IDs for Lab 2
CREATE TABLE tracking_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(100) NOT NULL,
    user_agent VARCHAR(255),
    last_visit DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tracking_logs (tracking_id, user_agent) VALUES
('v5X9qL2mP8kZ', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

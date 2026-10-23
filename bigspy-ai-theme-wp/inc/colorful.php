<?php
/**
 * Color: one-click site palettes (Customizer → Colors), more product card styles
 * and the background presets used by the Section block.
 *
 * @package BigspyTheme
 */

defined( 'ABSPATH' ) || exit;

/** key => [ label, description, primary, secondary, success, alert, heading, text, light, footer, footer bottom ]. */
function bst_color_palettes() {
	return array(
		'ocean'    => array( __( 'Ocean blue', 'bigspy-ai-theme-wp' ), __( 'Trust, services, laundry, clinics', 'bigspy-ai-theme-wp' ), '#0868c9', '#ffb703', '#16a34a', '#e11d48', '#0f2a44', '#3d5266', '#eef6fd', '#0b2540', '#081b2f' ),
		'sunset'   => array( __( 'Sunset', 'bigspy-ai-theme-wp' ), __( 'Food, cafés, travel', 'bigspy-ai-theme-wp' ), '#f97316', '#db2777', '#16a34a', '#dc2626', '#3b1d0f', '#5b4636', '#fff4ec', '#2a140b', '#1c0d07' ),
		'forest'   => array( __( 'Fresh green', 'bigspy-ai-theme-wp' ), __( 'Organic shops, farms, health', 'bigspy-ai-theme-wp' ), '#16a34a', '#f59e0b', '#15803d', '#dc2626', '#123524', '#3f5247', '#effaf2', '#0e2a1c', '#0a1f14' ),
		'berry'    => array( __( 'Berry pink', 'bigspy-ai-theme-wp' ), __( 'Beauty, spa, fashion', 'bigspy-ai-theme-wp' ), '#db2777', '#7c3aed', '#16a34a', '#e11d48', '#3a1029', '#5c4452', '#fdf0f6', '#2b0b1f', '#1e0715' ),
		'royal'    => array( __( 'Royal purple', 'bigspy-ai-theme-wp' ), __( 'Tech, education, agencies', 'bigspy-ai-theme-wp' ), '#6d28d9', '#06b6d4', '#16a34a', '#e11d48', '#1e1238', '#4a4560', '#f4f0ff', '#170d2e', '#0f0820' ),
		'tomato'   => array( __( 'Bold red', 'bigspy-ai-theme-wp' ), __( 'Sales, electronics, marts', 'bigspy-ai-theme-wp' ), '#dc2626', '#f59e0b', '#16a34a', '#b91c1c', '#2b0f0f', '#4f3d3d', '#fff2f2', '#1f0b0b', '#150707' ),
		'teal'     => array( __( 'Teal & coral', 'bigspy-ai-theme-wp' ), __( 'Modern, friendly brands', 'bigspy-ai-theme-wp' ), '#0d9488', '#fb7185', '#16a34a', '#e11d48', '#0f2e2c', '#3e5552', '#ecfbf9', '#0b2523', '#071a18' ),
		'gold'     => array( __( 'Black & gold', 'bigspy-ai-theme-wp' ), __( 'Luxury, jewelry, real estate', 'bigspy-ai-theme-wp' ), '#b8860b', '#111827', '#16a34a', '#b91c1c', '#111111', '#44403c', '#faf6ec', '#111111', '#000000' ),
		'sky'      => array( __( 'Sky & lemon', 'bigspy-ai-theme-wp' ), __( 'Kids, toys, playful shops', 'bigspy-ai-theme-wp' ), '#0ea5e9', '#facc15', '#22c55e', '#ef4444', '#0c2d48', '#3f5568', '#effaff', '#0b2a40', '#071d2d' ),
		'classic'  => array( __( 'BIGSPY classic', 'bigspy-ai-theme-wp' ), __( 'The original navy & orange', 'bigspy-ai-theme-wp' ), '#0a4d8c', '#f26b21', '#2e9d5b', '#d63638', '#111827', '#3a3f47', '#f5f7fa', '#111827', '#0b1220' ),
		'navy_gold' => array( __( 'Navy & gold', 'bigspy-ai-theme-wp' ), __( 'Law, finance, consulting', 'bigspy-ai-theme-wp' ), '#1e3a8a', '#d4a017', '#15803d', '#b91c1c', '#0f1b3d', '#3f4a63', '#f1f4fb', '#0c1733', '#081026' ),
		'steel' => array( __( 'Steel blue', 'bigspy-ai-theme-wp' ), __( 'Industry, logistics, B2B', 'bigspy-ai-theme-wp' ), '#2563eb', '#64748b', '#16a34a', '#dc2626', '#0f172a', '#475569', '#f1f5f9', '#0f172a', '#020617' ),
		'cyan_fresh' => array( __( 'Fresh cyan', 'bigspy-ai-theme-wp' ), __( 'Cleaning, water, pools', 'bigspy-ai-theme-wp' ), '#0891b2', '#22d3ee', '#16a34a', '#e11d48', '#083344', '#3b5560', '#ecfeff', '#06303d', '#04222b' ),
		'mint' => array( __( 'Mint', 'bigspy-ai-theme-wp' ), __( 'Pharmacy, dental, wellness', 'bigspy-ai-theme-wp' ), '#10b981', '#0ea5e9', '#059669', '#ef4444', '#063b2c', '#3f564e', '#ecfdf5', '#05301f', '#032116' ),
		'olive' => array( __( 'Olive & sand', 'bigspy-ai-theme-wp' ), __( 'Coffee, tea, handmade', 'bigspy-ai-theme-wp' ), '#65a30d', '#ca8a04', '#15803d', '#b91c1c', '#1f2a10', '#4a5139', '#f7f9ee', '#1a230d', '#111808' ),
		'earth' => array( __( 'Earth brown', 'bigspy-ai-theme-wp' ), __( 'Furniture, wood, bakery', 'bigspy-ai-theme-wp' ), '#92400e', '#d97706', '#15803d', '#b91c1c', '#2a1608', '#57483d', '#fbf5ef', '#231206', '#170b03' ),
		'terracotta' => array( __( 'Terracotta', 'bigspy-ai-theme-wp' ), __( 'Restaurants, ceramics, decor', 'bigspy-ai-theme-wp' ), '#c2410c', '#0f766e', '#15803d', '#b91c1c', '#2b1309', '#5b4a42', '#fdf3ee', '#241007', '#180a04' ),
		'peach' => array( __( 'Soft peach', 'bigspy-ai-theme-wp' ), __( 'Bakery, kids, gifts', 'bigspy-ai-theme-wp' ), '#fb923c', '#f472b6', '#22c55e', '#ef4444', '#3b1f12', '#5f4b40', '#fff7f0', '#2d170c', '#1e0f07' ),
		'rose_gold' => array( __( 'Rose gold', 'bigspy-ai-theme-wp' ), __( 'Wedding, jewelry, beauty', 'bigspy-ai-theme-wp' ), '#be7c6b', '#3f3a3a', '#15803d', '#b91c1c', '#2e2220', '#5a4a47', '#fbf4f2', '#2a1f1d', '#1b1312' ),
		'lavender' => array( __( 'Lavender', 'bigspy-ai-theme-wp' ), __( 'Spa, yoga, flowers', 'bigspy-ai-theme-wp' ), '#8b5cf6', '#f0abfc', '#16a34a', '#e11d48', '#25174a', '#4d4763', '#f6f3ff', '#1d123c', '#130b29' ),
		'magenta' => array( __( 'Magenta pop', 'bigspy-ai-theme-wp' ), __( 'Events, music, nightlife', 'bigspy-ai-theme-wp' ), '#c026d3', '#facc15', '#22c55e', '#ef4444', '#2a0b30', '#55415a', '#fdf2ff', '#220826', '#16051a' ),
		'indigo' => array( __( 'Indigo', 'bigspy-ai-theme-wp' ), __( 'SaaS, apps, startups', 'bigspy-ai-theme-wp' ), '#4f46e5', '#22d3ee', '#16a34a', '#e11d48', '#1e1b4b', '#4b4a66', '#eef2ff', '#17153d', '#0e0c29' ),
		'electric' => array( __( 'Electric blue & lime', 'bigspy-ai-theme-wp' ), __( 'Gaming, sports, energy', 'bigspy-ai-theme-wp' ), '#2563eb', '#84cc16', '#22c55e', '#ef4444', '#0b1530', '#3e4a64', '#eef4ff', '#0a1229', '#060b1c' ),
		'orange_tech' => array( __( 'Orange & charcoal', 'bigspy-ai-theme-wp' ), __( 'Tools, auto, hardware', 'bigspy-ai-theme-wp' ), '#ea580c', '#1f2937', '#16a34a', '#dc2626', '#111827', '#4b5563', '#fff5ed', '#111827', '#030712' ),
		'ruby' => array( __( 'Ruby wine', 'bigspy-ai-theme-wp' ), __( 'Wine, restaurants, premium', 'bigspy-ai-theme-wp' ), '#9f1239', '#d4a373', '#15803d', '#dc2626', '#2a0a14', '#56404a', '#fdf2f5', '#22070f', '#16040a' ),
		'emerald_lux' => array( __( 'Emerald luxury', 'bigspy-ai-theme-wp' ), __( 'Hotels, resorts, real estate', 'bigspy-ai-theme-wp' ), '#047857', '#c9a227', '#15803d', '#b91c1c', '#06281d', '#3e544b', '#effaf5', '#052219', '#03160f' ),
		'ocean_coral' => array( __( 'Ocean & coral', 'bigspy-ai-theme-wp' ), __( 'Travel, beach, tours', 'bigspy-ai-theme-wp' ), '#0369a1', '#f97362', '#16a34a', '#e11d48', '#082f49', '#3f5466', '#eff8ff', '#072a41', '#041c2c' ),
		'monochrome' => array( __( 'Monochrome', 'bigspy-ai-theme-wp' ), __( 'Minimal, studios, portfolios', 'bigspy-ai-theme-wp' ), '#111827', '#6b7280', '#16a34a', '#dc2626', '#0b0b0b', '#3f3f46', '#f4f4f5', '#0b0b0b', '#000000' ),
		'graphite_cyan' => array( __( 'Graphite & cyan', 'bigspy-ai-theme-wp' ), __( 'Tech shops, phones, IT', 'bigspy-ai-theme-wp' ), '#0e7490', '#f59e0b', '#16a34a', '#dc2626', '#18181b', '#52525b', '#f0fdff', '#18181b', '#09090b' ),
		'candy' => array( __( 'Candy', 'bigspy-ai-theme-wp' ), __( 'Sweets, toys, party', 'bigspy-ai-theme-wp' ), '#ec4899', '#06b6d4', '#22c55e', '#ef4444', '#3a0d24', '#5e4552', '#fff0f7', '#2c0a1c', '#1d0612' ),
		'sage' => array( __( 'Sage', 'bigspy-ai-theme-wp' ), __( 'Interior, eco, skincare', 'bigspy-ai-theme-wp' ), '#5f8a6b', '#c08457', '#15803d', '#b91c1c', '#1d2b22', '#4b564f', '#f3f7f4', '#18241c', '#0f1812' ),
		'sunflower' => array( __( 'Sunflower', 'bigspy-ai-theme-wp' ), __( 'Construction, delivery, energy', 'bigspy-ai-theme-wp' ), '#eab308', '#1e293b', '#16a34a', '#dc2626', '#1e293b', '#475569', '#fefce8', '#1e293b', '#0f172a' ),
		'medical' => array( __( 'Medical blue', 'bigspy-ai-theme-wp' ), __( 'Hospitals, labs, care', 'bigspy-ai-theme-wp' ), '#0284c7', '#14b8a6', '#16a34a', '#dc2626', '#0c2a43', '#3e5568', '#f0f9ff', '#0a2439', '#061827' ),
		'vietnam_red' => array( __( 'Lucky red & gold', 'bigspy-ai-theme-wp' ), __( 'Tết, gifts, traditional', 'bigspy-ai-theme-wp' ), '#c8102e', '#f2b705', '#15803d', '#991b1b', '#2b0a0f', '#57403f', '#fff5f0', '#220709', '#150405' ),
		'ruby_pastel' => array( __( 'Ruby pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Fashion, wine, gifts', 'bigspy-ai-theme-wp' ), '#d3697b', '#e6b889', '#16a34a', '#dc2626', '#511f27', '#5e4549', '#fcf3f4', '#2b1216', '#1d0c0f' ),
		'ruby_deep' => array( __( 'Ruby deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Fashion, wine, gifts', 'bigspy-ai-theme-wp' ), '#8a0f24', '#ee8c2b', '#16a34a', '#dc2626', '#250e12', '#5e4549', '#faebed', '#2b1216', '#1d0c0f' ),
		'crimson' => array( __( 'Crimson', 'bigspy-ai-theme-wp' ), __( 'Vivid · Sales, food, sport', 'bigspy-ai-theme-wp' ), '#bb1b1b', '#edb91d', '#16a34a', '#dc2626', '#341414', '#5e4545', '#fbefef', '#2b1212', '#1d0c0c' ),
		'crimson_pastel' => array( __( 'Crimson pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Sales, food, sport', 'bigspy-ai-theme-wp' ), '#d36969', '#e6cf89', '#16a34a', '#dc2626', '#511f1f', '#5e4545', '#fcf3f3', '#2b1212', '#1d0c0c' ),
		'crimson_deep' => array( __( 'Crimson deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Sales, food, sport', 'bigspy-ai-theme-wp' ), '#8a0f0f', '#eebd2b', '#16a34a', '#dc2626', '#250e0e', '#5e4545', '#faebeb', '#2b1212', '#1d0c0c' ),
		'coral' => array( __( 'Coral', 'bigspy-ai-theme-wp' ), __( 'Vivid · Beauty, cafés, kids', 'bigspy-ai-theme-wp' ), '#bb3b1b', '#1da0ed', '#16a34a', '#dc2626', '#341a14', '#5e4a45', '#fbf1ef', '#2b1712', '#1d100c' ),
		'coral_pastel' => array( __( 'Coral pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Beauty, cafés, kids', 'bigspy-ai-theme-wp' ), '#d37e69', '#89c4e6', '#16a34a', '#dc2626', '#51291f', '#5e4a45', '#fcf5f3', '#2b1712', '#1d100c' ),
		'coral_deep' => array( __( 'Coral deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Beauty, cafés, kids', 'bigspy-ai-theme-wp' ), '#8a280f', '#2ba6ee', '#16a34a', '#dc2626', '#25130e', '#5e4a45', '#faeeeb', '#2b1712', '#1d100c' ),
		'tangerine' => array( __( 'Tangerine', 'bigspy-ai-theme-wp' ), __( 'Vivid · Food delivery, fitness', 'bigspy-ai-theme-wp' ), '#bb5e1b', '#1d2eed', '#16a34a', '#dc2626', '#342114', '#5e5045', '#fbf4ef', '#2b1d12', '#1d130c' ),
		'tangerine_pastel' => array( __( 'Tangerine pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Food delivery, fitness', 'bigspy-ai-theme-wp' ), '#d39569', '#8991e6', '#16a34a', '#dc2626', '#51341f', '#5e5045', '#fcf7f3', '#2b1d12', '#1d130c' ),
		'tangerine_deep' => array( __( 'Tangerine deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Food delivery, fitness', 'bigspy-ai-theme-wp' ), '#8a420f', '#2b3bee', '#16a34a', '#dc2626', '#25180e', '#5e5045', '#faf1eb', '#2b1d12', '#1d130c' ),
		'amber' => array( __( 'Amber', 'bigspy-ai-theme-wp' ), __( 'Vivid · Bakery, honey, tours', 'bigspy-ai-theme-wp' ), '#bb811b', '#5b1ded', '#16a34a', '#dc2626', '#342814', '#5e5545', '#fbf6ef', '#2b2212', '#1d170c' ),
		'amber_pastel' => array( __( 'Amber pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Bakery, honey, tours', 'bigspy-ai-theme-wp' ), '#d3ac69', '#a589e6', '#16a34a', '#dc2626', '#513f1f', '#5e5545', '#fcf9f3', '#2b2212', '#1d170c' ),
		'amber_deep' => array( __( 'Amber deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Bakery, honey, tours', 'bigspy-ai-theme-wp' ), '#8a5d0f', '#652bee', '#16a34a', '#dc2626', '#251d0e', '#5e5545', '#faf4eb', '#2b2212', '#1d170c' ),
		'mustard' => array( __( 'Mustard', 'bigspy-ai-theme-wp' ), __( 'Vivid · Construction, logistics', 'bigspy-ai-theme-wp' ), '#bb9b1b', '#a01ded', '#16a34a', '#dc2626', '#342d14', '#5e5945', '#fbf8ef', '#2b2612', '#1d190c' ),
		'mustard_pastel' => array( __( 'Mustard pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Construction, logistics', 'bigspy-ai-theme-wp' ), '#d3be69', '#c489e6', '#16a34a', '#dc2626', '#51471f', '#5e5945', '#fcfaf3', '#2b2612', '#1d190c' ),
		'mustard_deep' => array( __( 'Mustard deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Construction, logistics', 'bigspy-ai-theme-wp' ), '#8a710f', '#a62bee', '#16a34a', '#dc2626', '#25200e', '#5e5945', '#faf7eb', '#2b2612', '#1d190c' ),
		'lime' => array( __( 'Lime', 'bigspy-ai-theme-wp' ), __( 'Vivid · Eco, juice, sport', 'bigspy-ai-theme-wp' ), '#86bb1b', '#ed1d1d', '#16a34a', '#dc2626', '#293414', '#565e45', '#f7fbef', '#232b12', '#171d0c' ),
		'lime_pastel' => array( __( 'Lime pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Eco, juice, sport', 'bigspy-ai-theme-wp' ), '#b0d369', '#e68989', '#16a34a', '#dc2626', '#41511f', '#565e45', '#f9fcf3', '#232b12', '#171d0c' ),
		'lime_deep' => array( __( 'Lime deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Eco, juice, sport', 'bigspy-ai-theme-wp' ), '#618a0f', '#ee2b2b', '#16a34a', '#dc2626', '#1d250e', '#565e45', '#f5faeb', '#232b12', '#171d0c' ),
		'leaf' => array( __( 'Leaf', 'bigspy-ai-theme-wp' ), __( 'Vivid · Garden, farm, organic', 'bigspy-ai-theme-wp' ), '#1bbb1b', '#1ded85', '#16a34a', '#dc2626', '#143414', '#455e45', '#effbef', '#122b12', '#0c1d0c' ),
		'leaf_pastel' => array( __( 'Leaf pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Garden, farm, organic', 'bigspy-ai-theme-wp' ), '#69d369', '#89e6b8', '#16a34a', '#dc2626', '#1f511f', '#455e45', '#f3fcf3', '#122b12', '#0c1d0c' ),
		'leaf_deep' => array( __( 'Leaf deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Garden, farm, organic', 'bigspy-ai-theme-wp' ), '#0f8a0f', '#2bee8c', '#16a34a', '#dc2626', '#0e250e', '#455e45', '#ebfaeb', '#122b12', '#0c1d0c' ),
		'jade' => array( __( 'Jade', 'bigspy-ai-theme-wp' ), __( 'Vivid · Wellness, tea, pharmacy', 'bigspy-ai-theme-wp' ), '#1bbb6b', '#1ded3f', '#16a34a', '#dc2626', '#143424', '#455e52', '#effbf5', '#122b1f', '#0c1d14' ),
		'jade_pastel' => array( __( 'Jade pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Wellness, tea, pharmacy', 'bigspy-ai-theme-wp' ), '#69d39e', '#89e699', '#16a34a', '#dc2626', '#1f5138', '#455e52', '#f3fcf7', '#122b1f', '#0c1d14' ),
		'jade_deep' => array( __( 'Jade deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Wellness, tea, pharmacy', 'bigspy-ai-theme-wp' ), '#0f8a4d', '#2bee4b', '#16a34a', '#dc2626', '#0e251a', '#455e52', '#ebfaf2', '#122b1f', '#0c1d14' ),
		'teal_pastel' => array( __( 'Teal pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Clinics, cleaning, water', 'bigspy-ai-theme-wp' ), '#69d3cb', '#89d7e6', '#16a34a', '#dc2626', '#1f514d', '#455e5c', '#f3fcfb', '#122b29', '#0c1d1b' ),
		'teal_deep' => array( __( 'Teal deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Clinics, cleaning, water', 'bigspy-ai-theme-wp' ), '#0f8a80', '#2bcdee', '#16a34a', '#dc2626', '#0e2523', '#455e5c', '#ebfaf9', '#122b29', '#0c1d1b' ),
		'aqua' => array( __( 'Aqua', 'bigspy-ai-theme-wp' ), __( 'Vivid · Pool, laundry, spa', 'bigspy-ai-theme-wp' ), '#1ba6bb', '#1d69ed', '#16a34a', '#dc2626', '#142f34', '#455b5e', '#eff9fb', '#12282b', '#0c1a1d' ),
		'aqua_pastel' => array( __( 'Aqua pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Pool, laundry, spa', 'bigspy-ai-theme-wp' ), '#69c5d3', '#89abe6', '#16a34a', '#dc2626', '#1f4b51', '#455b5e', '#f3fbfc', '#12282b', '#0c1a1d' ),
		'aqua_deep' => array( __( 'Aqua deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Pool, laundry, spa', 'bigspy-ai-theme-wp' ), '#0f798a', '#2b72ee', '#16a34a', '#dc2626', '#0e2225', '#455b5e', '#ebf8fa', '#12282b', '#0c1a1d' ),
		'azure' => array( __( 'Azure', 'bigspy-ai-theme-wp' ), __( 'Vivid · Travel, IT, airlines', 'bigspy-ai-theme-wp' ), '#1b86bb', '#1d2eed', '#16a34a', '#dc2626', '#142934', '#45565e', '#eff7fb', '#12232b', '#0c171d' ),
		'azure_pastel' => array( __( 'Azure pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Travel, IT, airlines', 'bigspy-ai-theme-wp' ), '#69b0d3', '#8991e6', '#16a34a', '#dc2626', '#1f4151', '#45565e', '#f3f9fc', '#12232b', '#0c171d' ),
		'azure_deep' => array( __( 'Azure deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Travel, IT, airlines', 'bigspy-ai-theme-wp' ), '#0f618a', '#2b3bee', '#16a34a', '#dc2626', '#0e1d25', '#45565e', '#ebf5fa', '#12232b', '#0c171d' ),
		'cobalt' => array( __( 'Cobalt', 'bigspy-ai-theme-wp' ), __( 'Vivid · Finance, law, B2B', 'bigspy-ai-theme-wp' ), '#1b5ebb', '#511ded', '#16a34a', '#dc2626', '#142134', '#45505e', '#eff4fb', '#121d2b', '#0c131d' ),
		'cobalt_pastel' => array( __( 'Cobalt pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Finance, law, B2B', 'bigspy-ai-theme-wp' ), '#6995d3', '#a089e6', '#16a34a', '#dc2626', '#1f3451', '#45505e', '#f3f7fc', '#121d2b', '#0c131d' ),
		'cobalt_deep' => array( __( 'Cobalt deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Finance, law, B2B', 'bigspy-ai-theme-wp' ), '#0f428a', '#5b2bee', '#16a34a', '#dc2626', '#0e1825', '#45505e', '#ebf1fa', '#121d2b', '#0c131d' ),
		'sapphire' => array( __( 'Sapphire', 'bigspy-ai-theme-wp' ), __( 'Vivid · Banking, insurance', 'bigspy-ai-theme-wp' ), '#1b3bbb', '#8f1ded', '#16a34a', '#dc2626', '#141a34', '#454a5e', '#eff1fb', '#12172b', '#0c101d' ),
		'sapphire_pastel' => array( __( 'Sapphire pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Banking, insurance', 'bigspy-ai-theme-wp' ), '#697ed3', '#bc89e6', '#16a34a', '#dc2626', '#1f2951', '#454a5e', '#f3f5fc', '#12172b', '#0c101d' ),
		'sapphire_deep' => array( __( 'Sapphire deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Banking, insurance', 'bigspy-ai-theme-wp' ), '#0f288a', '#962bee', '#16a34a', '#dc2626', '#0e1325', '#454a5e', '#ebeefa', '#12172b', '#0c101d' ),
		'indigo_pastel' => array( __( 'Indigo pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · SaaS, startups', 'bigspy-ai-theme-wp' ), '#7269d3', '#e6de89', '#16a34a', '#dc2626', '#231f51', '#47455e', '#f4f3fc', '#14122b', '#0e0c1d' ),
		'indigo_deep' => array( __( 'Indigo deep', 'bigspy-ai-theme-wp' ), __( 'Deep · SaaS, startups', 'bigspy-ai-theme-wp' ), '#190f8a', '#eede2b', '#16a34a', '#dc2626', '#100e25', '#47455e', '#ecebfa', '#14122b', '#0e0c1d' ),
		'violet' => array( __( 'Violet', 'bigspy-ai-theme-wp' ), __( 'Vivid · Education, agencies', 'bigspy-ai-theme-wp' ), '#561bbb', '#e6ed1d', '#16a34a', '#dc2626', '#1f1434', '#4e455e', '#f3effb', '#1b122b', '#120c1d' ),
		'violet_pastel' => array( __( 'Violet pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Education, agencies', 'bigspy-ai-theme-wp' ), '#9069d3', '#e3e689', '#16a34a', '#dc2626', '#311f51', '#4e455e', '#f6f3fc', '#1b122b', '#120c1d' ),
		'violet_deep' => array( __( 'Violet deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Education, agencies', 'bigspy-ai-theme-wp' ), '#3c0f8a', '#e7ee2b', '#16a34a', '#dc2626', '#160e25', '#4e455e', '#f0ebfa', '#1b122b', '#120c1d' ),
		'orchid' => array( __( 'Orchid', 'bigspy-ai-theme-wp' ), __( 'Vivid · Spa, florist, wedding', 'bigspy-ai-theme-wp' ), '#931bbb', '#ed1d85', '#16a34a', '#dc2626', '#2c1434', '#58455e', '#f8effb', '#25122b', '#180c1d' ),
		'orchid_pastel' => array( __( 'Orchid pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Spa, florist, wedding', 'bigspy-ai-theme-wp' ), '#b969d3', '#e689b8', '#16a34a', '#dc2626', '#451f51', '#58455e', '#faf3fc', '#25122b', '#180c1d' ),
		'orchid_deep' => array( __( 'Orchid deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Spa, florist, wedding', 'bigspy-ai-theme-wp' ), '#6b0f8a', '#ee2b8c', '#16a34a', '#dc2626', '#1f0e25', '#58455e', '#f6ebfa', '#25122b', '#180c1d' ),
		'fuchsia' => array( __( 'Fuchsia', 'bigspy-ai-theme-wp' ), __( 'Vivid · Events, cosmetics', 'bigspy-ai-theme-wp' ), '#bb1ba1', '#ed3f1d', '#16a34a', '#dc2626', '#34142e', '#5e455a', '#fbeff9', '#2b1227', '#1d0c1a' ),
		'fuchsia_pastel' => array( __( 'Fuchsia pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Events, cosmetics', 'bigspy-ai-theme-wp' ), '#d369c2', '#e69989', '#16a34a', '#dc2626', '#511f49', '#5e455a', '#fcf3fa', '#2b1227', '#1d0c1a' ),
		'fuchsia_deep' => array( __( 'Fuchsia deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Events, cosmetics', 'bigspy-ai-theme-wp' ), '#8a0f75', '#ee4b2b', '#16a34a', '#dc2626', '#250e21', '#5e455a', '#faebf7', '#2b1227', '#1d0c1a' ),
		'rose' => array( __( 'Rose', 'bigspy-ai-theme-wp' ), __( 'Vivid · Jewelry, bridal', 'bigspy-ai-theme-wp' ), '#bb1b5e', '#1deddb', '#16a34a', '#dc2626', '#341421', '#5e4550', '#fbeff4', '#2b121d', '#1d0c13' ),
		'rose_pastel' => array( __( 'Rose pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Jewelry, bridal', 'bigspy-ai-theme-wp' ), '#d36995', '#89e6de', '#16a34a', '#dc2626', '#511f34', '#5e4550', '#fcf3f7', '#2b121d', '#1d0c13' ),
		'rose_deep' => array( __( 'Rose deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Jewelry, bridal', 'bigspy-ai-theme-wp' ), '#8a0f42', '#2beede', '#16a34a', '#dc2626', '#250e18', '#5e4550', '#faebf1', '#2b121d', '#1d0c13' ),
		'mocha' => array( __( 'Mocha', 'bigspy-ai-theme-wp' ), __( 'Vivid · Coffee, furniture', 'bigspy-ai-theme-wp' ), '#8f6547', '#3c78cd', '#16a34a', '#dc2626', '#342114', '#5e5045', '#fbf4ef', '#2b1d12', '#1d130c' ),
		'mocha_pastel' => array( __( 'Mocha pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Coffee, furniture', 'bigspy-ai-theme-wp' ), '#b69a86', '#97b2d8', '#16a34a', '#dc2626', '#51341f', '#5e5045', '#fcf7f3', '#2b1d12', '#1d130c' ),
		'mocha_deep' => array( __( 'Mocha deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Coffee, furniture', 'bigspy-ai-theme-wp' ), '#684831', '#4881d1', '#16a34a', '#dc2626', '#25180e', '#5e5045', '#faf1eb', '#2b1d12', '#1d130c' ),
		'slate' => array( __( 'Slate', 'bigspy-ai-theme-wp' ), __( 'Vivid · Tech, minimal', 'bigspy-ai-theme-wp' ), '#47658f', '#483ccd', '#16a34a', '#dc2626', '#142134', '#45505e', '#eff4fb', '#121d2b', '#0c131d' ),
		'slate_pastel' => array( __( 'Slate pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Tech, minimal', 'bigspy-ai-theme-wp' ), '#869ab6', '#9d97d8', '#16a34a', '#dc2626', '#1f3451', '#45505e', '#f3f7fc', '#121d2b', '#0c131d' ),
		'slate_deep' => array( __( 'Slate deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Tech, minimal', 'bigspy-ai-theme-wp' ), '#314868', '#5348d1', '#16a34a', '#dc2626', '#0e1825', '#45505e', '#ebf1fa', '#121d2b', '#0c131d' ),
		'olive_pastel' => array( __( 'Olive pastel', 'bigspy-ai-theme-wp' ), __( 'Pastel · Handmade, interior', 'bigspy-ai-theme-wp' ), '#aeb686', '#b8d897', '#16a34a', '#dc2626', '#49511f', '#5a5e45', '#fafcf3', '#272b12', '#1a1d0c' ),
		'olive_deep' => array( __( 'Olive deep', 'bigspy-ai-theme-wp' ), __( 'Deep · Handmade, interior', 'bigspy-ai-theme-wp' ), '#5f6831', '#8cd148', '#16a34a', '#dc2626', '#21250e', '#5a5e45', '#f7faeb', '#272b12', '#1a1d0c' ),
		'night_neon' => array( __( 'Night neon', 'bigspy-ai-theme-wp' ), __( 'Dark · Gaming, tech, nightlife', 'bigspy-ai-theme-wp' ), '#22d3ee', '#a855f7', '#22c55e', '#ef4444', '#0b0f19', '#374151', '#eef2f7', '#0b0f19', '#05070d' ),
		'midnight_gold' => array( __( 'Midnight gold', 'bigspy-ai-theme-wp' ), __( 'Dark · Luxury, hotels', 'bigspy-ai-theme-wp' ), '#d4a017', '#f5f5f4', '#22c55e', '#ef4444', '#0b0f19', '#374151', '#eef2f7', '#0b0f19', '#05070d' ),
		'carbon_red' => array( __( 'Carbon red', 'bigspy-ai-theme-wp' ), __( 'Dark · Auto, sport, tools', 'bigspy-ai-theme-wp' ), '#ef4444', '#f59e0b', '#22c55e', '#ef4444', '#0b0f19', '#374151', '#eef2f7', '#0b0f19', '#05070d' ),
		'forest_night' => array( __( 'Forest night', 'bigspy-ai-theme-wp' ), __( 'Dark · Outdoor, camping', 'bigspy-ai-theme-wp' ), '#22c55e', '#facc15', '#22c55e', '#ef4444', '#0b0f19', '#374151', '#eef2f7', '#0b0f19', '#05070d' ),
	);
}

add_filter(
	'bst_defaults',
	function ( $defaults ) {
		return $defaults + array( 'color_use_palette' => true );
	}
);

add_filter(
	'bst_customizer_fields',
	function ( $fields ) {
		$presets = array();
		foreach ( bst_color_palettes() as $key => $p ) {
			$presets[ $key ] = array(
				'label'       => $p[0],
				'description' => $p[1],
				'swatches'    => array( $p[2], $p[3], $p[6], $p[8] ),
				'settings'    => array(
					'color_primary'    => $p[2],
					'color_secondary'  => $p[3],
					'color_success'    => $p[4],
					'color_alert'      => $p[5],
					'color_heading'    => $p[6],
					'color_text'       => $p[7],
					'color_link'       => $p[2],
					'color_link_hover' => $p[3],
					'color_light'      => $p[8],
					'header_top_bg'    => $p[2],
					'footer_cta_bg'    => $p[2],
					'footer_1_bg'      => $p[9],
					'footer_2_bg'      => $p[10],
					'footer_bottom_bg' => $p[10],
				),
			);
		}
		array_unshift(
			$fields,
			array(
				'id'          => 'color_use_palette',
				'section'     => 'bst_colors',
				'type'        => 'checkbox',
				'priority'    => 0,
				'label'       => __( 'Choose from the color palette library', 'bigspy-ai-theme-wp' ),
				'description' => __( 'On: pick a ready-made palette. Off: set every color by hand below.', 'bigspy-ai-theme-wp' ),
			),
			array(
				'id'          => 'color_palette_presets',
				'section'     => 'bst_colors',
				'type'        => 'layout_presets',
				'priority'    => 0,
				'label'       => __( 'Color palettes', 'bigspy-ai-theme-wp' ),
				'description' => __( 'One click sets the brand colors, links, top bar and footer together. Fine-tune any color below, then Publish.', 'bigspy-ai-theme-wp' ),
				'presets'     => $presets,
			)
		);

		// More product card styles for the shop, category pages and related products.
		foreach ( $fields as $i => $field ) {
			if ( isset( $field['id'] ) && 'product_card_style' === $field['id'] ) {
				$fields[ $i ]['choices'] = array(
					'bordered' => __( 'Bordered', 'bigspy-ai-theme-wp' ),
					'shadow'   => __( 'Shadow on hover', 'bigspy-ai-theme-wp' ),
					'plain'    => __( 'Plain', 'bigspy-ai-theme-wp' ),
					'boxed'    => __( 'Boxed card', 'bigspy-ai-theme-wp' ),
					'elevated' => __( 'Elevated (soft shadow, lifts on hover)', 'bigspy-ai-theme-wp' ),
					'colorful' => __( 'Colorful (tinted, gradient button)', 'bigspy-ai-theme-wp' ),
					'minimal'  => __( 'Minimal (image + name + price)', 'bigspy-ai-theme-wp' ),
				);
			}
		}
		return $fields;
	}
);

/** Section background presets: key => label. Colors follow the site palette. */
function bst_section_backgrounds() {
	return array(
		'soft-primary'     => __( 'Soft primary', 'bigspy-ai-theme-wp' ),
		'soft-secondary'   => __( 'Soft secondary', 'bigspy-ai-theme-wp' ),
		'primary'          => __( 'Primary (light text)', 'bigspy-ai-theme-wp' ),
		'gradient'         => __( 'Brand gradient (light text)', 'bigspy-ai-theme-wp' ),
		'gradient-soft'    => __( 'Soft gradient', 'bigspy-ai-theme-wp' ),
		'sunrise'          => __( 'Sunrise (light text)', 'bigspy-ai-theme-wp' ),
		'ocean'            => __( 'Ocean (light text)', 'bigspy-ai-theme-wp' ),
		'mint'             => __( 'Mint', 'bigspy-ai-theme-wp' ),
		'peach'            => __( 'Peach', 'bigspy-ai-theme-wp' ),
		'lavender'         => __( 'Lavender', 'bigspy-ai-theme-wp' ),
		'dark'             => __( 'Dark (light text)', 'bigspy-ai-theme-wp' ),
		'dots'             => __( 'Dotted pattern', 'bigspy-ai-theme-wp' ),
		'grid'             => __( 'Grid pattern', 'bigspy-ai-theme-wp' ),
		'mesh'             => __( 'Color mesh', 'bigspy-ai-theme-wp' ),
	);
}

/** Presets that need light text. */
function bst_section_dark_backgrounds() {
	return array( 'primary', 'gradient', 'sunrise', 'ocean', 'dark' );
}

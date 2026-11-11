# Hooks cho nhà phát triển

Sinh tự động bởi `python tools/list-hooks.py`; đừng sửa tay.
Không ai móc vào thì HTML không đổi.

Filter chung cho mọi khối `bst/*`: `bst_block_attributes( $attrs, $name, $block )` trước khi render,
`bst_block_classes( $classes, $name, $attrs, $block )` thêm class vào thẻ ngoài,
`bst_block_html( $html, $name, $attrs, $block )` sau khi render.

## Action

| Hook | Nơi gọi |
| --- | --- |
| `bst_after_content` | footer.php:10 |
| `bst_after_footer` | footer.php:20 |
| `bst_after_header` | header.php:30 |
| `bst_after_header_element` | inc/header-builder.php:208 |
| `bst_after_page_content` | page.php:27 |
| `bst_after_post_card` | inc/template-tags.php:320 |
| `bst_after_posts_loop` | index.php:22 |
| `bst_after_product_card` | woocommerce/content-product.php:114 |
| `bst_before_content` | header.php:35 |
| `bst_before_footer` | footer.php:15 |
| `bst_before_header` | header.php:23 |
| `bst_before_header_element` | inc/header-builder.php:208 |
| `bst_before_layout` | inc/template-tags.php:15 |
| `bst_before_page_content` | page.php:16 |
| `bst_before_post_card` | inc/template-tags.php:281 |
| `bst_before_posts_loop` | index.php:15 |
| `bst_before_product_card` | woocommerce/content-product.php:62 |
| `bst_features_after_form` | inc/features.php:241 |
| `bst_features_notices` | inc/features.php:224 |
| `bst_footer_start` | inc/footer.php:13 |
| `bst_listing_ad_changed` | inc/listing/meta-box.php:78 |
| `bst_product_card_media` | woocommerce/content-product.php:80 |

## Filter

| Hook | Nơi gọi |
| --- | --- |
| `bst_ad_modules` | inc/auto-detect.php:18 |
| `bst_ad_public_extra` | inc/auto-detect.php:164 |
| `bst_aid_api_base` | ai-designer/includes/client.php:11 |
| `bst_aid_extra_tool_groups` | ai-designer/includes/permissions.php:50 |
| `bst_aid_tools` | ai-designer/includes/tools.php:140 |
| `bst_block_attributes` | inc/hooks.php:43 |
| `bst_block_classes` | inc/hooks.php:65 |
| `bst_block_html` | inc/hooks.php:77 |
| `bst_block_presets` | inc/block-presets.php:19 |
| `bst_customize_focus_map` | inc/customizer/focus-map.php:92, inc/customizer/focus-map.php:177 |
| `bst_customizer_fields` | inc/customizer/fields.php:329 |
| `bst_customizer_sections` | inc/customizer/fields.php:53 |
| `bst_defaults` | inc/helpers.php:223 |
| `bst_dynamic_css` | inc/dynamic-css.php:98 |
| `bst_feature_fields` | inc/features.php:143, inc/features.php:217 |
| `bst_flatsome_translate` | inc/compat-flatsome-settings.php:347 |
| `bst_fonts` | inc/fonts.php:107 |
| `bst_header_element_<suffix>` | inc/header-builder.php:205 |
| `bst_header_element_html` | inc/header-builder.php:210 |
| `bst_header_element_schema` | inc/header-elements.php:209 |
| `bst_header_elements` | inc/helpers.php:273 |
| `bst_inherit_flatsome` | inc/compat-flatsome-settings.php:26 |
| `bst_layout_post_types` | inc/meta-boxes.php:43 |
| `bst_listing_url` | inc/listing/core.php:72 |
| `bst_live_search_args` | inc/header-extras.php:212, inc/header-extras.php:217, inc/header-extras.php:218, inc/header-extras.php:229 |
| `bst_live_search_cache_ttl` | inc/header-extras.php:186 |
| `bst_product_badges` | inc/woocommerce.php:227 |
| `bst_products_query_args` | inc/woocommerce-elements.php:191 |
| `bst_show_title` | inc/template-tags.php:84 |
| `bst_sidebar_position` | inc/helpers.php:477 |

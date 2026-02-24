<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo_path',
        'banner_path',
        'hero_title',
        'hero_subtitle',
        'products_section_title',
        'footer_info',
        'show_search_bar',
        'show_categories_section',
        'show_benefits_section',
        'benefit_1_icon', 'benefit_1_title', 'benefit_1_desc',
        'benefit_2_icon', 'benefit_2_title', 'benefit_2_desc',
        'benefit_3_icon', 'benefit_3_title', 'benefit_3_desc',
        'top_bar_active',
        'top_bar_text',
        'top_bar_link',
        'top_bar_bg_color',
        'top_bar_text_color',
        'primary_color',
        'secondary_color',
        'about_us_text',
        'contact_email',
        'contact_phone',
        'contact_address',
        'facebook_url',
        'instagram_url',
        'whatsapp_number',
        'is_active',
        // Featured Section
        'show_featured_section',
        'featured_title',
        'featured_description',
        'featured_btn_text',
        'featured_btn_link',
        // Testimonials
        'show_testimonials',
        'testimonials_title',
        // Social Extras
        'tiktok_url',
        'twitter_url',
        // Legal & Policies
        'shipping_policy_link',
        'returns_policy_link',
        'terms_conditions_link',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_search_bar' => 'boolean',
        'show_categories_section' => 'boolean',
        'show_benefits_section' => 'boolean',
        'top_bar_active' => 'boolean',
        'show_featured_section' => 'boolean',
        'show_testimonials' => 'boolean',
    ];
}
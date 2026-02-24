<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EcommerceConfiguration;
use App\Models\Tenant\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EcommerceSettingsController extends Controller
{
    public function edit()
    {
        $config = EcommerceConfiguration::firstOrCreate([], [
            'company_name' => 'Mi Tienda',
            'primary_color' => '#3b82f6',
            'secondary_color' => '#1e3a8a',
            'is_active' => true,
        ]);

        $testimonials = Testimonial::latest()->get();
        
        return view('tenant.ecommerce.settings.edit', compact('config', 'testimonials'));
    }

    public function update(Request $request)
    {
        $config = EcommerceConfiguration::first();

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'products_section_title' => 'nullable|string|max:255',
            'footer_info' => 'nullable|string',
            'show_search_bar' => 'sometimes|boolean',
            'show_categories_section' => 'sometimes|boolean',
            'show_benefits_section' => 'sometimes|boolean',
            'benefit_1_icon' => 'nullable|string|max:50',
            'benefit_1_title' => 'nullable|string|max:50',
            'benefit_1_desc' => 'nullable|string|max:100',
            'benefit_2_icon' => 'nullable|string|max:50',
            'benefit_2_title' => 'nullable|string|max:50',
            'benefit_2_desc' => 'nullable|string|max:100',
            'benefit_3_icon' => 'nullable|string|max:50',
            'benefit_3_title' => 'nullable|string|max:50',
            'benefit_3_desc' => 'nullable|string|max:100',
            'top_bar_active' => 'sometimes|boolean',
            'top_bar_text' => 'nullable|string|max:255',
            'top_bar_link' => 'nullable|string|max:255',
            'top_bar_bg_color' => 'nullable|string|max:7',
            'top_bar_text_color' => 'nullable|string|max:7',
            'about_us_text' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
            'is_active' => 'sometimes|boolean',
            'featured_title' => 'nullable|string|max:255',
            'featured_description' => 'nullable|string',
            'featured_btn_text' => 'nullable|string|max:50',
            'featured_btn_link' => 'nullable|string|max:255',
            'testimonials_title' => 'nullable|string|max:255',
            'show_featured_section' => 'sometimes|boolean',
            'show_testimonials' => 'sometimes|boolean',
            'shipping_policy_link' => 'nullable|string|max:255',
            'returns_policy_link' => 'nullable|string|max:255',
            'terms_conditions_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('logo')) {
            if ($config->logo_path) {
                Storage::disk('public')->delete($config->logo_path);
            }
            $path = $request->file('logo')->store('ecommerce/logos', 'public');
            $data['logo_path'] = $path;

            // Actualizar también el logo general del tenant (Navbar, Tickets, etc.)
            if (function_exists('tenant') && tenant()) {
                tenant()->update(['logo' => $path]);
            }
        }

        if ($request->hasFile('banner')) {
             if ($config->banner_path) {
                Storage::disk('public')->delete($config->banner_path);
            }
            $data['banner_path'] = $request->file('banner')->store('ecommerce/banners', 'public');
        }
        
        // Checkbox handling
        $data['is_active'] = $request->has('is_active');
        $data['show_search_bar'] = $request->has('show_search_bar');
        $data['show_categories_section'] = $request->has('show_categories_section');
        $data['show_benefits_section'] = $request->has('show_benefits_section');
        $data['show_featured_section'] = $request->has('show_featured_section');
        $data['show_testimonials'] = $request->has('show_testimonials');
        $data['top_bar_active'] = $request->has('top_bar_active');

        $config->update($data);

        return redirect()->route('tenant.ecommerce-settings.edit')->with('success', 'Configuración actualizada correctamente.');
    }
}
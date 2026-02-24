<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->paginate(10);
        return view('tenant.ecommerce.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('tenant.ecommerce.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('testimonials', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        Testimonial::create($data);

        return back()->with('success', 'Testimonio creado exitosamente.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('tenant.ecommerce.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($testimonial->image_path) {
                Storage::disk('public')->delete($testimonial->image_path);
            }
            $data['image_path'] = $request->file('image')->store('testimonials', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $testimonial->update($data);

        return back()->with('success', 'Testimonio actualizado exitosamente.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->image_path) {
            Storage::disk('public')->delete($testimonial->image_path);
        }
        $testimonial->delete();

        return back()->with('success', 'Testimonio eliminado exitosamente.');
    }
}

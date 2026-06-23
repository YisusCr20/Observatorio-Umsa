<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\PageSection;
use App\Models\PublicContentItem;
use App\Models\WelcomeSlide;
use App\Models\WelcomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WelcomeContentController extends Controller
{
    public function edit()
    {
        $slides = WelcomeSlide::orderBy('position')->get();
        $settings = WelcomeSetting::pluck('value', 'key');

        return view('admin.contenido.bienvenido.edit', compact('slides', 'settings'));
    }

    public function updateBackgrounds(Request $request)
    {
        $request->validate([
            'background_dark' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'background_light' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        foreach (['background_dark', 'background_light'] as $field) {
            if ($request->hasFile($field)) {
                $oldImage = WelcomeSetting::where('key', $field)->value('value');

                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }

                $path = $request->file($field)->store('welcome/backgrounds', 'public');

                WelcomeSetting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $path]
                );
            }
        }

        return back()->with('status', 'Fondos actualizados correctamente.');
    }

    public function storeSlide(Request $request)
    {
        $data = $request->validate([
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_normal' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_shape' => ['required', 'in:rounded,circle,tilted'],
            'position' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable'],
        ]);

        $data['image_path'] = $request->file('image')->store('welcome/slides', 'public');
        $data['is_active'] = $request->boolean('is_active');

        unset($data['image']);

        WelcomeSlide::create($data);

        return back()->with('status', 'Slide creado correctamente.');
    }

    public function updateSlide(Request $request, WelcomeSlide $slide)
    {
        $data = $request->validate([
            'title_highlight' => ['required', 'string', 'max:255'],
            'title_normal' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_shape' => ['required', 'in:rounded,circle,tilted'],
            'position' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable'],
        ]);

        if ($request->hasFile('image')) {
            if ($slide->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }

            $data['image_path'] = $request->file('image')->store('welcome/slides', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        unset($data['image']);

        $slide->update($data);

        return back()->with('status', 'Slide actualizado correctamente.');
    }

    public function destroySlide(WelcomeSlide $slide)
    {
        if ($slide->image_path) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->delete();

        return back()->with('status', 'Slide eliminado correctamente.');
    }

    public function updateAbout(Request $request)
    {
        $data = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.subtitle' => ['nullable', 'string', 'max:255'],
            'sections.*.body' => ['nullable', 'string'],
            'sections.*.position' => ['nullable', 'integer', 'min:1'],
            'sections.*.is_active' => ['nullable'],
            'sections.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        foreach ($data['sections'] as $key => $sectionData) {
            $section = PageSection::firstOrNew([
                'page' => 'acerca',
                'section_key' => $key,
            ]);

            if ($request->hasFile("sections.$key.image")) {
                if ($section->image_path) {
                    Storage::disk('public')->delete($section->image_path);
                }

                $sectionData['image_path'] = $request->file("sections.$key.image")
                    ->store('public-content/acerca', 'public');
            }

            $section->fill([
                'title' => $sectionData['title'] ?? null,
                'subtitle' => $sectionData['subtitle'] ?? null,
                'body' => $sectionData['body'] ?? null,
                'position' => $sectionData['position'] ?? 1,
                'is_active' => $request->boolean("sections.$key.is_active"),
            ]);

            if (isset($sectionData['image_path'])) {
                $section->image_path = $sectionData['image_path'];
            }

            $section->save();
        }

        return back()->with('status', 'Página Acerca de actualizada correctamente.');
    }

    public function storeGalleryImage(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'position' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable'],
        ]);

        $data['image_path'] = $request->file('image')->store('public-content/galeria', 'public');
        $data['is_active'] = $request->boolean('is_active');

        unset($data['image']);

        GalleryImage::create($data);

        return back()->with('status', 'Imagen agregada a la galería.');
    }

    public function destroyGalleryImage(GalleryImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('status', 'Imagen eliminada de la galería.');
    }

    public function storePublicItem(Request $request, string $page)
    {
        $data = $this->validatePublicItem($request);
        $data['page'] = $page;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')
                ->store("public-content/$page", 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        unset($data['image']);

        PublicContentItem::create($data);

        return back()->with('status', 'Contenido agregado correctamente.');
    }

    public function updatePublicItem(Request $request, PublicContentItem $item)
    {
        $data = $this->validatePublicItem($request, false);

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }

            $data['image_path'] = $request->file('image')
                ->store("public-content/$item->page", 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        unset($data['image']);

        $item->update($data);

        return back()->with('status', 'Contenido actualizado correctamente.');
    }

    public function destroyPublicItem(PublicContentItem $item)
    {
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return back()->with('status', 'Contenido eliminado correctamente.');
    }

    private function validatePublicItem(Request $request, bool $imageRequired = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'body' => ['nullable', 'string'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'button_label' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable'],
        ]);
    }
}

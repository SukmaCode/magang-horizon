<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
        ]);

        foreach ($request->settings as $index => $settingData) {
            $setting = Setting::find($settingData['id']);
            if ($setting) {
                if ($request->hasFile("settings.{$index}.value")) {
                    $file = $request->file("settings.{$index}.value");
                    $path = $file->store('settings', 'public');
                    $setting->value = '/storage/' . $path;
                } elseif (array_key_exists('value', $settingData)) {
                    if (!is_array($settingData['value']) && !is_object($settingData['value'])) {
                        $setting->value = $settingData['value'];
                    }
                }
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}

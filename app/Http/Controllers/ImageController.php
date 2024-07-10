<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::all();
        return view('images.index', compact('images'));
    }

    public function store(Request $request)
    {
        // dd('$request');
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('image');
        $path = $image->store('images', 'public');

        $newImage = Image::create(['file_path' => $path]);

        return response()->json(['id' => $newImage->id, 'file_path' => $path]);
    }

    public function show($id)
    {
        $image = Image::findOrFail($id);
        return response()->json($image);
    }

    public function update(Request $request, Image $image)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $newImage = $request->file('image');
        $path = $newImage->store('images', 'public');

        Storage::disk('public')->delete($image->file_path);

        $image->update(['file_path' => $path]);

        return response()->json(['file_path' => $path]);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return response()->json(['success' => 'Image deleted successfully.']);
    }
}


<?php

namespace App\Http\Controllers\v1;

use App\Models\Url;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShortenerPostRequest;

class ShortenerController extends Controller
{
    public function create(ShortenerPostRequest $request)
    {
        try {
            $originalUrl = $request->input('url');
            $shortUrl = Str::random(6);
            Url::create([
                'original_url' => $originalUrl,
                'short_url' => $shortUrl
            ]);
            return response()->json(['short_url' => $_SERVER['APP_URL'] . ':' . $_SERVER['SERVER_PORT'] . '/api/' . $shortUrl]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function all()
    {
        $data = Url::all()->isEmpty() ? ['no content'] : Url::all();
        return response()->json(['data' => $data]);
    }

    public function find(Request $request)
    {
        $shortUrl = $request->input('short_url');
        $url = Url::where('short_url', $shortUrl)->firstOrFail();

        return response()->json(['original_url' => url($url->original_url)]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'short_url' => 'required|string',
            'new_url' => 'required|url'
        ]);

        $url = Url::where('short_url', $validatedData['short_url'])->first();

        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        $url->original_url = $validatedData['new_url'];
        $url->save();

        return response()->json([
            'message' => 'URL updated successfully',
            'updated_url' => $url->original_url
        ]);
    }

    public function delete(Request $request)
    {
        $shortUrl = $request->input('short_url');
        $url = Url::where('short_url', $shortUrl)->first();

        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        $url->delete();
        return response()->json(['message' => 'URL deleted successfully'], 204);
    }

    public function redirect($shortUrl)
    {
        $fullUrl = $_SERVER['APP_URL'] . ':' . $_SERVER['SERVER_PORT'] . '/api/' . $shortUrl;
        $url = Url::where('short_url', $shortUrl)->first();

        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return redirect()->away($url->original_url);
    }

    public function stats(Request $request)
    {
        $validatedData = $request->validate([
            'short_url' => 'required|string',
        ]);

        $url = Url::where('short_url', $validatedData['short_url'])->first();
        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return response()->json([
            'id' => $url->id,
            'url' => $url->original_url,
            'shortCode' => $url->short_url,
            'createdAt' => $url->created_at->toIso8601String(),
            'updatedAt' => $url->updated_at->toIso8601String(),
            'accessCount' => $url->access_count ?? 0 // Usa 0 caso não exista um valor em access_count
        ]);
    }
}

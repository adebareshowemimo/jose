<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Settings;
use Illuminate\Http\Request;

class SocialSettingsController extends Controller
{
    public function index(Settings $settings)
    {
        return view('admin.social.index', [
            'social' => $settings->group('social'),
        ]);
    }

    public function update(Request $request, Settings $settings)
    {
        $data = $request->validate([
            // Share button toggles
            'share_twitter_enabled'  => 'sometimes|boolean',
            'share_linkedin_enabled' => 'sometimes|boolean',
            'share_facebook_enabled' => 'sometimes|boolean',
            'share_copy_enabled'     => 'sometimes|boolean',
            'share_twitter_handle'   => 'nullable|string|max:50',

            // Company profile URLs
            'profile_twitter_url'   => 'nullable|url|max:512',
            'profile_linkedin_url'  => 'nullable|url|max:512',
            'profile_facebook_url'  => 'nullable|url|max:512',
            'profile_instagram_url' => 'nullable|url|max:512',
            'profile_youtube_url'   => 'nullable|url|max:512',
        ]);

        $map = [
            'social.share.twitter_enabled'  => $request->boolean('share_twitter_enabled'),
            'social.share.linkedin_enabled' => $request->boolean('share_linkedin_enabled'),
            'social.share.facebook_enabled' => $request->boolean('share_facebook_enabled'),
            'social.share.copy_enabled'     => $request->boolean('share_copy_enabled'),
            'social.share.twitter_handle'   => $this->normalizeHandle($data['share_twitter_handle'] ?? null),

            'social.profile.twitter_url'   => $data['profile_twitter_url']   ?? null,
            'social.profile.linkedin_url'  => $data['profile_linkedin_url']  ?? null,
            'social.profile.facebook_url'  => $data['profile_facebook_url']  ?? null,
            'social.profile.instagram_url' => $data['profile_instagram_url'] ?? null,
            'social.profile.youtube_url'   => $data['profile_youtube_url']   ?? null,
        ];

        foreach ($map as $key => $value) {
            $row = Setting::firstOrNew(['key' => $key]);
            $row->group = 'social';
            $row->is_encrypted = false;
            $row->value = is_bool($value) ? ($value ? '1' : '0') : $value;
            $row->save();
        }

        $settings->flush();

        return back()->with('success', 'Social media settings updated.');
    }

    private function normalizeHandle(?string $handle): ?string
    {
        if (! $handle) return null;
        return ltrim(trim($handle), '@');
    }
}

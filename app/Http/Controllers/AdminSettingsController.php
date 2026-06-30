<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use App\Services\MailConnectionTester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = \App\Models\Setting::orderBy('group')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Get settings for a specific group.
     */
    public function show($group)
    {
        $settings = \App\Models\Setting::where('group', $group)->get();
        return response()->json($settings);
    }

    /**
     * Update settings for a group.
     */
    public function update(Request $request, $group)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            $this->settingsService->set($key, $value, \Illuminate\Support\Facades\Auth::id());
        }

        return response()->json(['message' => 'Settings saved successfully.']);
    }

    /**
     * Upload logo or favicon.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'type' => 'required|in:logo,favicon',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $request->type . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('branding', $filename, 'public');

            // Delete old file if exists
            $oldPathKey = $request->type === 'logo' ? 'app_logo' : 'app_favicon';
            $oldPath = $this->settingsService->get($oldPathKey);
            if ($oldPath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldPath))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldPath));
            }

            $publicUrl = '/storage/' . $path;
            $this->settingsService->set($oldPathKey, $publicUrl, \Illuminate\Support\Facades\Auth::id());

            return response()->json([
                'message' => ucfirst($request->type) . ' uploaded successfully.',
                'url' => $publicUrl
            ]);
        }

        return response()->json(['message' => 'Upload failed.'], 400);
    }

    /**
     * Test SMTP Connection.
     */
    public function testSmtp(Request $request, MailConnectionTester $tester)
    {
        $result = $tester->testSmtp(
            $request->input('host', ''),
            (int) $request->input('port', 587),
            $request->input('encryption', 'tls'),
            $request->input('username', ''),
            $request->input('password', '')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Test IMAP Connection.
     */
    public function testImap(Request $request, MailConnectionTester $tester)
    {
        $result = $tester->testImap(
            $request->input('host', ''),
            (int) $request->input('port', 993),
            $request->input('encryption', 'ssl'),
            $request->input('username', ''),
            $request->input('password', '')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Test POP3 Connection.
     */
    public function testPop(Request $request, MailConnectionTester $tester)
    {
        $result = $tester->testPop(
            $request->input('host', ''),
            (int) $request->input('port', 995),
            $request->input('encryption', 'ssl'),
            $request->input('username', ''),
            $request->input('password', '')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}

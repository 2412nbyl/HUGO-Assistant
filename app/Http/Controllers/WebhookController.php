<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WebhookController
 *
 * Receives GitHub webhook POST requests and runs `git pull` to auto-update
 * the local server whenever new code is pushed to the GitHub repository.
 *
 * SETUP STEPS (in GitHub):
 *   1. Go to: https://github.com/NN242224/HUGO-Assistant/settings/hooks
 *   2. Click "Add webhook"
 *   3. Payload URL: http://YOUR_SERVER/webhook/github
 *   4. Content type: application/json
 *   5. Secret: (set a random string here, same as GITHUB_WEBHOOK_SECRET in .env)
 *   6. Events: Just the push event
 */
class WebhookController extends Controller
{
    public function github(Request $request)
    {
        // ── 1. Verify signature (security) ────────────────────────────────────
        $secret = config('services.github.webhook_secret');

        if ($secret) {
            $signature = $request->header('X-Hub-Signature-256');
            if (!$signature) {
                Log::warning('GitHub webhook: missing signature header');
                return response()->json(['error' => 'Signature required'], 403);
            }

            $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($expected, $signature)) {
                Log::warning('GitHub webhook: invalid signature');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // ── 2. Only handle push events ────────────────────────────────────────
        $event = $request->header('X-GitHub-Event', 'push');
        if ($event !== 'push') {
            return response()->json(['message' => "Ignored event: {$event}"]);
        }

        // ── 3. Only update on main/master branch ──────────────────────────────
        $ref    = $request->input('ref', '');
        $branch = $request->input('repository.default_branch', 'main');
        if ($ref !== "refs/heads/{$branch}") {
            return response()->json(['message' => "Ignored ref: {$ref}"]);
        }

        // ── 4. Run git pull ───────────────────────────────────────────────────
        $projectPath = base_path();
        $output = [];
        $code   = 0;

        // git pull
        exec("cd \"{$projectPath}\" && git pull origin {$branch} 2>&1", $output, $code);
        Log::info('GitHub webhook: git pull', ['output' => $output, 'code' => $code]);

        if ($code !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'git pull failed',
                'output'  => implode("\n", $output),
            ], 500);
        }

        // Optional: composer install (safe to run, skips if nothing changed)
        if (file_exists($projectPath . '/composer.lock')) {
            exec("cd \"{$projectPath}\" && composer install --no-dev --optimize-autoloader --no-interaction 2>&1", $cOutput, $cCode);
            Log::info('GitHub webhook: composer install', ['code' => $cCode]);
        }

        // Clear caches
        exec("cd \"{$projectPath}\" && php artisan config:clear 2>&1");
        exec("cd \"{$projectPath}\" && php artisan route:clear 2>&1");
        exec("cd \"{$projectPath}\" && php artisan view:clear 2>&1");

        $pusher       = $request->input('pusher.name', 'unknown');
        $commitMsg    = $request->input('head_commit.message', '');
        $commitId     = substr($request->input('head_commit.id', ''), 0, 7);

        Log::info("GitHub webhook: deployed commit [{$commitId}] by {$pusher}: {$commitMsg}");

        return response()->json([
            'success'    => true,
            'message'    => 'Deployed successfully',
            'commit'     => $commitId,
            'pusher'     => $pusher,
            'git_output' => implode("\n", $output),
        ]);
    }
}
